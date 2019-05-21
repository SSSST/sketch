<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->report_posts) {
            $report_posts_data = json_decode($this->report_posts, true);
            while($key = key($report_posts_data)) {
                $report_posts[$key] = $report_posts_data[$key];
                next($report_posts_data);
            }
        } else {
            $report_posts = NULL;
        }
        return [
            'type' => 'report',
            'id' => (int)$this->id,
            'attributes' => [
                'post_id' => (int)$this->post_id,
                'reportable_type' => (string)$this->reportable_type,
                'reportable_id' => (int)$this->reportable_id,
                'report_kind' => (string)$this->report_kind,
                'report_type' => (string)$this->report_type,
                'report_posts' => $report_posts,
                'review_result' => (string)$this->review_result,
            ],
        ];
    }
}
