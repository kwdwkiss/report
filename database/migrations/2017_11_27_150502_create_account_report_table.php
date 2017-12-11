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
            $table->string('account_name');
            $table->integer('account_type');
            $table->string('ip');
            $table->integer('type');
            $table->tinyInteger('display')->default(1);
            $table->string('remark');
            $table->timestamps();

            $table->index(['account_name','account_type']);
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