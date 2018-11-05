<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminArticle extends Model
{
    public static $types = [
        1 => '内部公告'
    ];

    protected $guarded = [];
}
