<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    const ACCOUNT_STATUS = 1;
    const ACCOUNT_TYPE = 2;
    const REPORT_TYPE = 3;
    const USER_TYPE = 4;
    const ARTICLE_TYPE = 5;

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

    public static function get($pid)
    {
        return static::where('pid', $pid)->orderBy('order')->get();
    }

    public static function accountType()
    {
        return static::get(static::ACCOUNT_TYPE);
    }

    public static function accountStatus()
    {
        return static::get(static::ACCOUNT_STATUS);
    }

    public static function reportType()
    {
        return static::get(static::REPORT_TYPE);
    }

    public static function userType()
    {
        return static::get(static::USER_TYPE);
    }

    public static function articleType()
    {
        return static::get(static::ARTICLE_TYPE);
    }
}
