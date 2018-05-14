<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:36
 */

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    protected $basicList = [
        'domain',
        'name',
        'close_register',
        'index_blog_article'
    ];

    public function getBasic()
    {
        $basic = [];

        \DB::transaction(function () use (&$basic) {
            foreach ($this->basicList as $name) {
                $basic[$name] = Config::get('site.' . $name);
            }
        });

        return ['data' => $basic];
    }

    public function setBasic()
    {
        \DB::transaction(function () {
            $input = request()->only($this->basicList);

            foreach ($input as $name => $data) {
                Config::set('site.' . $name, $data);
            }
        });

        return [];
    }

    public function getIndex()
    {
        return ['data' => Config::getSiteIndex()];
    }

    public function setIndex()
    {
        Config::setSiteIndex(json_decode(request()->getContent(), true));
        return [];
    }

    public function popWindow()
    {
        Config::setSitePopWindow(json_decode(request()->getContent(), true));
        return [];
    }
}