<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/20
 * Time: 下午4:16
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class AmountController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();


    }
}