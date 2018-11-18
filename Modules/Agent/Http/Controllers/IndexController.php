<?php

namespace Modules\Agent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Common\Entities\Taxonomy;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'user' => \Auth::guard('agent')->user(),
        ];
        return view('agent::index', compact('config'));
    }
}
