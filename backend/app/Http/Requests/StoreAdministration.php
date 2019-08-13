<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Sosadfun\Traits\ManageTrait;
use App\Models\Administration;
use DB;

class StoreAdministration extends FormRequest
{
    use ManageTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('api')->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'report_id' => 'numeric',
            'administratable_type' => 'required|string',
            'administratable_id' => 'required|numeric',
            'administration_option' => 'required|numeric',
            'option_attribute' => 'numeric',
            'channel_id' => 'numeric',
            'majia' => 'string',
            'reason' => 'required|string',
            'is_public' => 'boolean',
        ];
    }

    public function generate()
    {
        $item = $this->findItem(Request('administratable_id'), Request('administratable_type'));
        $administration_data = $this->only('report_id', 'administratable_type', 'administratable_id', 'administration_option', 'reason', 'is_public', 'option_attribute');
        $administration_data = $this->checkData($administration_data, $item);

        $administration = DB::transaction(function() use($administration_data, $item) {
            $administration = Administration::create($administration_data);
            $this->manageItem($item);
            return $administration;
        });

        return $administration;
    }
}
