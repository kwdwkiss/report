<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/7
 * Time: 上午10:24
 */

namespace Modules\Agent\Http\Controllers;

use Modules\Common\Entities\BehaviorLog;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;

class AgentController extends Controller
{
    public function info()
    {
        return ['data' => \Auth::guard('agent')->user()];
    }

    public function login()
    {
        $username = request('username');
        $password = request('password');
        $auth_code = request('auth_code');

        $ip = get_client_ip();
        BehaviorLog::create([
            'type' => 6,
            'content' => json_encode([
                'username' => $username,
                'password' => $password,
                'auth_code' => $auth_code,
                'ip' => $ip,
                'geo' => get_geo_str($ip)
            ], JSON_UNESCAPED_UNICODE)
        ]);

        if (env('SITE_AGENT_AUTH_CODE') && env('SITE_AGENT_AUTH_CODE') != $auth_code) {
            throw new JsonException('授权码错误');
        }

        if (\Auth::guard('agent')->attempt([
            'name' => $username,
            'password' => $password
        ])) {
            return [];
        }
        return [
            'code' => 100,
            'message' => '登录失败'
        ];
    }

    public function logout()
    {
        \Auth::guard('agent')->logout();
        return [];
    }

    public function modifyPassword()
    {
        $this->validate(request(), ['newPassword' => 'required|min:8']);

        $user = \Auth::guard('agent')->user();
        if (!\Hash::check(request('password'), $user->password)) {
            throw new JsonException('密码错误');
        }
        $user->update([
            'password' => bcrypt(request('newPassword'))
        ]);
        return [];
    }
}