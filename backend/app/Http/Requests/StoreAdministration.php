<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use DB;
use App\Models\Administration;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Status;
use App\Models\Quote;
use Carbon\Carbon;

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
            'is_public' => 'boolean',
        ];
    }

    public function generate()
    {
        $item = $this->findItem(Request('administratable_id'), Request('administratable_type'));
        $administration_data = $this->generateAdministrationData($item);

        $administration = DB::transaction(function() use($administration_data, $item) {
            $administration = Administration::create($administration_data);

            switch ($administration_data['administratable_type']) {
                case 'user':
                    $this->userManagement($item);
                    break;
                case 'thread':
                    $this->threadManagement($item);
                    break;
                case 'post':
                    $this->postManagement($item);
                    break;
                case 'status':
                    $item->delete();
                    break;
                case 'quote':

                    break;
            }
            return $administration;
        });

        return $administration;
    }

    private function userManagement($user) //禁言、解禁、禁止登录、解禁登录
    {
        $administration_type = Request('administration_type');
        switch ($administration_type) {
            case 'no_post':
            case 'no_login':
                $this->blockUser($user, $administration_type);
                break;
            case 'can_post':
                $this->unblockUser($user, 'no_post'); // 第二个参数为role_user表中role应为的值
                break;
            case 'can_login':
                $this->unblockUser($user, 'no_login');
                break;
        }
    }

    private function threadManagement($thread) // 删除、修改channel、匿名、非匿名、锁帖、解锁、边缘、非边缘
    {
        switch (Request('administration_type')) {
            case 'lock':
                $this->changeIsLocked($thread, 0); // 若要执行操作则帖子的is_locked应为0
                break;
            case 'unlock':
                $this->changeIsLocked($thread, 1);
                break;
            case 'public':
                $this->changeIsPublic($thread, 0);
                break;
            case 'no_public':
                $this->changeIsPublic($thread, 1);
                break;
            case 'bianyuan':
                $this->changeIsBianyuan($thread, 0);
                break;
            case 'no_bianyuan':
                $this->changeIsBianyuan($thread, 1);
                break;
            case 'anonymous':
                $this->changeIsAnonymous($thread, 0);
                break;
            case 'no_anonymous':
                $this->changeIsAnonymous($thread, 1);
                break;
            case 'change_channel':
                $this->changeChannel($thread);
                break;
            case 'delete':
                $thread->delete();
                break;
        }
    }

    private function postManagement($post) // 删除、匿名、非匿名、折叠、非折叠、边缘、非边缘
    {
        switch (Request('administration_type')) {
            case 'bianyuan':
                $this->changeIsBianyuan($post, 0);
                break;
            case 'no_bianyuan':
                $this->changeIsBianyuan($post, 1);
                break;
            case 'fold':
                $this->changeIsFolded($post, 0);
                break;
            case 'unfold':
                $this->changeIsFolded($post, 1);
                break;
            case 'delete':
                $post->delete();
                break;
        }
    }

    private function generateAdministrationData($item)
    {
        $administration_data = $this->only('report_id', 'administratable_type', 'administratable_id', 'administration_type', 'options', 'reason', 'is_public');
        $administration_data['administrator_id'] = auth('api')->id();

        $administration_type = ['anonymous', 'no_post', 'no_login', 'change_channel'];
        if(!in_array(Request('administration_type'), $administration_type)) $administration_data['options'] = null;
        if(is_null(Request('is_public'))) $administration_data['is_public'] = true;

        if(Request('administratable_type') == 'user') $administration_data['administratee_id'] = $item->id;
        else $administration_data['administratee_id'] = $item->user_id;

        return $administration_data;
    }

    private function generateRoleUser($user, $type, $days, $hours)
    {
        $role_user_data = [
            'user_id' => $user->id,
            'role' => $type,
            'reason' => Request('reason'),
            'end_at' => Carbon::now()->addDays($days)->addHours($hours),
        ];

        $role_user = DB::table('role_user')->insert($role_user_data);
    }

    private function findItem($item_id, $item_type)
    {
        switch ($item_type) {
            case 'user':
                $item = User::findOrFail($item_id);
                break;
            case 'thread':
                $item = Thread::findOrFail($item_id);
                break;
            case 'post':
                $item = Post::findOrFail($item_id);
                break;
            case 'status':
                $item = Status::findOrFail($item_id);
                break;
            case 'quote':
                $item = Quote::findOrFail($item_id);
                break;
        }

        if(!$item) abort(404);
        return $item;
    }

    private function getOptionsData($data)
    {
        $options = json_decode(Request('options'), true);
        return $options[$data] ?? null;
    }

    private function blockUser($user, $type)
    {
        $days = $this->getOptionsData('days');
        $hours = $this->getOptionsData('hours');
        if(!$days && !$hours) {abort(422);}

        $role_user = DB::table('role_user')->where('user_id', $user->id)->where('role', $type)->first();
        if(!$role_user) {
            $this->generateRoleUser($user, $type, $days, $hours);
        }else {
            DB::table('role_user')->where('user_id', $user->id)->where('role', $type)->update([
                'end_at' => Carbon::parse($role_user->end_at)->addDays($days)->addHours($hours),
            ]);
        }
    }

    private function unblockUser($user, $type)
    {
        $role_user = DB::table('role_user')->where('user_id', $user->id)->where('role', $type)->first();
        if(!$role_user) {abort(412);}

        $now = Carbon::now();
        $end_at = Carbon::parse($role_user->end_at);
        if($now->gt($end_at)) {abort(412);}

        DB::table('role_user')->where('user_id', $user->id)->where('role', $type)->update([
            'end_at' => Carbon::now(),
        ]);
    }

    private function changeChannel($thread)
    {
        $channel_id = $this->getOptionsData('channel_id');
        if(!$channel_id || $channel_id == $thread->channel_id) {abort(412);}
        $thread->channel_id = $channel_id;
        $thread->save();
    }

    private function changeIsLocked($thread, $is_locked)
    {
        if($thread->is_locked != $is_locked) {abort(412);}
        $thread->is_locked = !$thread->is_locked;
        $thread->save();
    }

    private function changeIsPublic($thread, $is_public)
    {
        if($thread->is_public != $is_public) {abort(412);}
        $thread->is_public = !$thread->is_public;
        $thread->save();
    }

    private function changeIsAnonymous($thread, $is_anonymous)
    {
        if($thread->is_anonymous != $is_anonymous) {abort(412);}
        $thread->is_anonymous = !$thread->is_anonymous;
        if($thread->is_anonymous && $majia = $this->getOptionsData('majia')) {
            $thread->majia = $majia;
        }
        $thread->save();
    }

    private function changeIsBianyuan($item, $is_bianyuan)
    {
        if($item->is_bianyuan != $is_bianyuan) {abort(412);}
        $item->is_bianyuan = !$item->is_bianyuan;
        $item->save();
    }

    private function changeIsFolded($post, $is_folded)
    {
        if($post->is_folded != $is_folded) {abort(412);}
        $post->is_folded = !$post->is_folded;
        $post->save();
    }
}
