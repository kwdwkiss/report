<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/1/10
 * Time: 16:51.
 */

namespace Cly\Vbot\Message;

use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\Traits\Multimedia;
use Cly\Vbot\Message\Traits\SendAble;

class Image extends Message implements MessageInterface
{
    use Multimedia, SendAble;

    const API = 'webwxsendmsgimg?fun=async&f=json&';
    const DOWNLOAD_API = 'webwxgetmsgimg?&MsgID=';
    const EXT = '.jpg';
    const TYPE = 'image';

    public function make($msg)
    {
        static::autoDownload($msg);

        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[图片]';
    }

    public static function send($username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw']);

        if (!is_file($file)) {
            return false;
        }

        $response = static::uploadMedia($username, $file);

        return static::sendMsg([
            'Type'         => 3,
            'MediaId'      => $response['MediaId'],
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }
}