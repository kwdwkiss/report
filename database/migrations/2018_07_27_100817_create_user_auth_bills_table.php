<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAuthBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_auth_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('status');//0-可支付认证 1-已支付认证
            $table->integer('amount');//支付积分
            $table->integer('type');
            $table->integer('duration');
            $table->timestamp('pay_at')->nullable();//支付时间
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
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
        Schema::dropIfExists('user_auth_bills');
    }
}
