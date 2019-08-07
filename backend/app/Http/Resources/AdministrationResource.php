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
        $administration_option = $this->getOption($this->administration_option);

        return [
            'type' => 'administration',
            'id' => (int)$this->id,
            'attributes' => [
                'report_id' => (int)$this->report_id,
                'administratable_type' => (string)$this->administratable_type,
                'administratable_id' => (int)$this->administratable_id,
                'administration_option' => $administration_option,
                'option_attribute' => (int)$this->option_attribute,
                'reason' => (string)$this->reason,
                'record' => (string)$this->record,
                'is_public' => (bool)$this->is_public,
                'created_at' => (string)$this->created_at,
            ],
            'administrator' => new UserBriefResource($this->whenLoaded('administrator')),
            'administratee' => new UserBriefResource($this->whenLoaded('administratee')),
        ];
    }

    private function getOption($option)
    {
        $administration_option = config('constants.administrations');
        return $administration_option[$option];
    }
}
