<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use DB;
use App\Models\Administration;

class StoreAdministration extends FormRequest
{
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
            'administration_type' => 'required|string',
            'options' => 'json',
            'reason' => 'required|string',
            'administratee_id' => 'required|numeric',
            'is_public' => 'boolean',
        ];
    }

    public function generate()
    {
        // 获取request内容
        $administration_data = $this->only('report_id', 'administratable_type', 'administratable_id', 'administration_type', 'options', 'reason', 'administratee_id', 'is_public');
        $administration_data['administrator_id'] = auth('api')->id();

        // 创建一条管理记录
        $administration = DB::transaction(function () use($administration_data) {
            $administration = Administration::create($administration_data);

            // 作出管理
            switch ($administration_data['administratable_type']) {
                case 'user':
                    $this->userManagement();
                    break;
                case 'thread':
                    $this->threadManagement();
                    break;
                case 'post':
                    $this->postManagement();
                    break;
                case 'status':
                    $this->statusManagement();
                    break;
                case 'quote':
                    $this->quoteManagement();
                    break;
            }
            return $administration;
        });

        return $administration;
    }

    private function threadManagement()
    {
        $thread = Request('administratable_id');

        switch (Request('administration_type')) {
            case 'lock':
            case 'unlock':
                $thread->is_locked = !$thread->is_locked;
                $thread->save();
                break;
            case 'public':
            case 'no_public':
                $thread->is_public = !$thread->is_public;
                $thread->save();
                break;
            case 'bianyuan':
            case 'no_bianyuan':
                $thread->is_bianyuan = !$thread->bianyuan;
                $thread->save();
                break;
            case 'change_channel':

                break;
            case 'delete':
                $thread->delete();
                break;
        }
    }

    private function postManagement()
    {
        $post = Request('administratable_id');

        switch (Request('administration_type')) {
            case 'fold':
            case 'unfold':
                $post->is_folded = !$post->is_folded;
                $post->save();
                break;
            case 'anonymous':

                break;
            case 'delete':
                $post->delete();
                break;
        }
    }

    private function statusManagement()
    {

    }

    private function quoteManagement()
    {

    }
}
