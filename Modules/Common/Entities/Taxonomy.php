<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    const ACCOUNT_STATUS = 1;
    const ACCOUNT_TYPE = 2;
    const REPORT_TYPE = 3;
    const USER_TYPE = 4;
    const ARTICLE_TYPE = 5;

    public static $types = [
        0 => 'root',
        1 => 'account_status',
        2 => 'account_type',
        3 => 'report_type',
        4 => 'user_type',
        5 => 'article_type',
    ];

    protected $table = 'taxonomy';

    protected $guarded = [];

    public $timestamps = false;

    public function _parent()
    {
        return $this->hasOne(static::class, 'id', 'pid');
    }

    public function _children()
    {
        return $this->hasMany(static::class, 'pid', 'id');
    }

    public static function allData()
    {
        $taxonomy = Taxonomy::orderBy('order')->get()->groupBy('pid');
        $data = [];
        foreach ($taxonomy as $key => $value) {
            $data[static::$types[$key]] = $value->toArray();
        }
        return $data;
    }

    public static function allDisplay()
    {
        $taxonomy = Taxonomy::where('display', 1)->orderBy('order')->get()->groupBy('pid');
        $data = [];
        foreach ($taxonomy as $key => $value) {
            $data[static::$types[$key]] = $value->toArray();
        }
        return $data;
    }

    public static function userTypes()
    {
        return static::where('pid', 4)->get()->pluck('id')->toArray();
    }
}
