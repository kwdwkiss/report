<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace App\Http\Controllers\User;

use App\Config;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'siteName' => Config::get('site.name') . ' 用户后台',
        ];
        return view('user', compact('config'));
    }

    public function info()
    {
        return ['data' => \Auth::guard('user')->user()];
    }

    public function login()
    {
        if (\Auth::guard('user')->attempt([
            'name' => request('username'),
            'password' => request('password')
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
        \Auth::guard('user')->logout();
        return [];
    }

    public function modifyPassword()
    {
        $this->validate(request(), [
            'newPassword' => 'required|min:8'
        ]);
        $user = \Auth::guard('user')->user();
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