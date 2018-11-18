<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('account_name');
            $table->integer('account_type');
            $table->string('ip');
            $table->integer('type');
            $table->tinyInteger('display')->default(1);
            $table->text('description')->nullable();//描述
            $table->string('remark');
            $table->timestamps();

            $table->index(['account_name', 'account_type']);
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_report');
    }
}
