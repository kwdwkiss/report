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
use App\Product;
use App\ProductBill;
use App\Taxonomy;
use App\UserAuthBill;
use App\UserProduct;
use Carbon\Carbon;

class UserAuthBillController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $query = UserAuthBill::query()->with('_user', '_product', '_productBill')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc');

        return UserAuthBillResource::collection($query->paginate());
    }

    public function apply()
    {
        $id = request('id');
        $duration = request('duration');

        $user = \Auth::guard('user')->user();
        $product = Product::findOrFail($id);

        if ($user->_profile->amount < 5000) {
            throw new JsonException('积分不足5000，不能申请认证');
        }

        $exists = UserAuthBill::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('status', 0)
            ->first();

        if ($exists) {
            throw new JsonException('存在待审核的认证申请');
        }

        \DB::transaction(function () use ($user, $product, $duration) {

            if (!in_array($duration, $product->duration)) {
                throw new JsonException('duration error');
            }

            $amountTotal = $product->amount * $duration;

            $productBill = ProductBill::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $duration,
                'amount' => $amountTotal,
                'pay_status' => 0,
            ]);

            UserAuthBill::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_bill_id' => $productBill->id,
                'status' => 0,
            ]);
        });

        return ['message' => '申请成功'];
    }
}