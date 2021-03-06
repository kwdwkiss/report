<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductBill extends Model
{
    protected $guarded = [];

    protected $dates = [
        'pay_at',
    ];

    public static $payStatus = [
        0 => '未支付',
        1 => '已支付',
        2 => '已取消',
    ];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function _product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
