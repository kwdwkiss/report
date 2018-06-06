<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('name')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('wx')->unique()->nullable();//微信
            $table->string('qq')->unique()->nullable();//qq
            $table->string('ww')->unique()->nullable();//旺旺
            $table->string('jd')->unique()->nullable();//京东
            $table->string('is')->unique()->nullable();//IS
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
