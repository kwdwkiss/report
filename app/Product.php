<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'duration' => 'array',
    ];

    public static $typesUnit = [
        1 => '永久',
        2 => '年',
        3 => '月',
    ];

    public function getUnit()
    {
        return static::$typesUnit[$this->type];
    }
}
