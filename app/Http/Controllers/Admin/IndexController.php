<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Taxonomy;
use App\User;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'siteName' => Config::get('site.name') . ' 管理后台',
        ];
        return view('admin', compact('config'));
    }

    public function info()
    {
        return ['data' => \Auth::guard('admin')->user()];
    }

    public function login()
    {
        if (\Auth::guard('admin')->attempt([
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

    public function upload()
    {
        $dir = 'upload/' . date('Ym', time()) . '/' . date('d', time());

        $path = request()->file('file')->store($dir, 'public');

        $url = asset('storage/' . $path);

        return ['data' => $url];
    }

    public function statement()
    {
        $today = User::query()
            ->where('created_at', '>', Carbon::today())
            ->count();

        $yesterday = User::query()
            ->where('created_at', '>', Carbon::yesterday())
            ->where('created_at', '<', Carbon::today())
            ->count();

        $month = User::query()
            ->where('created_at', '>', Carbon::now()->startOfMonth())
            ->count();

        $lastMonth = User::query()
            ->where('created_at', '>', Carbon::now()->subMonths(1)->startOfMonth())
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->count();

        return [
            'data' => [
                'userRegister' => compact('today', 'yesterday', 'week', 'lastWeek', 'month', 'lastMonth')
            ]
        ];
    }
}