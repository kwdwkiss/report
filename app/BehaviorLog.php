<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BehaviorLog extends Model
{
    protected $guarded = [];

    public static $types = [
        1 => '广告点击',
        2 => '一键EXCEL',
        3 => '管理员登录',
        4 => '用户登录',
        5 => '管理员充值',
    ];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
