<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/7
 * Time: 上午10:24
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\Admin;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AdminResource;
use Modules\Common\Entities\BehaviorLog;
use Modules\Common\Entities\Role;
use Cly\RegExp\RegExp;

class AdminController extends Controller
{
    public function index()
    {
        $query = Admin::with('roles');

        return AdminResource::collection($query->paginate());
    }

    public function create()
    {
        \DB::transaction(function () {
            $name = request('name');
            $password = request('password');
            $rolesIds = request('roles_ids', []);

            $existsAdmin = Admin::where('name', $name)->lockForUpdate()->first();
            if ($existsAdmin) {
                throw new JsonException('用户名已存在');
            }

            if (!preg_match(RegExp::PASSWORD, $password)) {
                throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
            }

            $roles = Role::whereIn('id', $rolesIds)->get();
            $rolesName = array_column($roles->toArray(), 'name');
            if (in_array('super_admin', $rolesName)) {
                $operator = \Auth::guard('admin')->user();
                if (!$operator->isFounder()) {
                    throw new JsonException('创始人才能分配超级管理员');
                }
            }

            $admin = Admin::create([
                'name' => $name,
                'password' => bcrypt($password)
            ]);

            $admin->syncRoles($roles);
        });

        return [];
    }

    public function show()
    {
        $id = request('id');

        $admin = Admin::with('roles')->findOrFail($id);

        return new AdminResource($admin);
    }

    public function update()
    {
        \DB::transaction(function () {
            $id = request('id');
            $password = request('password');
            $rolesIds = request('roles_ids', []);

            if ($password && !preg_match(RegExp::PASSWORD, $password)) {
                throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
            }

            $admin = Admin::findOrFail($id);

            $roles = Role::whereIn('id', $rolesIds)->get();
            $rolesName = array_column($roles->toArray(), 'name');
            if (in_array('super_admin', $rolesName)) {
                $operator = \Auth::guard('admin')->user();
                if (!$operator->isFounder()) {
                    throw new JsonException('创始人才能分配超级管理员');
                }
            }

            if ($password) {
                $admin->password = bcrypt($password);
            }
            $admin->save();

            $admin->syncRoles($roles);
        });

        return [];
    }

    public function delete()
    {
        $id = request('id');

        $admin = Admin::findOrFail($id);

        if ($admin->isFounder()) {
            throw new JsonException('创始人不能删除');
        }

        $operator = \Auth::guard('admin')->user();
        if ($operator->id == $id) {
            throw new JsonException('不能删除自己');
        }

        $admin->delete();

        return [];
    }

    public function info()
    {
        return ['data' => \Auth::guard('admin')->user()];
    }

    public function login()
    {
        $username = request('username');
        $password = request('password');
        $auth_code = request('auth_code');

        $ip = get_client_ip();
        BehaviorLog::create([
            'type' => 3,
            'content' => json_encode([
                'username' => $username,
                'password' => $password,
                'auth_code' => $auth_code,
                'ip' => $ip,
                'geo' => get_geo_str($ip)
            ], JSON_UNESCAPED_UNICODE)
        ]);

        if (env('SITE_ADMIN_AUTH_CODE') && env('SITE_ADMIN_AUTH_CODE') != $auth_code) {
            throw new JsonException('授权码错误');
        }

        if (\Auth::guard('admin')->attempt([
            'name' => $username,
            'password' => $password
        ])) {
            return [];
        }
        return [
            'code' => 100,
            'message' => '登录失败'
        ];
    }

    public function logout()
    {
        \Auth::guard('admin')->logout();
        return [];
    }

    public function modifyPassword()
    {
        $this->validate(request(), ['newPassword' => 'required|min:8']);

        $user = \Auth::guard('admin')->user();
        if (!\Hash::check(request('password'), $user->password)) {
            throw new JsonException('密码错误');
        }
        $user->update([
            'password' => bcrypt(request('newPassword'))
        ]);
        return [];
    }
}