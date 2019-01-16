<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Thread;
Use App\Models\User;


class UpdateThreadTest extends TestCase
{


  /** @test */
  public function an_authorised_user_can_update_thread()
  {

    // channle_id = 1,原创
    // channel_id =2, 同人，tags   63,64,
    // 篇幅 : 75,76,77  中篇 短篇  之类
    // 性向: 82，83，84,BG,BL
    // 不能多于3个：66,67,68,69
    // manage: 41,45
    //thread的channel_id 只能是1或者2,取其他数值没有意义
     $thread = factory(Thread::class)->create([
           'channel_id' => '1',
       ]);

    //更新thread的人必须是创建者，如果用其他人会更新失败
    $user = User::find($thread->user_id);
    $this->be($user);

    //测试数据1  2，同人 细分为动漫 75中篇  63 64全职高手同人   以下性向82 83 84 85 86
    // $request = $this->actingAs($user,'api')
    // ->put('api/thread/'.$thread->id,
    // ['is_bianyuan' => true,
    // 'channel' => 2,
    // 'tags' =>[2,75,63,64,83]
    //  ]);



    //测试数据2  1,原创 77长篇 80 完结  性向85 84
    $request = $this->actingAs($user,'api')
      ->put('api/thread/'.$thread->id,
      ['is_bianyuan' => false,
      'title' => 'test1234589',
      'brief' => 'test12345',
      'body' => 'test12345abcdefg',
      'majia' => 'test13234',
      'tags' => '75,80,84'
    ]);

    $response = $request->send();

    $this->assertEquals(200, $response->getStatusCode());



  }
}
