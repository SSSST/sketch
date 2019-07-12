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
                    $this->statusManagement($item);
                    break;
                case 'quote':
                    $this->quoteManagement($item);
                    break;
            }
            return $administration;
        });

        return $administration;
    }

    private function userManagement($user) //禁言、解禁、禁止登录、解禁登录
    {
        $administration_option = Request('administration_option');
        switch ($administration_option) {
            case 16:
            case 18:
                $this->blockUser($user, $administration_option);
                break;
            case 17:
                $this->unblockUser($user, 'no_login');
                break;
            case 19:
                $this->unblockUser($user, 'no_post'); // 第二个参数为role_user表中role应为的值
                break;
            default:
                abort(422);
        }
    }

    private function threadManagement($thread) // 删除、修改channel、匿名、非匿名、锁帖、解锁、边缘、非边缘
    {
        switch (Request('administration_option')) {
            case 1:
                $this->changeIsLocked($thread, 0); // 若要执行操作则帖子的is_locked应为0
                break;
            case 2:
                $this->changeIsLocked($thread, 1);
                break;
            case 3:
                $this->changeIsPublic($thread, 1);
                break;
            case 4:
                $this->changeIsPublic($thread, 0);
                break;
            case 5:
                $thread->delete();
                break;
            case 6:
                $thread->restore();
                break;
            case 9:
                $this->changeChannel($thread);
                break;
            case 12:
                $this->anonymous($thread);
                break;
            case 13:
                $this->noAnonymous($thread);
                break;
            case 14:
                $this->changeIsBianyuan($thread, 1);
                break;
            case 15:
                $this->changeIsBianyuan($thread, 0);
                break;
            default:
                abort(422);
        }
    }

    private function postManagement($post) // 删除、匿名、非匿名、折叠、非折叠、边缘、非边缘
    {
        switch (Request('administration_option')) {
            case 7:
                $post->delete();
                break;
            case 8:
                $post->restore();
                break;
            case 10:
                $this->changeIsFolded($post, 1);
                break;
            case 11:
                $this->changeIsFolded($post, 0);
                break;
            case 12:
                $this->anonymous($post);
                break;
            case 13:
                $this->noAnonymous($post);
                break;
            case 14:
                $this->changeIsBianyuan($post, 1);
                break;
            case 15:
                $this->changeIsBianyuan($post, 0);
                break;
            default:
                abort(422);
        }
    }

    private function quoteManagement($quote)
    {
        switch (Request('administration_option')) {
            case 12:
                $this->anonymous($quote);
                break;
            case 13:
                $this->noAnonymous($quote);
                break;
            default:
                abort(422);
        }
    }

    private function statusManagement($status)
    {
        switch (Request('administration_option')) {
            case 20:
                $status->delete();
                break;
            case 21:
                $status->restore();
                break;
            default:
                abort(422);
        }
    }

    private function generateAdministrationData($item)
    {
        $administration_data = $this->only('report_id', 'administratable_type', 'administratable_id', 'administration_option', 'reason', 'is_public', 'option_attribute');
        $administration_data['administrator_id'] = auth('api')->id();

        if(!in_array(Request('administration_option'), [16, 18])) $administration_data['option_attribute'] = null;
        if(is_null(Request('is_public'))) $administration_data['is_public'] = true;

        if(Request('administratable_type') == 'user') $administration_data['administratee_id'] = $item->id;
        else $administration_data['administratee_id'] = $item->user_id;

        return $administration_data;
    }

    private function generateRoleUser($user, $type, $hours)
    {
        $role_user_data = [
            'user_id' => $user->id,
            'role' => $type,
            'reason' => Request('reason'),
            'created_at' => Carbon::now(),
            'end_at' => Carbon::now()->addHours($hours),
        ];

        $role_user = DB::table('role_user')->insert($role_user_data);
    }

    private function findItem($item_id, $item_type)
    {
        $is_deleted = false;
        switch ($item_type) {
            case 'user':
                $item = User::withTrashed()->find($item_id);
                break;
            case 'thread':
                $item = Thread::withTrashed()->find($item_id);
                break;
            case 'post':
                $item = Post::withTrashed()->find($item_id);
                break;
            case 'status':
                $item = Status::withTrashed()->find($item_id);
                break;
            case 'quote':
                $item = Quote::withTrashed()->find($item_id);
                break;
        }

        if(!$item || $item->deleted_at) $is_deleted = true;
        $restore_options = [6, 8, 21];
        if($is_deleted && !in_array(Request('administration_option'), $restore_options)) {abort(404);} // 不属于恢复操作且找不到数据时
        if(!$is_deleted && in_array(Request('administration_option'), $restore_options)) {abort(412);} // 属于恢复操作但数据并未被删除时
        return $item;
    }

    private function blockUser($user, $option)
    {
        $hours = Request('option_attribute');
        if(!$hours) {abort(412);}
        if($option == 16) $type = 'no_login';
        elseif ($option == 18) $type = 'no_post';

        $role_user_builder = DB::table('role_user')->where('user_id', $user->id)->where('role', $type);
        $role_user = $role_user_builder->first();
        if(!$role_user) {
            $this->generateRoleUser($user, $type, $hours);
        }else {
            $role_user_builder->update([
                'is_valid' => 1,
                'end_at' => Carbon::parse($role_user->end_at)->addHours($hours),
            ]);
        }
    }

    private function unblockUser($user, $type)
    {
        $role_user_builder = DB::table('role_user')->where('user_id', $user->id)->where('role', $type);
        $role_user = $role_user_builder->first();
        if(!$role_user || !$role_user->is_valid) {abort(412);}

        $role_user_builder->update([
            'is_valid' => 0,
            'end_at' => Carbon::now(),
        ]);
    }

    private function changeChannel($thread)
    {
        $channel_id = Request('channel_id');
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

    private function noAnonymous($item)
    {
        if($item->is_anonymous != 1) {abort(412);} // 如果原来就未匿名还要执行取匿操作则报错

        $item->is_anonymous = 0;
        $item->save();
    }

    private function anonymous($item)
    {
        $majia = Request('majia');
        if((!$majia && !$item->majia) || !strcmp($item->majia, $majia)) {abort(412);}
        if($majia) $item->majia = $majia;

        if($item->is_anonymous == 0) {
            $item->is_anonymous = 1;
        }
        $item->save();
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
