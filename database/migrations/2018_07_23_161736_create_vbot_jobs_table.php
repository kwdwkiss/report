<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVbotJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vbot_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            //0-新任务 1-已获取UUID 2-已登录
            //-1任务完成 -2登录超时
            $table->integer('status');
            $table->text('context');
            $table->text('data');
            $table->text('exception');
            $table->string('qrcode_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vbot_jobs');
    }
}
