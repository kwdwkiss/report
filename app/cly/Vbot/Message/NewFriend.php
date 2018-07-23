<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/2/12
 * Time: 20:44.
 */

namespace Cly\Vbot\Message;

class NewFriend extends Message implements MessageInterface
{
    const TYPE = 'new_friend';

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return $this->message;
    }

    protected function getExpand(): array
    {
        return [];
    }
}
