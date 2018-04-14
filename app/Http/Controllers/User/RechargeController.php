<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/13
 * Time: 下午3:53
 */

namespace App\Http\Controllers\User;


use App\BizRecharge;
use App\Exceptions\JsonException;
use App\User;
use App\UserAmountBill;
use Carbon\Carbon;

class RechargeController
{
    public function config()
    {

    }

    public function index()
    {

    }

    public function create()
    {
        $money = request('money');
        $pay_type = request('pay_type');
        $user = \Auth::guard('user')->user();

        if (!is_int($money) || $money < 1) {
            throw new JsonException('充值金额必须大于等于1元且为整数');
        }
        if (!in_array($pay_type, [0, 1])) {
            throw new JsonException('pay_type error');
        }
        if (!$user) {
            throw new JsonException('user null');
        }

        $count = BizRecharge::where('user_id', $user)
            ->where('status', 0)
            ->where('created_at', '>', Carbon::now()->subHours(1))
            ->count();
        $limit = 5;
        if ($count >= $limit) {
            throw new JsonException("1小时内创建了${limit}张以上订单，但未充值到账，请稍后再来");
        }

        BizRecharge::create([
            'user_id' => $user->id,
            'bill_no' => BizRecharge::generateBillNo(),
            'pay_type' => $pay_type,
            'money' => $money,
            'status' => 0
        ]);

        return [];
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

        $bizRecharge = BizRecharge::where('user_id', $userId)
            ->where('type', 0)
            ->where('money', $money)
            ->where('created_at', '>', Carbon::now()->subHours(2))//2小时内的订单有效
            ->orderBy('created_at', 'desc')
            ->fisrt();
        if (!$bizRecharge) {
            return 'biz_recharge null';
        }

        try {
            \DB::transaction(function () use ($user, $bizRecharge, $tno) {
                $userProfile = $user->_profile;
                if (!$userProfile) {
                    throw new \Exception('user_profile null');
                }

                $amount = $bizRecharge->money * 100;
                $userProfile->increment('amount', $amount);

                UserAmountBill::create([
                    'user_id' => $user->id,
                    'bill_no' => UserAmountBill::generateBillNo(),
                    'type' => 0,
                    'amount' => $amount,
                    'biz_id' => $bizRecharge->id,
                    'biz_type' => 0
                ]);

                $bizRecharge->pay_no = $tno;
                $bizRecharge->status = 1;
                $bizRecharge->save();
            });
            return 1;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}