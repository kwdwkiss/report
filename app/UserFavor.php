<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFavor extends Model
{
    public static $total = 150;

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
