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
            $table->integer('cash_amount');//可提现额度，1元=100
            $table->integer('deposit');//保证金，单位元
            $table->string('name');
            $table->integer('age');
            $table->tinyInteger('gender');//0-未知 1-男 2-女
            $table->string('occupation');
            $table->string('province');
            $table->string('city');
            $table->string('alipay');//支付宝账号
            $table->string('alipay_img');//支付宝截图
            $table->string('identity_code');//身份证号
            $table->string('identity_front_img');//身份证正面照
            $table->string('identity_back_img');//身份证背面照
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
