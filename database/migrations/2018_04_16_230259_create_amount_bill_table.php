<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmountBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amount_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('bill_no')->unique();
            $table->integer('status')->default(1);
            $table->tinyInteger('type');//0-收入 1-支出
            $table->integer('amount');//积分
            $table->integer('user_amount');//用户积分
            $table->integer('biz_type');//业务类型 0-系统发放 1-充值 2-充值提成 101-查询
            $table->integer('biz_id');//业务id
            $table->text('description');
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
        Schema::dropIfExists('amount_bill');
    }
}
