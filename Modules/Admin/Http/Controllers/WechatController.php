<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/15
 * Time: 下午4:51
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\WechatReource;
use Modules\Common\Entities\Wechat;

class WechatController extends Controller
{
    public function getServer()
    {
        $wechat = Wechat::query()->firstOrCreate([], [
            'wechat_id' => 'gh_fabdef6e2911',
            'app_id' => 'wx1120af7462a3189a',
            'app_secret' => '684b481e3f783330b6ae960cb5688a36'
        ]);
        $wechat->url = url('/wechat');

        return [
            'data' => new WechatReource($wechat)
        ];
    }

    public function setServer()
    {
        $id = request('id');
        $wechat_id = request('wechat_id');
        $app_id = request('app_id');
        $app_secret = request('app_secret');

        $wechat = Wechat::findOrFail($id);

        $wechat->update([
            'wechat_id' => $wechat_id,
            'app_id' => $app_id,
            'app_secret' => $app_secret
        ]);

        return [];
    }

    public function refreshToken()
    {
        $id = request('id');

        $wechat = Wechat::findOrFail($id);

        $wechat->update([
            'token' => str_random(32)
        ]);

        return [];
    }

    public function getMenu()
    {
        $wechat = Wechat::query()->firstOrCreate([], [
            'wechat_id' => 'gh_fabdef6e2911',
            'app_id' => 'wx1120af7462a3189a',
            'app_secret' => '684b481e3f783330b6ae960cb5688a36'
        ]);

        return ['data' => $wechat->menu];
    }

    public function setMenu()
    {

    }
}