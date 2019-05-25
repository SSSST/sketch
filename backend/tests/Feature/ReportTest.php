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

        $response_user = $this->post('/api/report', ['title' => $title, 'brief' => $brief, 'body' => $body, 'reportable_type' => 'user', 'reportable_id' => $reported_user->id, 'report_kind' => 'unfriendly', 'report_type' => 'bad-lang'])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'report' => [
                    'type',
                    'id',
                    'attributes' => [
                        'post_id',
                        'reportable_type',
                        'reportable_id',
                        'report_kind',
                        'report_type',
                        'report_posts',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'report' => [
                    'type' => 'report',
                    'attributes' => [
                        'reportable_type' => 'user',
                        'reportable_id' => $reported_user->id,
                        'report_kind' => 'unfriendly',
                        'report_type' => 'bad-lang',
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function user_can_report_posts_in_a_thread_at_a_time() // 用户可举报一个帖子下的多条评论
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');
        $thread = factory('App\Models\Thread')->create();
        $post1 = factory('App\Models\Post')->create(['thread_id' => $thread->id]);
        $post2 = factory('App\Models\Post')->create(['thread_id' => $thread->id]);

        $title = 'report title';
        $brief = 'report brief';
        $body = 'report body';
        $report_posts_data = [
            ['post_id' => $post1->id, 'clip' => 'zxcvbnm', 'reason' => 'reason'],
            ['post_id' => $post2->id, 'clip' => 'zxcvbnm', 'reason' => 'reason'],
        ];
        $report_posts = json_encode($report_posts_data);

        $response = $this->post('/api/report', ['title' => $title, 'brief' => $brief, 'body' => $body, 'reportable_type' => 'thread', 'reportable_id' => $thread->id, 'report_kind' => 'unfriendly', 'report_type' => 'bad-lang', 'report_posts' => $report_posts])
        ->assertStatus(200)
        ->assertJsonStructure([
            'code',
            'data' => [
                'report' => [
                    'type',
                    'id',
                    'attributes' => [
                        'post_id',
                        'reportable_type',
                        'reportable_id',
                        'report_kind',
                        'report_type',
                        'report_posts',
                        'created_at',
                    ],
                ],
            ],
        ])
        ->assertJson([
            'code' => 200,
            'data' => [
                'report' => [
                    'type' => 'report',
                    'attributes' => [
                        'reportable_type' => 'thread',
                        'reportable_id' => $thread->id,
                        'report_kind' => 'unfriendly',
                        'report_type' => 'bad-lang',
                        'report_posts' => $report_posts_data,
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function guest_can_not_report_an_item() // 游客不可进行举报操作
    {
        $reported_user = factory('App\Models\User')->create();

        $title = 'report title';
        $brief = 'report brief';
        $body = 'report body';

        $response_user = $this->post('/api/report', ['title' => $title, 'brief' => $brief, 'body' => $body, 'reportable_type' => 'user', 'reportable_id' => $reported_user->id, 'report_kind' => 'unfriendly', 'report_type' => 'bad-lang'])
        ->assertStatus(401);
    }
}
