<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_type');//0-游客 1-用户 2-管理员
            $table->string('name')->unique();
            $table->integer('type');//0-图片
            $table->tinyInteger('use');//0-未使用 1-使用
            $table->string('url');//访问url
            $table->integer('storage');//0-本地 1-阿里云
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
        Schema::dropIfExists('attachment');
    }
}
