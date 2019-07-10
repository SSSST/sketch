<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('administrator_id')->index(); //执行管理员
            $table->unsignedInteger('report_id')->default(0); // 对应的举报ID
            $table->morphs('administratable'); // user|thread|post|status|quote
            $table->integer('administration_option'); // 管理操作
            $table->integer('option_attribute')->nullable(); // 禁言天数
            $table->string('reason'); // 具体理由
            $table->unsignedInteger('administratee_id'); // 被处理人ID
            $table->boolean('is_public')->default(true); // 是否公开
            $table->dateTime('created_at')->nullable(); // 执行管理时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrations');
    }
}
