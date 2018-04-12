<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAmountBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_amount_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('bill_no')->unique();
            $table->tinyInteger('type');//0-收入 1-支出
            $table->integer('amount');
            $table->integer('biz_id');//业务账单id
            $table->integer('biz_type');//0-充值
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
        Schema::dropIfExists('user_amount_bill');
    }
}
