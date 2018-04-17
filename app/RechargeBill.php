<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RechargeBill extends Model
{
    protected $table = 'recharge_bill';

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function generateBillNo($userId)
    {
        return date('ymdHis', time()) . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }
}
