<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table = 'sms';

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at'
    ];
}
