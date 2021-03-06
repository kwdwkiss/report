<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date');
            $table->integer('type');//1-天报 2-月报
            $table->integer('user_register');//用户注册数
            $table->integer('user_register_inviter');//被邀请注册数
            $table->integer('account_report');//账号举报
            $table->integer('account_search');//账号查询
            $table->integer('account_search_user');//查询用户
            $table->integer('recharge_count');//充值笔数
            $table->decimal('recharge_money');//充值金额
            $table->integer('recharge_first_user');//首充用户
            $table->integer('recharge_referer_count');//充值提成笔数
            $table->integer('recharge_referer_amount');//充值提成金额
            $table->integer('excel_download_count');//excel下载次数
            $table->integer('excel_download_user');//excel下载用户数
            $table->integer('excel_save_count');//excel保存个数
            $table->integer('excel_save_user');//excel保存用户数
            $table->timestamps();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statements');
    }
}
