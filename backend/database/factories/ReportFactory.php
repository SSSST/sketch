<?php

use Faker\Generator as Faker;
use App\Helpers\ConstantObjects;

$factory->define(App\Models\Report::class, function (Faker $faker) {
    $system_variables = ConstantObjects::system_variable()
        ->where('report_thread_type', 1)
        ->where('is_valid', 1)
        ->first();
    $post = factory('App\Models\Post')->create(['thread_id' => $system_variables->report_thread_id]);

    return [
        'report_kind' => 'bad-lang',
        'reportable_id' => function() {
            return factory('App\Models\Thread')->create()->id;
        },
        'reportable_type' => 'thread',
        'post_id' => $post->id,
        'reporter_id' => $post->user_id,
    ];
});
