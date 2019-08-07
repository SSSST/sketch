<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Helpers\StringProcess;
use DB;
use Cache;
use App\Models\Post;
use App\Models\Report;
use App\Models\Thread;
use Carbon\Carbon;
use App\Helpers\ConstantObjects;

class StoreReport extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $level_limit = config('constants.level_limit');
        $user_level = DB::table('user_infos')->where('user_id', auth('api')->id())->value('user_level');
        return auth('api')->check() && $user_level >= $level_limit['can_report'];
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
            'reportable_type' => 'required|string',
            'reportable_id' => 'required|numeric',
            'report_kind' => 'required|string',
            'report_posts' => 'array',
            'review_result' => 'string',
        ];
    }

    public function generate()
    {
        $report_data = $this->only('reportable_type', 'reportable_id', 'report_kind');
        $report_data['report_posts'] = json_encode(Request('report_posts'));
        $report_data['reporter_id'] = auth('api')->id();
        $post_data = $this->generatePostData();

        $data = DB::transaction(function() use($report_data, $post_data) {
            $post = Post::create($post_data);
            $report_data['post_id'] = $post->id;
            $data['report'] = Report::create($report_data);
            $data['post'] = $post;
            return $data;
        });

        return $data;
    }

    public function reviewReport(Report $report)
    {
        if(!auth('api')->user()->isAdmin()) {abort(403);}

        $post = POST::find($report->post_id);
        $review_result = Request('review_result');
        $review_post_data = $this->generatePostData('reportRev', $post);

        $data = DB::transaction(function() use($report, $review_post_data, $review_result) {
            $post = POST::create($review_post_data);
            $report->update([
                'review_result' => $review_result,
            ]);
            $data = ['report' => $report, 'post' => $post];

            return $data;
        });

        return $data;
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
        $kind = Request('report_kind');
        $report_kinds = config('constants.report_kind');
        if(!in_array($kind, array_keys($report_kinds))) {abort(422);}
        $report_thread_type = $report_kinds[$kind];

        $thread = ConstantObjects::system_variable()
            ->where('report_thread_type', $report_thread_type)
            ->where('is_valid', 1)
            ->first();
        return $thread->report_thread_id;
    }
}
