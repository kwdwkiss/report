<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/15
 * Time: 下午6:05
 */

namespace App\Http\Controllers;


use App\Wechat;
use EasyWeChat\Factory;

class WechatController extends Controller
{
    public function serve()
    {
        $wechat = Wechat::query()->first();

        if (!$wechat) {
            logger('wechat', ['wechat null']);
            return '';
        }

        $app = Factory::officialAccount([]);

        return $app->server->serve();
    }
}