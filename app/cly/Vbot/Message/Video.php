<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/1/13
 * Time: 22:08.
 */

namespace Cly\Vbot\Message;

use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\Traits\Multimedia;
use Cly\Vbot\Message\Traits\SendAble;

class Video extends Message implements MessageInterface
{
    use SendAble, Multimedia;

    const API = 'webwxsendvideomsg?fun=async&f=json&';
    const DOWNLOAD_API = 'webwxgetvideo?msgid=';
    const EXT = '.mp4';
    const TYPE = 'video';

    public function make($msg)
    {
        static::autoDownload($msg);

        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[视频]';
    }

    public static function send($username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw']);

        if (!is_file($file)) {
            return false;
        }

        $response = static::uploadMedia($username, $file);

        return static::sendMsg([
            'Type'         => 43,
            'MediaId'      => $response['MediaId'],
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }

    protected static function getDownloadOption()
    {
        return ['headers' => ['Range' => 'bytes=0-']];
    }
}
