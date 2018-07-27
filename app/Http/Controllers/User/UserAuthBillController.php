<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/27
 * Time: 下午3:29
 */

namespace App\Http\Controllers\User;


use App\AmountBill;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthBillResource;
use App\Taxonomy;
use App\UserAuthBill;
use Carbon\Carbon;

class UserAuthBillController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $query = UserAuthBill::query()
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc');

        return UserAuthBillResource::collection($query->paginate());
    }

    public function pay()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        $userAuthBill = UserAuthBill::query()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        \DB::transaction(function () use ($user, $userAuthBill) {
            $amount = $userAuthBill->amount;

            if ($user->_profile->amount < $amount) {
                throw new JsonException('用户积分不足，请充值积分');
            }

            $typeLabel = Taxonomy::findOrFail($userAuthBill->type)->name;
            $durationLabel = $userAuthBill->duration . '个月';

            $user->_profile->decrement('amount', $amount);
            AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 1,
                'amount' => $amount,
                'user_amount' => $user->_profile->amount,
                'biz_type' => 102,
                'biz_id' => $userAuthBill->id,
                'description' => "认证：{$typeLabel} 时长：$durationLabel"
            ]);

            $user->update([
                'type' => $userAuthBill->type,
                'auth_type' => $userAuthBill->type,
                'auth_duration' => $userAuthBill->duration,
                'auth_start_at' => Carbon::now(),
                'auth_end_at' => Carbon::now()->addMonths($userAuthBill->duration),
            ]);

            $userAuthBill->update([
                'status' => 1,
                'pay_at' => Carbon::now()
            ]);
        });

        return [];
    }
}