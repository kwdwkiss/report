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
            $table->integer('status');//0-待运行 1-运行中 -1-完成 -2-异常退出
            $table->string('qrcode');
            $table->integer('login_status');//0-获取uuid 1-等待扫码 2-等待登录 3-登录完成
            $table->text('context');
            $table->text('friends');//好友
            $table->text('groups');//群组
            $table->text('members');//群成员
            $table->text('officials');//公众号
            $table->text('specials');//特殊账号
            $table->text('myself');//自己

            $table->text('data');
            $table->text('exception');
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
