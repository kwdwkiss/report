<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/2
 * Time: 下午7:44
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\PermissionResource;
use Modules\Common\Entities\Permission;
use Modules\Common\Entities\Role;

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