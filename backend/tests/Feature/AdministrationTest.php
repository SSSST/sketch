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
                        'administratee_id' => $thread->user_id,
                    ],
                ],
            ],
        ]);
        $this->assertSoftDeleted('threads', ['id' => $thread->id]);
    }

    /** @test */
    public function admin_can_not_delete_nonexistent_item() // 管理员不能删除不存在的item（如已被删除的item）
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
        ->assertStatus(200);
        $failed_response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200);
        $failed_response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(200);
        $failed_response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(404);
    }

    /** @test */
    public function user_can_not_delete_items() // 用户不能删除item
    {
        $acting_user = factory('App\Models\User')->create();
        $this->actingAs($acting_user, 'api');
        $user = factory('App\Models\User')->create();
        $reason = 'delete an item';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(403);
    }

    /** @test */
    public function guest_can_not_delete_items() // 游客不能删除item
    {
        $user = factory('App\Models\User')->create();
        $reason = 'delete an item';

        $status = factory('App\Models\Status')->create(['user_id' => $user->id]);
        $response_status = $this->post('/api/manage', ['administratable_type' => 'status', 'administratable_id' => $status->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);

        $post = factory('App\Models\Post')->create(['user_id' => $user->id]);
        $response_post = $this->post('/api/manage', ['administratable_type' => 'post', 'administratable_id' => $post->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);

        $thread = factory('App\Models\Thread')->create(['user_id' => $user->id]);
        $response_thread = $this->post('/api/manage', ['administratable_type' => 'thread', 'administratable_id' => $thread->id, 'administration_type' => 'delete', 'reason' => $reason])
        ->assertStatus(401);
    }
}
