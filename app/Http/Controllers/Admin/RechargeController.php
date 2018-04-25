<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\AmountBill;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\RechargeBillResource;
use App\RechargeBill;
use App\User;

class RechargeController extends Controller
{
    public function list()
    {
        $mobile = request('mobile');
        $payType = request('pay_type');
        $payNo = request('pay_no');
        $created_at = request('created_at');

        $query = RechargeBill::query()->with('_user')->orderBy('created_at', 'desc');

        if (!is_null($mobile)) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }
        if (!is_null($payType)) {
            $query->where('pay_type', $payType);
        }
        if (!is_null($payNo)) {
            $query->where('pay_no', $payNo);
        }
        if (!is_null($created_at)) {
            $query->where('created_at', '>', $created_at[0]);
            $query->where('created_at', '<', date('Y-m-d', strtotime($created_at[1] . ' +1 day')));
        }

        return RechargeBillResource::collection($query->paginate());
    }

    public function create()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            foreach ($input as $item) {
                $pay_type = array_get($item, 'pay_type');
                $mobile = array_get($item, 'mobile');
                $pay_no = array_get($item, 'pay_no');
                $money = array_get($item, 'money');

                if (!in_array($pay_type, [1, 2])) {
                    throw new JsonException('支付类型错误');
                }

                $user = User::where('mobile', $mobile)->first();
                if (!$user) {
                    throw new JsonException('用户不存在');
                }

                if (is_null($pay_no)) {
                    throw new JsonException('外部订单号不为空');
                }

                if (is_null($money)) {
                    throw new JsonException('金额不为空');
                }

                \DB::transaction(function () use ($user, $pay_no, $pay_type, $money) {
                    $userProfile = $user->_profile;
                    if (!$userProfile) {
                        throw new \Exception('user_profile null');
                    }

                    $rechargeBill = RechargeBill::create([
                        'user_id' => $user->id,
                        'bill_no' => RechargeBill::generateBillNo($user->id),
                        'pay_type' => $pay_type,
                        'pay_no' => $pay_no,
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
            }
        });

        return [];
    }
}