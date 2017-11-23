<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    protected $primaryKey = 'name';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'data' => 'array'
    ];

    public static function get($name, $default = '')
    {
        $config = static::find($name);
        if (!$config) {
            return $default;
        }
        return $config->data;
    }

    public static function set($name, $data)
    {
        static::updateOrCreate([
            'name' => $name
        ], [
            'data' => $data === null ? '' : $data
        ]);
    }
}
