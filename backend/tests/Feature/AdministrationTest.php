<?php

namespace Tests\Feature;

use Tests\TestCase;
use DB;

class AdministrationTest extends TestCase
{
    private function create_an_item()
    {
        $user = factory('App\Models\User')->create();
        $num = mt_rand(1,2);

        switch ($num) {
            case 1:
                $item = ['item_model' => factory('App\Models\Thread')->create(), 'item_type' => 'thread'];
                break;
            case 2:
                $item = ['item_model' => factory('App\Models\Post')->create(), 'item_type' => 'post'];
                break;
        }

        return $item;
    }

    /** @test */
    public function administrator_can_delete_items() // 管理员可以删除站内item
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');

        $item_data = $this->create_an_item();
        $item = $item_data['item_model'];
        $item_type = $item_data['item_type'];
        $reason = 'delete an item';

        $response = $this->post('/api/manage', ['administratable_type' => $item_type, 'administratable_id' => $item->id, 'administration_type' => 'delete', 'reason' => $reason, 'administratee_id' => $item->user_id])
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
                        'administratable_type' => $item_type,
                        'administratable_id' => $item->id,
                        'administration_type' => 'delete',
                        'reason' => $reason,
                        'administratee_id' => $item->user_id,
                    ],
                ],
            ],
        ]);
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

        $item_data = $this->create_an_item();
        $item = $item_data['item_model'];
        $item_type = $item_data['item_type'];
        $reason = 'delete an item';

        $success_response = $this->post('/api/manage', ['administratable_type' => $item_type, 'administratable_id' => $item->id, 'administration_type' => 'delete', 'reason' => $reason, 'administratee_id' => $item->user_id])
        ->assertStatus(200);

        $failed_response = $this->post('/api/manage', ['administratable_type' => $item_type, 'administratable_id' => $item->id, 'administration_type' => 'delete', 'reason' => $reason, 'administratee_id' => $item->user_id])
        ->assertStatus(404);
    }

    /** @test */
    public function user_can_not_delete_items() // 用户不能删除item
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $item_data = $this->create_an_item();
        $item = $item_data['item_model'];
        $item_type = $item_data['item_type'];
        $reason = 'delete an item';

        $response = $this->post('/api/manage', ['administratable_type' => $item_type, 'administratable_id' => $item->id, 'administration_type' => 'delete', 'reason' => $reason, 'administratee_id' => $item->user_id])
        ->assertStatus(403);
    }

    /** @test */
    public function guest_can_not_delete_items() // 游客不能删除item
    {
        $item_data = $this->create_an_item();
        $item = $item_data['item_model'];
        $item_type = $item_data['item_type'];
        $reason = 'delete an item';

        $response = $this->post('/api/manage', ['administratable_type' => $item_type, 'administratable_id' => $item->id, 'administration_type' => 'delete', 'reason' => $reason, 'administratee_id' => $item->user_id])
        ->assertStatus(401);
    }
}
