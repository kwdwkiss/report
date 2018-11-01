<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/1
 * Time: 下午9:05
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Role;

class RoleController extends Controller
{
    public function index()
    {

    }

    public function all()
    {
        return RoleResource::collection(Role::all());
    }
}