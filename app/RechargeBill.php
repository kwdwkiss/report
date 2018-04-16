<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RechargeBill extends Model
{
    protected $table = 'recharge_bill';

    protected $guarded = [];

    public static function generateBillNo($userId)
    {
        return time() . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }
}
