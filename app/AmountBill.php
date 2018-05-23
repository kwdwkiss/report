<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmountBill extends Model
{
    protected $table = 'amount_bill';

    protected $guarded = [];

    public static $types = [
        0 => '收入',
        1 => '支出',
    ];

    public static $bizTypes = [
        0 => '系统发放',
        1 => '充值',
        2 => '充值提成',
        101 => '查询结算',
    ];

    public $sysGiveAmount = [
        1 => '新用户注册赠送200积分',
        2 => '邀请新用户注册赠送100积分',
        3 => '邀请用户充值积分，提成10%',
    ];

    public static function generateBillNo($userId)
    {
        $userId = substr($userId, -4);
        return date('ymdHis', time()) . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
