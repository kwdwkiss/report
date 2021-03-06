<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\AmountBill;
use Modules\Common\Entities\BehaviorLog;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\RechargeBillResource;
use Modules\Common\Entities\RechargeBill;
use Modules\Common\Entities\User;

class RechargeController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $payType = request('pay_type');
        $payNo = request('pay_no');
        $created_at = request('created_at');

        $query = RechargeBill::query()->with('_user');

        $order_query = json_decode(request('order_query'), true);
        $order_field = array_get($order_query, 'field');
        $order_order = array_get($order_query, 'order');
        if ($order_field) {
            if ($order_order == 'ascending') {
                $query->orderBy($order_field, 'asc');
            } elseif ($order_order == 'descending') {
                $query->orderBy($order_field, 'desc');
            }
        }
        $query->orderBy('id', 'desc');

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
            $pay_type = request('pay_type');
            $mobile = request('mobile');
            $pay_no = request('pay_no');
            $money = request('money');

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

            RechargeBill::recharge($user, $money, $pay_no, $pay_type);

            $admin = \Auth::guard('admin')->user();
            $data = [
                'user_id' => $user->id,
                'money' => $money,
                'pay_no' => $pay_no,
                'pay_type' => $pay_type,
                'admin_user_id' => $admin->id,
            ];
            BehaviorLog::create([
                'type' => 5,
                'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ]);
        });

        return [];
    }

    public function cancel()
    {
        $id = request('id');

        RechargeBill::cancel($id);

        return ['message' => '撤销成功'];
    }
}