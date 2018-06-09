<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountSearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_search', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');//查询用户id
            $table->integer('type');//查询类型，0-用户手动查询 1-用户api查询
            $table->string('name');//查询账号名
            $table->string('ip');//查询ip地址
            $table->tinyInteger('success');//查询是否成功：0-失败 1-成功
            $table->text('result');
            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_search');
    }
}
