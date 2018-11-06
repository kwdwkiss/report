<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'account';

    protected $guarded = [];

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }

    public function _status()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'status');
    }
}
