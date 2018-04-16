<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AmountBill extends Model
{
    protected $table = 'amount_bill';

    protected $guarded = [];

    public static function generateBillNo($userId)
    {
        return time() . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }
}
