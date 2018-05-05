<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechargeBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('bill_no')->unique();
            $table->integer('pay_type');//0-人工 1-支付宝 2-微信
            $table->string('pay_no');
            $table->decimal('money');
            $table->tinyInteger('status');//-1-已关闭 0-待充值 1-已到账
            $table->timestamps();

            $table->unique(['pay_type', 'pay_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharge_bill');
    }
}
