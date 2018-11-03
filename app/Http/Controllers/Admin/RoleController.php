<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/1
 * Time: 下午9:05
 */

namespace App\Http\Controllers\Admin;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Permission;
use App\Role;

class RoleController extends Controller
{
    public function index()
    {
        $query = Role::query()->with('permissions');

        return RoleResource::collection($query->paginate());
    }

    public function show()
    {
        $id = request('id');

        $role = Role::with('permissions')->findOrFail($id);

        return new RoleResource($role);
    }

    public function update()
    {
        $id = request('id');
        $permissionIds = request('permissions');

        $role = Role::findOrFail($id);
        if ($role->isSuperAdmin()) {
            throw new JsonException('超级管理员不允许编辑');
        }

        $perms = Permission::query()->whereIn('id', $permissionIds)->get();
        $role->syncPermissions($perms);

        return [];
    }

    public function all()
    {
        return RoleResource::collection(Role::all());
    }
}