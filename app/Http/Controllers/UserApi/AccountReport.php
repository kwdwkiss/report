<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/9
 * Time: 下午10:00
 */

namespace App\Http\Controllers\UserApi;


use App\AccountSearch;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;

class AccountReport extends Controller
{
    public function search()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('user null');
        }
        if (!$user->_profile) {
            throw new JsonException('user profile error');
        }

        $name = request('name');
        if (!$name) {
            throw new JsonException('查询账号为空', 1000);
        }

        if ($user->_profile->amount < 2) {
            throw new JsonException('用户积分不足，请充值', 1001);
        }

        \DB::transaction(function () use ($user, $name) {
            $user->_profile->decrement('amount', 2);

            AccountSearch::create([
                'user_id' => $user->id,
                'type' => 1,
                'name' => $name,
                'ip' => get_client_ip(),
                'success' => 1,
                'result' => ''
            ]);
        });
    }
}