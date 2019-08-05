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
            $table->unsignedInteger('reporter_id')->index(); // 举报人id
            $table->morphs('reportable'); // thread|status|post|user|quote
            $table->string('report_kind'); // 举报类型
            $table->json('report_posts')->nullable(); // 举报一个thread下的多个post所需
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
