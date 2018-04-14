<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BizRecharge extends Model
{
    protected $table = 'biz_recharge';

    protected $guarded = [];

    public static function generateBillNo()
    {

    }
}
