<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_bill', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date');//查询结算日期
            $table->integer('user_id');//用户
            $table->integer('count');//当前查询次数
            $table->integer('amount');//消耗积分
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
        Schema::dropIfExists('search_bill');
    }
}
