<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Modules\Common\Transformers\AttachmentResource;
use Symfony\Component\HttpFoundation\File\File;

class Attachment extends Model
{
    const TYPE_IMAGE = 0;

    protected $table = 'attachment';

    protected $guarded = [];

    public static function createForLocal(UploadedFile $uploadFile, $user = null, $type = self::TYPE_IMAGE)
    {
        try {
            $dir = 'upload/' . date('Ymd', time());
            $path = $uploadFile->store($dir, 'public');
            $url = '/storage/' . $path;
            $attachment = static::create([
                'user_id' => $user ? $user->id : 0,
                'user_type' => static::parseUserType($user),
                'name' => $path,
                'type' => $type,
                'use' => 0,
                'url' => $url,
                'storage' => 0,
            ]);

            return new AttachmentResource($attachment);
        } catch (\Exception $e) {
            return [
                'code' => -1,
                'message' => '上传失败',
                'error_msg' => $e->getMessage()
            ];
        }
    }

    public static function createForOss(File $uploadFile, $user = null, $bucket = '')
    {
        $filename = $uploadFile->getRealPath();

        $key = date('Ymd/', time()) . md5(microtime(true)) . '.' . $uploadFile->guessExtension();

        $oss = app('aliyun.oss');
        if ($bucket) {
            $oss->setBucket($bucket);
        }
        $result = $oss->uploadFile($key, $filename);

        if ($result['code'] == 0) {
            $attachment = static::create([
                'user_id' => $user ? $user->id : 0,
                'user_type' => static::parseUserType($user),
                'name' => $key,
                'type' => self::TYPE_IMAGE,
                'use' => 0,
                'url' => $result['url'],
                'storage' => 1,
            ]);
            return new AttachmentResource($attachment);
        }
        return $result;
    }

    protected static function parseUserType($user)
    {
        $userType = 0;
        if ($user instanceof User) {
            $userType = 1;
        }
        if ($user instanceof Admin) {
            $userType = 2;
        }
        return $userType;
    }
}
