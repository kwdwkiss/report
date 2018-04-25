<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmountBill extends Model
{
    protected $table = 'amount_bill';

    protected $guarded = [];

    public $sysGiveAmount = [
        1 => '新用户注册赠送200积分'
    ];

    public static function generateBillNo($userId)
    {
        $userId = substr($userId, -4);
        return date('ymdHis', time()) . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }
}
