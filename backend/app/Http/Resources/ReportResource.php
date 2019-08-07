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
            $report_posts = json_decode($this->report_posts);
        } else {
            $report_posts = NULL;
        }

        return [
            'type' => 'report',
            'id' => (int)$this->id,
            'attributes' => [
                'post_id' => (int)$this->post_id,
                'reporter_id' => (int)$this->reporter_id,
                'reportable_type' => (string)$this->reportable_type,
                'reportable_id' => (int)$this->reportable_id,
                'report_kind' => (string)$this->report_kind,
                'report_posts' => $report_posts,
                'review_result' => (string)$this->review_result,
                'created_at' => (string)$this->created_at,
            ],
        ];
    }
}
