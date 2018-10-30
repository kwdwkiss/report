<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Taxonomy;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'user' => \Auth::guard('admin')->user(),
        ];
        return view('admin', compact('config'));
    }
}