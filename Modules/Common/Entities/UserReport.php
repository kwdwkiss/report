<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
