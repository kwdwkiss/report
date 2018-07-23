<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Cly\Vbot\Message;

class FriendVerify extends Message implements MessageInterface
{
    const TYPE = 'friend_verify';

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[朋友验证]';
    }
}
