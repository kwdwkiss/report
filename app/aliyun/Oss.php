<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/3/26
 * Time: 下午4:25
 */

namespace Aliyun;

use OSS\Core\OssException;
use OSS\OssClient;

class Oss
{
    protected $accessKeyId;

    protected $accessKeySecret;

    protected $endpoint;

    protected $ossClient;

    protected $bucket;

    public function __construct($accessKeyId, $accessKeySecret, $endpoint)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->endpoint = $endpoint;

        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    public function uploadFile($key, $filename, $bucket = null)
    {
        $bucket = $bucket ? $bucket : $this->bucket;
        try {
            $res = $this->ossClient->uploadFile($bucket, $key, $filename);
        } catch (OssException $e) {
            return [
                'code' => -1,
                'message' => '失败',
                'error_code' => $e->getErrorCode(),
                'error_msg' => $e->getErrorMessage()
            ];
        }
        return [
            'code' => 0,
            'message' => '成功',
            'url' => str_replace(['http:', 'https'], '', $res['oss-request-url'])
        ];
    }
}