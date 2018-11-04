<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/2
 * Time: 下午7:44
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Permission;
use App\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $query = Permission::query();

        return PermissionResource::collection($query->paginate());
    }

    public function all()
    {
        $query = Permission::query()
            ->orderBy('controller');

        return PermissionResource::collection($query->get());
    }

    public function refresh()
    {
        Permission::initCreate();

        Role::refreshSuperAdminPermission();

        return [];
    }
}