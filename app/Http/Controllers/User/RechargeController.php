<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/13
 * Time: 下午3:53
 */

namespace App\Http\Controllers\User;

use App\AmountBill;
use App\Http\Resources\RechargeBillResource;
use App\RechargeBill;
use App\User;

class RechargeController
{
    public function config()
    {

    }

    public function index()
    {
        $user = \Auth::guard('user')->user();

        $recharges = $user->_recharge()->orderBy('created_at', 'desc')->paginate();

        return RechargeBillResource::collection($recharges);
    }

    public function recharge()
    {
        $userId = request('name');
        $tno = request('tno');
        $money = request('money');
        $sign = request('sign');
        $key = request('key');

        if ($key != env('MCB_RECHARGE_KEY')) {
            return 'key invalid';
        }

        $md5 = md5($tno . $userId . $money . env('MCB_RECHARGE_MD5'));
        if ($md5 != $sign) {
            return 'sign invalid';
        }

        $user = User::find($userId);
        if (!$user) {
            return 'user null';
        }

        try {
            \DB::transaction(function () use ($user, $money, $tno) {
                $userProfile = $user->_profile;
                if (!$userProfile) {
                    throw new \Exception('user_profile null');
                }

                $rechargeBill = RechargeBill::create([
                    'user_id' => $user->id,
                    'bill_no' => RechargeBill::generateBillNo($user->id),
                    'pay_type' => 1,
                    'pay_no' => $tno,
                    'money' => $money,
                    'status' => 1
                ]);

                $amount = $rechargeBill->money * 100;
                $userProfile->increment('amount', $amount);

                AmountBill::create([
                    'user_id' => $user->id,
                    'bill_no' => AmountBill::generateBillNo($user->id),
                    'type' => 0,
                    'biz_type' => 1,
                    'biz_id' => $rechargeBill->id,
                    'description' => "充值${money}元"
                ]);
            });
            return 1;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}