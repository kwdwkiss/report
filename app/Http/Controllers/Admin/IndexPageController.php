<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/6
 * Time: 下午4:59
 */

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;

class IndexPageController extends Controller
{
    public function get()
    {
        return ['data' => Config::getSiteIndex()];
    }

    public function set()
    {
        Config::setSiteIndex(json_decode(request()->getContent(), true));
        return [];
    }
}