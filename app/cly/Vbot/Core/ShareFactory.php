<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Cly\Vbot\Core;

use Cly\Vbot\Foundation\Vbot;
use Cly\Vbot\Message\File;
use Cly\Vbot\Message\Mina;
use Cly\Vbot\Message\Official;
use Cly\Vbot\Message\Share;
use Cly\Vbot\Support\Content;

class ShareFactory
{
    public $type;
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function make($msg)
    {
        try {
            $xml = Content::formatContent($msg['Content']);

            $this->parse($xml);

            if ($this->type == 6) {
                return (new File())->make($msg);
            } elseif ($this->vbot->officials->get($msg['FromUserName'])) {
                return (new Official())->make($msg);
            } elseif ($this->type == 33) {
                return (new Mina())->make($msg);
            } else {
                return (new Share())->make($msg);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function parse($xml)
    {
        if (starts_with($xml, '@')) {
            $xml = preg_replace('/(@\S+:\\n)/', '', $xml);
        }

        $array = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->type = $info['type'];
    }
}
