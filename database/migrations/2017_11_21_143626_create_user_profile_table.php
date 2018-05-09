<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique();
            $table->integer('amount');//账户余额，1元=100
            $table->string('name');
            $table->integer('age');
            $table->tinyInteger('gender');//0-未知 1-男 2-女
            $table->string('occupation');
            $table->string('province');
            $table->string('city');
            $table->string('alipay');//支付宝账号
            $table->string('inviter');//邀请人
            $table->string('remark');
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
        Schema::dropIfExists('user_profile');
    }
}
