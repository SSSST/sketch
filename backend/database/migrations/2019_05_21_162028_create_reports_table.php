<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id')->index(); // 举报楼里对应的post_id
            $table->morphs('reportable'); // thread|status|post|user|quote
            $table->string('report_kind'); // 所属举报楼类型
            $table->string('report_type'); // 违规原因细分
            $table->json('report_posts')->nullable(); // 举报一个thread下的多个post所需，包括post_id,clip,reason
            $table->string('review_result')->nullable(); //审核结果
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
