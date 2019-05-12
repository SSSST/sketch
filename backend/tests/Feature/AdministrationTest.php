<?php

namespace Tests\Feature;

use Tests\TestCase;
use DB;
use App\Models\Thread;
use App\Models\Status;

class AdministrationTest extends TestCase
{
    /** @test */
    public function administrator_can_delete_items() // 管理员可以删除站内item
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'delete an item';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'status',
                        'administratable_id' => $status->id,
                        'administration_type' => 'delete',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $status->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertSoftDeleted('statuses', ['id' => $status->id]);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'post',
                        'administratable_id' => $post->id,
                        'administration_type' => 'delete',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $post->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'delete',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertSoftDeleted('threads', ['id' => $thread->id]);
    }

    /** @test */
    public function admin_can_change_is_locked() // 管理员可以锁/解锁帖子
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'lock or unlock a thread';

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_lock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'lock', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'lock',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $thread->fresh()->is_locked);

        $response_unlock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'unlock', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'unlock',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $thread->fresh()->is_locked);
    }

    /** @test */
    public function admin_can_change_is_public() // 管理员可以将帖子转为私密/公开
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'change is_public';

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_no_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_public', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'no_public',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $thread->fresh()->is_public);

        $response_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'public', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'public',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $thread->fresh()->is_public);
    }

    /** @test */
    public function admin_can_change_is_bianyuan() // 管理员可以将帖子/评论转为边缘/非边缘
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'change is_bianyuan';

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'bianyuan',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $thread->fresh()->is_bianyuan);

        $response_no_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'no_bianyuan',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $thread->fresh()->is_bianyuan);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'post',
                        'administratable_id' => $post->id,
                        'administration_type' => 'bianyuan',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $post->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $post->fresh()->is_bianyuan);

        $response_no_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'post',
                        'administratable_id' => $post->id,
                        'administration_type' => 'no_bianyuan',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $post->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $post->fresh()->is_bianyuan);
    }

    /** @test */
    public function admin_can_change_is_is_anonymous() // 管理员可以将帖子转为匿名/解除匿名
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'change is_anonymous';
        $majia = 'anonymous';
        $options_data = ['majia' => $majia];
        $options = json_encode($options_data);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_anonymous = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'anonymous', 'reason' => $reason, 'options' => $options])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'anonymous',
                        'reason' => $reason,
                        'options' => [
                            'majia' => $majia,
                        ],
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $thread->fresh()->is_anonymous);
        $this->assertEquals($majia, $thread->fresh()->majia);

        $response_no_anonymous = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_anonymous', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'thread',
                        'administratable_id' => $thread->id,
                        'administration_type' => 'no_anonymous',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $thread->fresh()->is_anonymous);
    }

    /** @test */
    public function admin_can_change_is_folded() // 管理员可以折叠/解折评论
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'change is_folded';

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_fold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'fold', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'post',
                        'administratable_id' => $post->id,
                        'administration_type' => 'fold',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $post->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(1, $post->fresh()->is_folded);

        $response_unfold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'unfold', 'reason' => $reason])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'administration' => [
                    'type',
                    'id',
                    'attributes' => [
                        'administrator_id',
                        'report_id',
                        'administratable_type',
                        'administratable_id',
                        'administration_type',
                        'reason',
                        'options',
                        'administratee_id',
                        'is_public',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'administration' => [
                    'type' => 'administration',
                    'attributes' => [
                        'administrator_id' => $admin->id,
                        'administratable_type' => 'post',
                        'administratable_id' => $post->id,
                        'administration_type' => 'unfold',
                        'reason' => $reason,
                        'options' => NULL,
                        'administratee_id' => $post->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertEquals(0, $post->fresh()->is_folded);
    }

    /** @test */
    public function admin_can_not_make_a_wrong_management_to_an_item() // 管理员不能对帖子进行错误操作
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'wrong management';

        $locked_thread = factory('App\Models\Thread')->create(['user_id' => $user->id, 'is_locked' => 1]);
        $response_lock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $locked_thread->id, 'administration_type' => 'lock', 'reason' => $reason])
        ->assertStatus(409);

        $unlocked_thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_unlock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $unlocked_thread->id, 'administration_type' => 'unlock', 'reason' => $reason])
        ->assertStatus(409);

        $private_thread = factory('App\Models\Thread')->create(['user_id' => $user->id, 'is_public' => 0]);
        $response_private = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $private_thread->id, 'administration_type' => 'no_public', 'reason' => $reason])
        ->assertStatus(409);

        $public_thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $public_thread->id, 'administration_type' => 'public', 'reason' => $reason])
        ->assertStatus(409);

        $bianyuan_thread = factory('App\Models\Thread')->create(['user_id' => $user->id, 'is_bianyuan' => 1]);
        $response_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $bianyuan_thread->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(409);

        $no_bianyuan_thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_no_bianyaun_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $no_bianyuan_thread->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(409);

        $majia = 'anonymous';
        $options_data = ['majia' => $majia];
        $options = json_encode($options_data);
        $anonymous_thread = factory('App\Models\Thread')->create(['user_id' => $user->id, 'is_anonymous' => 1]);
        $response_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $anonymous_thread->id, 'administration_type' => 'anonymous', 'reason' => $reason, 'options' => $options])
        ->assertStatus(409);

        $no_anonymous_thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_no_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $no_anonymous_thread->id, 'administration_type' => 'no_anonymous', 'reason' => $reason])
        ->assertStatus(409);

        $bianyuan_post = factory('App\Models\Post')->create(['user_id' => $user->id, 'is_bianyuan' => 1]);
        $response_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $bianyuan_post->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(409);

        $no_bianyuan_post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_no_bianyaun_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $no_bianyuan_post->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(409);

        $folded_post = factory('App\Models\Post')->create(['user_id' => $user->id, 'is_folded' => 1]);
        $response_fold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $folded_post->id, 'administration_type' => 'fold', 'reason' => $reason])
        ->assertStatus(409);

        $unfolded_post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_unfold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $unfolded_post->id, 'administration_type' => 'unfold', 'reason' => $reason])
        ->assertStatus(409);
    }

    /** @test */
    public function admin_can_not_manage_nonexistent_item() // 管理员不能对删除的item操作（如已被删除的item）
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'can not manage deleted item';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200);
        $failed_response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200);
        $failed_response_delete_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_no_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_fold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'fold', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_unfold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'unfold', 'reason' => $reason])
        ->assertStatus(404);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200);
        $failed_response_delete_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_lock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'lock', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_unlock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'unlock', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_no_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_public', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'public', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_no_bianyaun_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_no_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_anonymous', 'reason' => $reason])
        ->assertStatus(404);
        $failed_response_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'anonymous', 'reason' => $reason])
        ->assertStatus(404);
    }

    /** @test */
    public function user_can_not_manage_items() // 用户不能对item操作
    {
        $acting_user = factory('App\Models\User')->create();
        $this->actingAs($acting_user, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'can not manage';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_delete_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_delete_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);
        $response_no_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(403);
        $response_bianyuan_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(403);
        $response_unfold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'unfold', 'reason' => $reason])
        ->assertStatus(403);
        $response_fold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'fold', 'reason' => $reason])
        ->assertStatus(403);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_delete_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);
        $response_lock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'lock', 'reason' => $reason])
        ->assertStatus(403);
        $response_unlock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'unlock', 'reason' => $reason])
        ->assertStatus(403);
        $response_no_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_public', 'reason' => $reason])
        ->assertStatus(403);
        $response_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'public', 'reason' => $reason])
        ->assertStatus(403);
        $response_no_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(403);
        $response_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(403);
        $response_no_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_anonymous', 'reason' => $reason])
        ->assertStatus(403);
        $response_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'anonymous', 'reason' => $reason])
        ->assertStatus(403);
    }

    /** @test */
    public function guest_can_not_manage_items() // 游客不能对item操作
    {
        $user = factory('App\Models\User')->create();
        $reason = 'can not manage';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_delete_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_delete_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);
        $response_no_bianyaun_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(401);
        $response_bianyaun_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(401);
        $response_unfold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'unfold', 'reason' => $reason])
        ->assertStatus(401);
        $response_fold = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'fold', 'reason' => $reason])
        ->assertStatus(401);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_delete_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);
        $response_lock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'lock', 'reason' => $reason])
        ->assertStatus(401);
        $response_unlock = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'unlock', 'reason' => $reason])
        ->assertStatus(401);
        $response_no_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_public', 'reason' => $reason])
        ->assertStatus(401);
        $response_public = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'public', 'reason' => $reason])
        ->assertStatus(401);
        $response_no_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_bianyuan', 'reason' => $reason])
        ->assertStatus(401);
        $response_bianyuan_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'bianyuan', 'reason' => $reason])
        ->assertStatus(401);
        $response_no_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'no_anonymous', 'reason' => $reason])
        ->assertStatus(401);
        $response_anonymous_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'anonymous', 'reason' => $reason])
        ->assertStatus(401);
    }
}
