<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBizRechargeBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biz_recharge_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bill_no')->unique();
            $table->integer('pay_type');//0-支付宝 1-微信
            $table->string('pay_no');
            $table->integer('money');//最低充值1元
            $table->tinyInteger('status');//-1-已关闭 0-待充值 1-已到账
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
        Schema::dropIfExists('biz_recharge_bill');
    }
}
