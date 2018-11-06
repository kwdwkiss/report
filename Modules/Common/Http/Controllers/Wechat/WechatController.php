<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/15
 * Time: 下午6:05
 */

namespace Modules\Common\Http\Controllers\Wechat;

use EasyWeChat\Factory;
use Modules\Common\Entities\Wechat;
use Modules\Common\Http\Controllers\Controller;

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