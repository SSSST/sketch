<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\StringProcess;
use DB;
use App\Models\Post;
use App\Models\Report;

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
            'reportable_type' => 'required|string',
            'reportable_id' => 'required|numeric',
            'report_kind' => 'required|string',
            'report_type' => 'required|string',
            'report_posts' => 'json',
        ];
    }

    public function generate()
    {
        $report_data = $this->only('reportable_type', 'reportable_id', 'report_kind', 'report_type', 'report_posts');
        $post_data = $thie->generatePostData();

        $report = DB::transaction(function() use($report_data, $post_data) {
            $post = Post::create($post_data);
            $report_data['post_id'] = $post_id;
            $report = Report::create($report_data);
            return $report;
        });

        return $report;
    }

    private function generatePostData()
    {
        $post_data = $this->only('title', 'body');
        if($this->isDuplicatePost($post_data)){abort(409);}
        $post_data['user_id'] = auth('api')->id();
        $post_data['thread_id'] =
        $post_data['type'] = 'post';
        $post_data['brief'] = $this->brief ?: StringProcess::trimtext($this->body, config('constants.brief_len'));
        $post_data['creation_ip'] = request()->getClientIp();
        return $post_data;
    }

    private function isDuplicatePost($post_data)
    {
        $last_post = Post::where('user_id', auth('api')->id())
        ->orderBy('created_at', 'desc')
        ->first();
        return (!empty($last_post)) && (strcmp($last_post->body, $post_data['body']) === 0);
    }
}
