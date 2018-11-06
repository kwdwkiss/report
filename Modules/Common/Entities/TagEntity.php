<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class TagEntity extends Model
{
    protected $table = 'tag_entity';

    protected $guarded = [];

    public $timestamps = false;
}
