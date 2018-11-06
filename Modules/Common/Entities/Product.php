<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'duration' => 'array',
    ];

    public static $types = [
        1 => '永久',
        2 => '年',
        3 => '月',
    ];

    public static $userTypes = [
        2 => 402,
        3 => 404,
        4 => 403,
    ];

    public function getTypeLabel()
    {
        return static::$types[$this->type];
    }

    public function getUserType()
    {
        return static::$userTypes[$this->id];
    }
}
