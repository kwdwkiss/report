<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 上午10:08
 */

namespace App\Http\Controllers\Admin;

use App\AccountReport;
use App\AccountSearch;
use App\Attachment;
use App\Config;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\RechargeBill;
use App\Taxonomy;
use App\User;

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
        ], request('remember'))) {
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
        $uploadFile = request()->file('file');

        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }
        if (!$uploadFile->isValid()) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }

        $user = \Auth::guard('admin')->user();

        return Attachment::createForLocal($uploadFile, $user);
    }

    public function uploadOss()
    {
        $user = \Auth::guard('admin')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再上传图片');
        }

        $uploadFile = request()->file('file');

        if (!$uploadFile) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }
        if (!$uploadFile->isValid()) {
            throw new JsonException('上传文件失败，请稍后再次尝试');
        }

        $size = $uploadFile->getSize();//byte

        $limit = 1000;

        if ($size / 1024 > $limit) {
            return [
                'code' => -1,
                'message' => "上传文件不能超过{$limit}KB"
            ];
        }

        $user = \Auth::guard('admin')->user();

        return Attachment::createForOss($uploadFile, $user, env('ALIYUN_OSS_BUCKET_ADMIN'));
    }

    public function statement()
    {
        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

        return [
            'data' => [
                'userRegister' => User::statement(),
                'accountReport' => AccountReport::statement(),
                'accountSearch' => AccountSearch::statement(),
                'rechargeBill' => RechargeBill::statement()
            ]
        ];
    }
}