<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReportTest extends TestCase
{
    /** @test */
    public function user_can_report_an_item() // 用户可进行举报操作
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        $reported_user = factory('App\Models\User')->create();

        $title = 'report title';
        $brief = 'report brief';
        $body = 'report body';

        $response_user = $this->post('/api/report/store', ['title' => $title, 'brief' => $brief, 'body' => 'body', 'reportable_type' => 'user', 'reportable_id' => $reported_user->id, 'report_kind' => 'unfriendly', 'report_type' => 'bad-lang'])
        ->assertStatus(200);
    }
}
