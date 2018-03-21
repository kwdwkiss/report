<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile');
            $table->string('code');
            $table->integer('status');//-1发送失败，0-未验证，1-已验证
            $table->string('ip');//防止暴力刷短信码
            $table->timestamp('expired_at');
            $table->text('result');
            $table->timestamps();

            $table->index(['ip', 'created_at']);
            $table->index(['mobile', 'expired_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
