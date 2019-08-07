<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_variables', function (Blueprint $table) {
            $table->integer('latest_public_notice_id')->default(0);//最新系统消息是哪个
            $table->unsignedInteger('report_thread_id'); // 举报楼id
            $table->tinyInteger('report_thread_type'); // 举报楼类型
            $table->boolean('is_valid')->default(0);
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_variables');
    }
}
