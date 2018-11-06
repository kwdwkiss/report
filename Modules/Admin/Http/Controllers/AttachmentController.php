<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/30
 * Time: 下午8:28
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Entities\Attachment;
use Modules\Common\Exceptions\JsonException;
use Illuminate\Http\File;

class AttachmentController
{
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
}