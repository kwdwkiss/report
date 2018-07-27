<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/27
 * Time: 上午10:35
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAuthBillResource;
use App\Taxonomy;
use App\User;
use App\UserAuthBill;

class UserAuthBillController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');

        $query = UserAuthBill::query()
            ->with('_user')
            ->orderBy('id', 'desc');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                return $query->where('mobile', $mobile);
            });
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

    public function update()
    {

    }

    public function delete()
    {
        $id = request('id');

        $userAuthBill = UserAuthBill::findOrFail($id);

        if ($userAuthBill->status == 1) {
            throw new JsonException('已支付的认证不能删除');
        }

        $userAuthBill->delete();

        return [];
    }
}