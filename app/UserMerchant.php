<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMerchant extends Model
{
    protected $table = 'user_merchant';

    protected $guarded = [];

    public static $types = [
        0 => '未知',
        1 => '天猫店',
        2 => '企业淘宝店',
        3 => '个人淘宝店'
    ];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
