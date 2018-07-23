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
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\RechargeBill;
use App\Statement;
use App\Taxonomy;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\File;

class IndexController extends Controller
{
    public function index()
    {
        $config = [
            'taxonomy' => Taxonomy::allData(),
            'user' => \Auth::guard('admin')->user(),
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

    public function uploadOssImage()
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
            throw new JsonException('上传文件失败，文件大小必须小于5M，请稍后再次尝试');
        }

        $watermark = request('watermark', '');
        $watermarkList = [
            'identity' => public_path('images/indentity_watermark.png'),
        ];
        $watermark = array_get($watermarkList, $watermark);

        $size = $uploadFile->getSize();//byte

        $limit = 200;

        if ($size / 1024 > $limit || $watermark) {
            try {
                $image = \Image::make($uploadFile);
            } catch (\Exception $e) {
                throw new JsonException('图片只支持JPG，PNG，GIF格式。');
            }
            if ($image->height() > 600) {
                $image->heighten(600);
            } elseif ($image->width() > 600) {
                $image->widen(600);
            }
            if ($watermark && is_file($watermark)) {
                $image->insert($watermark, 'center');
            }
            $filename = storage_path() . '/' . md5(microtime());
            $image->save($filename);
            $uploadFile = new File($filename);
        }

        $result = Attachment::createForOss($uploadFile, $user);

        unlink($uploadFile->getRealPath());

        return $result;
    }

    public function statement()
    {
        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

        $data = \Cache::get('statement.profile', Statement::profile());

        return ['data' => $data];
    }
}