<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    protected $guarded = [];

    protected $dates = [
        'start_at',
        'end_at',
    ];

    public static $status = [
        0 => '未启用',
        1 => '已启用',
        2 => '已过期',
    ];

    public static $productTypes = [
        'excel' => [1]
    ];

    public function _productBill()
    {
        return $this->belongsTo(ProductBill::class, 'product_bill_id', 'id');
    }

    public function _product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public static function hasEnableProducts($user, $productKey)
    {
        $where = static::query()
            ->whereIn('product_id', static::$productTypes[$productKey])
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->where('end_at', '>', Carbon::now());
        return $where
                ->count() > 0;
    }

    public function enable()
    {
        //找到已经启用的最后一个产品
        $userProduct = UserProduct::query()
            ->where('user_id', $this->user_id)
            ->where('status', 1)
            ->where('end_at', '>', Carbon::now())
            ->orderBy('end_at', 'desc')
            ->first();

        if ($userProduct) {
            $start_at = $userProduct->end_at;
        } else {
            $start_at = Carbon::now();
        }
        $quantity = $this->_productBill->quantity;
        $end_at = $start_at->copy()->addMonth($quantity);

        $this->update([
            'status' => 1,
            'start_at' => $start_at,
            'end_at' => $end_at,
        ]);
    }
}
