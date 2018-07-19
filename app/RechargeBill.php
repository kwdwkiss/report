<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RechargeBill extends Model
{
    protected $table = 'recharge_bill';

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function generateBillNo($userId)
    {
        $userId = substr($userId, -4);
        return date('ymdHis', time()) . random_int(10, 99) . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }

    public static function recharge($user, $money, $tno, $pay_type)
    {
        \DB::transaction(function () use ($user, $money, $tno, $pay_type) {
            $userProfile = $user->_profile;
            if (!$userProfile) {
                throw new \Exception('user_profile null');
            }

            $rechargeBill = RechargeBill::create([
                'user_id' => $user->id,
                'bill_no' => RechargeBill::generateBillNo($user->id),
                'pay_type' => $pay_type,
                'pay_no' => $tno,
                'money' => $money,
                'status' => 1
            ]);

            $amount = $rechargeBill->money * 100;
            $userProfile->increment('amount', $amount);

            $amountBill = AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 0,
                'amount' => $amount,
                'user_amount' => $userProfile->amount,
                'biz_type' => 1,
                'biz_id' => $rechargeBill->id,
                'description' => "充值${money}元"
            ]);

            //推荐人获得10%积分提成
            $inviter = $userProfile->_inviter;
            if ($inviter) {
                $rate = 0.1;//10%
                $inviterAmount = floor($amount * $rate);
                $inviter->_profile->increment('amount', $inviterAmount);
                AmountBill::create([
                    'user_id' => $inviter->id,
                    'bill_no' => AmountBill::generateBillNo($inviter->id),
                    'type' => 0,
                    'amount' => $inviterAmount,
                    'user_amount' => $inviter->_profile->amount,
                    'biz_type' => 2,
                    'biz_id' => $amountBill->id,
                    'description' => "邀请人充值${amount}积分",
                ]);
            }
        });
    }
}
