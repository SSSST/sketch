<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Helpers\StringProcess;
use DB;
use App\Models\Post;
use App\Models\Thread;
use App\Models\Report;
use App\Models\Administration;
use Carbon\Carbon;
use App\Helpers\ConstantObjects;
use App\Http\Requests\StoreAdministration;
use App\Sosadfun\Traits\ManageTrait;

class StoreReport extends FormRequest
{
    use ManageTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $level_limit = config('constants.level_limit');
        $user_level = auth('api')->user()->info()->value('user_level');
        return auth('api')->check() && ($user_level >= $level_limit['can_report'] || auth('api')->user()->isAdmin());
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
            'report_posts' => 'array',
            'review_result' => 'string',
            'administration_option' => 'array',
            'option_attribute' => 'numeric',
            'channel_id' => 'numeric',
            'majia' => 'string',
            'reason' => 'string',
            'is_public' => 'boolean',
        ];
    }

    public function generate()
    {
        $report_data = $this->only('reportable_type', 'reportable_id', 'report_kind');
        $report_data['report_posts'] = json_encode(Request('report_posts'));
        $report_data['reporter_id'] = auth('api')->id();
        $post_data = $this->generatePostData();

        if (!$this->isDuplicateReport($report_data)) {
            $report = DB::transaction(function() use($report_data, $post_data) {
                $post = Post::create($post_data);
                $report_data['post_id'] = $post->id;
                $report = Report::create($report_data);
                return $report;
            });
        } else {
            abort(409);
        }

        return $report;
    }

    public function reviewReport(Report $report)
    {
        $post = POST::find($report->post_id);
        $review_result = Request('review_result');
        $review_post_data = $this->generatePostData('reportRev', $post);

        if (!$this->isDuplicateReview($report, $review_post_data)) {
            DB::transaction(function() use($report, $review_post_data, $review_result) {
                $post = POST::create($review_post_data);
                $report->update([
                    'review_result' => $review_result,
                    'updated_at' => Carbon::now(),
                ]);

                if(!strcmp($review_result, 'approved'))  {$this->manage($report);} // 如果审核通过则创建记录并执行操作
            });
        } else {
            abort(409);
        }

        return $report;
    }

    private function manage($report)
    {
        $administration_options = Request('administration_option');
        $item = $this->findItem($report->reportable_id, $report->reportable_type, $administration_options[0]);
        foreach ($administration_options as $option) {
            $administration_data = $this->generateAdministrationData($report, $item);
            $administration_data['administration_option'] = $option;
            $administration_data = $this->checkData($administration_data, $item);
            $administration = Administration::create($administration_data);
            $this->manageItem($item, $administration_data['administratable_type'], $option);
        }
    }

    private function generateAdministrationData($report, $item)
    {
        $administration_data = [
            'report_id' => $report->id,
            'administratable_type' => $report->reportable_type,
            'administratable_id' => $report->reportable_id,
            'option_attribute' => Request('option_attribute'),
            'reason' => Request('reason'),
            'is_public' => Request('is_public'),
        ];

        return $administration_data;
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

    private function isDuplicateReport($report_data)
    {
        $last_report = Report::where([
            'reportable_type' => $report_data['reportable_type'],
            'reportable_id' => $report_data['reportable_id'],
            'report_kind' => $report_data['report_kind'],
        ])->orderBy('created_at', 'desc')
        ->first();

        return !empty($last_report);
    }

    private function isDuplicateReview($report, $post_data)
    {
        $last_post = Post::where([
            'user_id' => auth('api')->id(),
            'type' => 'reportRev',
        ])->orderBy('created_at', 'desc')
        ->first();

        return (strcmp($report->review_result, Request('review_result')) === 0)
        && (strcmp($last_post->body, $post_data['body']) === 0);
    }
}
