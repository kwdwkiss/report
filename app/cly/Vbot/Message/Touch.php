<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Cly\Vbot\Message;

class Touch extends Message implements MessageInterface
{
    const TYPE = 'touch';

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[点击事件]';
    }
}
