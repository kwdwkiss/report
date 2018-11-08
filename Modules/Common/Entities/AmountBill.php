<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class AmountBill extends Model
{
    protected $table = 'amount_bill';

    protected $guarded = [];

    public static $types = [
        0 => '收入',
        1 => '支出',
    ];

    public static $status = [
        0 => '待支付',
        1 => '已支付',
        2 => '已撤销',
    ];

    public static $bizTypes = [
        0 => '系统发放',
        1 => '充值',
        2 => '充值提成',
        3 => '下载EXCEL提成',
        4 => '点赞送积分',
        101 => '查询结算',
        102 => '认证会员',
        103 => '下载EXCEL',
        104 => '购买EXCEL包月服务',
    ];

    public $sysGiveAmount = [
        1 => '新用户注册赠送200积分',
        2 => '邀请新用户注册赠送100积分',
        3 => '邀请用户充值积分，提成10%',
        4 => '新用户注册赠送100积分',
        5 => '邀请新用户注册赠送50积分',
        6 => '下载EXCEL扣除10积分',
        7 => '推荐好友下载EXCEL提成4积分',
        8 => '点赞送2积分',
    ];

    public static function generateBillNo($userId)
    {
        $length = 5;
        $userId = substr($userId, -$length);
        return date('ymdHis', time()) . random_int(10, 99) . str_pad($userId, $length, '0', STR_PAD_LEFT);
    }

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
