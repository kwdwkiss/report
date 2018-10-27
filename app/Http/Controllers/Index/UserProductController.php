<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 下午12:35
 */

namespace App\Http\Controllers\Index;


use App\AmountBill;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserProductResource;
use App\Product;
use App\ProductBill;
use App\UserProduct;

class UserProductController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $query = UserProduct::query()->with('_product')
            ->where('user_id', $user->id);
        $query->orderBy('id', 'desc');

        return UserProductResource::collection($query->paginate());
    }

    public function create()
    {
        $id = request('id');
        $duration = request('duration');

        $user = \Auth::guard('user')->user();
        $product = Product::findOrFail($id);

        if (!in_array($duration, $product->duration)) {
            throw new JsonException('duration error');
        }

        $amountTotal = $product->amount * $duration;
        if ($user->_profile->amount < $amountTotal) {
            throw new JsonException('用户积分不足，请充值');
        }

        $amountBill = null;
        \DB::transaction(function () use ($user, $product, $duration, $amountTotal, &$amountBill) {
            $user->_profile->decrement('amount', $amountTotal);

            $productBill = ProductBill::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $duration,
                'amount' => $amountTotal,
                'pay_status' => 1,
            ]);

            $unit = $product->getUnit();

            $amountBill = AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 1,
                'amount' => $amountTotal,
                'user_amount' => $user->_profile->amount,
                'biz_type' => 104,
                'biz_id' => $productBill->id,
                'description' => "购买EXCEL包月 时长：{$duration}{$unit}"
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

        return ['message' => $amountBill->description . ' 成功'];
    }
}