<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Entities\Taxonomy;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'user' => \Auth::guard('admin')->user(),
        ];
        return view('admin::index', compact('config'));
    }
}