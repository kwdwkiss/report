<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name');
            $table->integer('type');
            $table->integer('status');
            $table->string('remark');
            //info
            $table->string('address');
            //report
            $table->integer('report_count');
            //auth
            $table->timestamp('auth_at')->nullable();
            $table->integer('auth_cash');

            $table->timestamps();

            $table->unique(['name', 'type']);
            $table->index('name');
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
        Schema::dropIfExists('account');
    }
}
