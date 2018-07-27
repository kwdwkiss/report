<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAuthBill extends Model
{
    protected $guarded = [];

    public static $statusTypes = [
        0 => '待支付',
        1 => '已支付',
    ];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
