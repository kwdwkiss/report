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

class AdminController extends Controller
{
    public function list()
    {
        return AdminResource::collection(Admin::paginate(10));
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
}