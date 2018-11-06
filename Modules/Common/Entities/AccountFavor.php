<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class AccountFavor extends Model
{
    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function _accountType()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'account_type');
    }
}
