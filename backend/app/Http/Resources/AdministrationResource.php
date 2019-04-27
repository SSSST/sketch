<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdministrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'administration',
            'id' => (int)$this->id,
            'attributes' => [
                'administrator_id' => (int)$this->administrator_id,
                'report_id' => (int)$this->report_id,
                'administratable_type' => (string)$this->administratable_type,
                'administratable_id' => (int)$this->administratable_id,
                'administration_type' => (string)$this->administration_type,
                'options' => ,
                'reason' => (string)$this->reason,
                'administratee_id' => (int)$this->administratee_id,
                'is_public' => (bool)$this->is_public,
                'created_at' => (string)$this->created_at,
            ],
            'administrator' => new UserBriefResource($this->whenLoaded('administrator')),
        ];
    }
}
