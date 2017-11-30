<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'article';

    protected $guarded = [];

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }
}
