<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Helpers\StringProcess;
use DB;
use Cache;
use App\Models\Post;
use App\Models\Report;
use App\Models\Thread;

class StoreReport extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|max:50',
            'brief' => 'string|max:50',
            'body' => 'string|max:20000',
            'reportable_type' => 'string',
            'reportable_id' => 'numeric',
            'report_kind' => 'string',
            'report_type' => 'string',
            'report_posts' => 'json',
            'review_result' => 'string',
        ];
    }

    public function generate()
    {
        $report_data = $this->only('reportable_type', 'reportable_id', 'report_kind', 'report_type', 'report_posts');
        $post_data = $this->generatePostData();

        $report = DB::transaction(function() use($report_data, $post_data) {
            $post = Post::create($post_data);
            $report_data['post_id'] = $post->id;
            $report = Report::create($report_data);
            return $report;
        });

        return $report;
    }

    public function reviewReport(Report $report)
    {
        if(!auth('api')->user()->isAdmin()) {abort(403);}

        $post = POST::find($report->post_id);
        $review_result = Request('review_result');
        $review_post_data = $this->generatePostData('reportRev', $post);

        $update_report = DB::transaction(function() use($report, $review_post_data, $review_result) {
            $post = POST::create($review_post_data);
            $report->update([
                'review_result' => $review_result,
            ]);
            return $report;
        });

        return $update_report;
    }

    private function generatePostData($type = null, $post = null)
    {
        $post_data = $this->only('title', 'body');
        $post_data['user_id'] = auth('api')->id();
        if($post == null) { // report
            $post_data['thread_id'] = $this->getThreadId();
            $post_data['type'] = 'post';
        }else{ // reportRev
            $post_data['thread_id'] = $post->thread_id;
            $post_data['type'] = $type;
            $post_data['reply_id'] = $post->id;
            $post_data['reply_brief'] = $post->brief;
        }
        $post_data['brief'] = $this->brief ?: StringProcess::trimtext($this->body, config('constants.brief_len'));
        $post_data['creation_ip'] = request()->getClientIp();
        return $post_data;
    }

    private function getThreadId()
    {
        $report_kind = Request('report_kind');
        if(Cache::has($report_kind)) return Cache::get($report_kind) ;

        $thread = Thread::create([
            'user_id' => auth('api')->id(),
        ]);
        Cache::forever($report_kind, $thread->id);
        return $thread->id;
    }
}
