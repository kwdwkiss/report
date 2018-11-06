<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function _inviter()
    {
        return $this->belongsTo(User::class, 'inviter', 'mobile');
    }
}
