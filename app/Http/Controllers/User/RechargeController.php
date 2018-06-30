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
        $name = request('name');
        $tno = request('tno');
        $money = request('money');
        $sign = request('sign');
        $key = request('key');

        if ($key != env('MCB_RECHARGE_KEY')) {
            return 'key invalid';
        }

        $md5 = md5($tno . $name . $money . env('MCB_RECHARGE_MD5'));
        if ($md5 != $sign) {
            return 'sign invalid';
        }

        $user = User::where('mobile', $name)->first();
        if (!$user) {
            return 'user null';
        }

        $exist = RechargeBill::query()
            ->where('pay_no', $tno)
            ->where('pay_type', 1)
            ->first();
        if ($exist) {
            return 1;
        }

        try {
            RechargeBill::recharge($user, $money, $tno, 1);
            return 1;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageCallback()
    {

    }
}