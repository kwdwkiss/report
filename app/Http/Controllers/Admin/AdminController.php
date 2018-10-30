<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/7
 * Time: 上午10:24
 */

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\BehaviorLog;

class AdminController extends Controller
{
    public function index()
    {
        return AdminResource::collection(Admin::paginate());
    }

    public function create()
    {
        $superUser = \Auth::guard('admin')->user();
        if ($superUser->id != 1) {
            return [
                'code' => -1,
                'message' => '只有超级管理员才能进行此操作'
            ];
        }
        $name = request('name');

        $existsAdmin = Admin::where('name', $name)->first();
        if ($existsAdmin) {
            return [
                'code' => -1,
                'message' => '用户名已存在'
            ];
        }

        Admin::create([
            'name' => $name,
            'password' => bcrypt(request('password'))
        ]);

        return [];
    }

    public function update()
    {
        $this->validate(request(), [
            'password' => 'required'
        ]);
        $superUser = \Auth::guard('admin')->user();
        if ($superUser->id != 1) {
            return [
                'code' => -1,
                'message' => '只有超级管理员才能进行此操作'
            ];
        }
        $user = Admin::findOrFail(request('id'));
        $user->update([
            'password' => bcrypt(request('password'))
        ]);

        return [];
    }

    public function delete()
    {
        $superUser = \Auth::guard('admin')->user();
        if ($superUser->id != 1) {
            return [
                'code' => -1,
                'message' => '只有超级管理员才能进行此操作'
            ];
        }
        $user = Admin::findOrFail(request('id'));
        if ($user->id == 1) {
            return [
                'code' => -1,
                'message' => '不能删除超级管理员'
            ];
        }
        $user->delete();

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
            return [
                'code' => 100,
                'message' => '授权码错误'
            ];
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
        $this->validate(request(), [
            'newPassword' => 'required|min:8'
        ]);
        $user = \Auth::guard('admin')->user();
        if (!\Hash::check(request('password'), $user->password)) {
            return [
                'code' => -1,
                'message' => '密码错误'
            ];
        }
        $user->update([
            'password' => bcrypt(request('newPassword'))
        ]);
        return [];
    }
}