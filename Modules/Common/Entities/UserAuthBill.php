<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class UserAuthBill extends Model
{
    protected $guarded = [];

    protected $dates = [
        'check_at'
    ];

    public static $statusTypes = [
        0 => '待审核',
        1 => '已审核',
        2 => '已拒绝',
    ];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function _admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function _product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function _productBill()
    {
        return $this->belongsTo(ProductBill::class, 'product_bill_id', 'id');
    }
}
