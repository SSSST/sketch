<?php

namespace Tests\Feature;

use Tests\TestCase;
use DB;

class AdministrationTest extends TestCase
{
    /** @test */
    public function administrator_can_manage_items() // 管理员可以对站内item作出管理
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        // 创建一个item
        // 执行管理
        // 验证
    }
}
