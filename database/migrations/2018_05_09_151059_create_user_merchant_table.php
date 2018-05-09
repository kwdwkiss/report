<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_merchant', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique();
            $table->integer('type');//商铺类型 1-天猫 2-企业淘宝店 3-个人淘宝店
            $table->string('name');//店铺名称
            $table->string('goods_type');//商品类型
            $table->string('url');//店铺网址
            $table->string('credit');//店铺信誉
            $table->string('manager');//掌柜
            $table->tinyInteger('user_lock');//用户锁 0-解锁 1-上锁
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
        Schema::dropIfExists('user_merchant');
    }
}
