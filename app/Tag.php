<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $guarded = [];

    public $timestamps = false;

    public function _tagEntities()
    {
        return $this->belongsTo(TagEntity::class, 'id', 'tag_id');
    }
}
