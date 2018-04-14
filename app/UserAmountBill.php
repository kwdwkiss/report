<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAmountBill extends Model
{
    protected $table = 'user_amount_bill';

    protected $guarded = [];

    public static function generateBillNo()
    {

    }
}
