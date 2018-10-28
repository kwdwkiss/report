<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/27
 * Time: 上午10:35
 */

namespace App\Http\Controllers\Admin;


use App\AmountBill;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthBillResource;
use App\Taxonomy;
use App\User;
use App\UserAuthBill;
use App\UserProduct;
use Carbon\Carbon;

class UserAuthBillController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $status = request('status');

        $query = UserAuthBill::query()->with('_user._profile', '_admin', '_product', '_productBill')
            ->orderBy('id', 'desc');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                return $query->where('mobile', $mobile);
            });
        }
        if (is_numeric($status)) {
            $query->where('status', $status);
        }

        return UserAuthBillResource::collection($query->paginate());
    }

    public function create()
    {
        $mobile = request('mobile');
        $type = request('type');
        $duration = request('duration');

        $user = User::query()->where('mobile', $mobile)->first();
        if (!$user) {
            throw new JsonException('手机号不存在');
        }
        Taxonomy::query()->where('pid', Taxonomy::USER_TYPE)->findOrFail($type);
        if (!in_array($duration, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 24, 36])) {
            throw new JsonException('时长错误');
        }

        $userAuthBill = UserAuthBill::query()
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->first();
        if ($userAuthBill) {
            throw new JsonException('存在未支付的用户认证');
        }

        //计算积分
        if (in_array($duration, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11])) {
            $amount = min($duration * 417, 5000);
        } elseif ($duration == 12) {
            $amount = 5000;
        } elseif ($duration == 24) {
            $amount = 10000;
        } elseif ($duration == 36) {
            $amount = 15000;
        }

        UserAuthBill::create([
            'user_id' => $user->id,
            'status' => 0,
            'type' => $type,
            'duration' => $duration,
            'amount' => $amount
        ]);

        //锁定用户资料
        $user->_profile->update(['user_lock' => 1,]);

        return [];
    }

    public function check()
    {
        $id = request('id');

        \DB::transaction(function () use ($id) {
            $admin = \Auth::guard('admin')->user();

            $userAuthBill = UserAuthBill::findOrFail($id);
            $user = $userAuthBill->_user;
            $productBill = $userAuthBill->_productBill;
            $product = $userAuthBill->_product;

            $amount = $productBill->amount;
            $duration = $productBill->duration;
            $unit = $product->getTypeLabel();

            if ($user->_profile->amount < $amount) {
                throw new JsonException('用户积分不足');
            }

            $userAuthBill->update([
                'admin_id' => $admin->id,
                'status' => 1,
                'check_at' => Carbon::now(),
            ]);

            $user->_profile->decrement('amount', $amount);

            AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 1,
                'amount' => $amount,
                'user_amount' => $user->_profile->amount,
                'biz_type' => 104,
                'biz_id' => $productBill->id,
                'description' => "用户认证包年 时长：{$duration}{$unit}"
            ]);

            $productBill->update([
                'pay_status' => 1,
                'pay_at' => Carbon::now(),
            ]);

            $userProduct = UserProduct::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_bill_id' => $productBill->id,
                'status' => 0,
            ]);

            //启用
            $userProduct->enable();
        });

        return ['message' => '审核成功'];
    }

    public function reject()
    {
        return [];
    }
}