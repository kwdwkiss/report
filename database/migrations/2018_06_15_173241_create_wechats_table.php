<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');//类型 0-未设置 1-未认证订阅号 2-认证订阅号 3-未认证服务号 4-认证服务号
            $table->string('wechat_id');//原始ID
            $table->string('app_id');//appid
            $table->string('app_secret');//app_secret
            $table->string('token');
            $table->string('aes_key');
            $table->text('menu');//menu
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
        Schema::dropIfExists('wechats');
    }
}
