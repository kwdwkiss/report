<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('name')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('wx')->unique()->nullable();//微信
            $table->string('qq')->unique()->nullable();//qq
            $table->string('ww')->unique()->nullable();//旺旺
            $table->string('jd')->unique()->nullable();//京东
            $table->string('is')->unique()->nullable();//IS
            $table->string('password');
            $table->string('api_key')->unique()->nullable();//api key
            $table->string('api_secret')->unique()->nullable();//api secret
            $table->integer('auth_duration');//认证时长，单位：月
            $table->timestamp('auth_start_at')->nullable();//认证开始时间
            $table->timestamp('auth_end_at')->nullable();//认证结束时间
            $table->string('last_ip');//最近登录ip
            $table->tinyInteger('deny_login');//禁止登陆
            $table->rememberToken();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
