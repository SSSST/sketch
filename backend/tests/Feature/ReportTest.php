<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Report;
use DB;

class ReportTest extends TestCase
{
    /** @test */
    public function user_can_report_an_item() // 用户可进行举报操作（普通举报）
    {
        $user = factory('App\Models\User')->create();
        DB::table('user_infos')->where('user_id', $user->id)->update(['user_level' => 3]);
        $this->actingAs($user, 'api');
        $reported_user = factory('App\Models\User')->create();
        $title = 'report title';
        $brief = 'report brief';
        $body = 'report body';

        $response_user = $this->post('/api/report', ['title' => $title, 'brief' => $brief, 'body' => $body, 'reportable_type' => 'user', 'reportable_id' => $reported_user->id, 'report_kind' => 'unfriendly'])
        ->assertStatus(200);
        // ->assertJsonStructure([
        //     'code',
        //     'data' => [
        //         'report' => [
        //             'type',
        //             'id',
        //             'attributes' => [
        //                 'post_id',
        //                 'reportable_type',
        //                 'reportable_id',
        //                 'report_kind',
        //                 'report_type',
        //                 'report_posts',
        //                 'created_at',
        //             ],
        //         ],
        //         'post' => [
        //             'type',
        //             'id',
        //             'attributes' => [
        //                 'post_type',
        //                 'thread_id',
        //                 'title',
        //                 'brief',
        //                 'body',
        //             ],
        //         ],
        //     ],
        // ])
        // ->assertJson([
        //     'code' => 200,
        //     'data' => [
        //         'report' => [
        //             'type' => 'report',
        //             'attributes' => [
        //                 'reportable_type' => 'user',
        //                 'reportable_id' => $reported_user->id,
        //                 'report_kind' => 'unfriendly',
        //                 'report_type' => 'bad-lang',
        //             ],
        //         ],
        //         'post' => [
        //             'type' => 'post',
        //             'attributes' => [
        //                 'post_type' => 'post',
        //                 'title' => $title,
        //                 'brief' => $brief,
        //                 'body' => $body,
        //             ],
        //         ],
        //     ],
        // ]);
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
                'post' => [
                    'type',
                    'id',
                    'attributes' => [
                        'post_type',
                        'thread_id',
                        'title',
                        'brief',
                        'body',
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
                'post' => [
                    'type' => 'post',
                    'attributes' => [
                        'post_type' => 'post',
                        'title' => $title,
                        'brief' => $brief,
                        'body' => $body,
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

    /** @test */
    public function admin_can_review_report() //管理员可以审核举报内容
    {
        $admin = factory('App\Models\User')->create();
        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'api');

        $report = Report::first();
        $title = 'review title';
        $brief = 'review brief';
        $body = 'review body';
        $review_result = 'approved';

        $response = $this->post('/api/report/'.$report->id.'/review', ['title' => $title, 'brief' => $brief, 'body' => $body, 'review_result' => $review_result])
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
                        'review_result',
                        'created_at',
                    ],
                ],
                'post' => [
                    'type',
                    'id',
                    'attributes' => [
                        'post_type',
                        'title',
                        'brief',
                        'body',
                        'reply_id',
                        'reply_brief',
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
                        'reportable_type' => $report->reportable_type,
                        'reportable_id' => $report->reportable_id,
                        'report_kind' => $report->report_kind,
                        'report_type' => $report->report_type,
                        'review_result' => $review_result,
                    ],
                ],
                'post' => [
                    'type' => 'post',
                    'attributes' => [
                        'post_type' => 'reportRev',
                        'title' => $title,
                        'brief' => $brief,
                        'body' => $body,
                        'reply_id' => $report->post_id,
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function user_can_not_review_report() //用户不可以审核举报内容
    {
        $user = factory('App\Models\User')->create();
        $this->actingAs($user, 'api');

        $report = Report::first();
        $title = 'review title';
        $brief = 'review brief';
        $body = 'review body';
        $review_result = 'approved';

        $response = $this->post('/api/report/'.$report->id.'/review', ['title' => $title, 'brief' => $brief, 'body' => $body, 'review_result' => $review_result])
        ->assertStatus(403);
    }

    /** @test */
    public function guest_can_not_review_report() //游客不可以审核举报内容
    {
        $report = Report::first();
        $title = 'review title';
        $brief = 'review brief';
        $body = 'review body';
        $review_result = 'approved';

        $response = $this->post('/api/report/'.$report->id.'/review', ['title' => $title, 'brief' => $brief, 'body' => $body, 'review_result' => $review_result])
        ->assertStatus(401);
    }
}
