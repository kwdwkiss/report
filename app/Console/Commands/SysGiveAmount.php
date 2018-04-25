<?php

namespace App\Console\Commands;

use App\AmountBill;
use Illuminate\Console\Command;

class SysGiveAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sys_give_amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userIds = AmountBill::select('user_id')->where('biz_type', 0)->where('biz_id', 1)->get()->pluck('user_id');

        \App\User::with('_amountBill', '_profile')->whereNotIn('id', $userIds)->chunk(1000, function ($users) {
            foreach ($users as $user) {
                \DB::transaction(function () use ($user) {
                    //新用户注册发放200积分
                    $amount = 200;
                    AmountBill::create([
                        'user_id' => $user->id,
                        'bill_no' => AmountBill::generateBillNo($user->id),
                        'type' => 0,
                        'amount' => $amount,
                        'biz_type' => 0,
                        'biz_id' => 1,
                        'description' => "新用户注册赠送${amount}积分"
                    ]);
                    $user->_profile->increment('amount', $amount);
                });
            }
        });
    }
}
