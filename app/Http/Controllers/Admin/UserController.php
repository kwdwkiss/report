<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 下午3:11
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Taxonomy;
use App\User;
use App\UserMerchant;
use App\UserProfile;

class UserController extends Controller
{
    public function list()
    {
        $type = request('type');
        $mobile = request('mobile');
        $qq = request('qq');
        $wx = request('wx');
        $ww = request('ww');

        $query = User::with('_profile', '_type', '_merchant')->orderBy('id', 'desc');
        if ($type) {
            $query->where('type', $type);
        }
        if ($mobile) {
            $query->where('mobile', $mobile);
        }
        if ($qq) {
            $query->where('qq', $qq);
        }
        if ($wx) {
            $query->where('wx', $wx);
        }
        if ($ww) {
            $query->where('wx', $ww);
        }

        return UserResource::collection($query->paginate());
    }

    public function show()
    {
        $user = User::with('_profile', '_type', '_merchant')->findOrFail(request('id'));

        return new UserResource($user);
    }

    public function create()
    {
        \DB::transaction(function () {
            $type = request('type');
            $mobile = request('mobile');
            $qq = request('qq');
            $wx = request('wx');
            $ww = request('ww');

            Taxonomy::where('pid', Taxonomy::USER_TYPE)->findOrFail($type);
            if ($mobile) {
                if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
                    throw new JsonException('手机号错误');
                }
                $exists = User::where('mobile', $mobile)->first();
                if ($exists) {
                    throw new JsonException('手机号码已存在');
                }
            } else {
                throw new JsonException('手机号不能为空');
            }
            if ($qq) {
                if (!preg_match('/^[1-9][0-9]{4,14}$/', $qq)) {
                    throw new JsonException('QQ号错误');
                }
                $exists = User::where('qq', $qq)->first();
                if ($exists) {
                    throw new JsonException('QQ已存在');
                }
            } else {
                $qq = null;
            }

            if ($wx) {
                $exists = User::where('wx', $wx)->first();
                if ($exists) {
                    throw new JsonException('微信已存在');
                }
            } else {
                $wx = null;
            }

            if ($ww) {
                $exists = User::where('ww', $ww)->first();
                if ($exists) {
                    throw new JsonException('旺旺已存在');
                }
            } else {
                $ww = null;
            }
            $user = User::create([
                'type' => $type,
                'mobile' => $mobile,
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
            ]);

            $profileData = request('_profile');
            $name = array_get($profileData, 'name', '');
            $age = array_get($profileData, 'age', '');
            $gender = array_get($profileData, 'gender', 0);
            $occupation = array_get($profileData, 'occupation', '');
            $province = array_get($profileData, 'province', '');
            $city = array_get($profileData, 'city', '');
            $remark = array_get($profileData, 'remark', '');
            $alipay = array_get($profileData, 'alipay', '');

            if (!in_array($gender, [0, 1, 2,])) {
                throw new JsonException('性别错误');
            }
            UserProfile::create([
                'user_id' => $user->id,
                'name' => $name,
                'age' => $age,
                'gender' => $gender,
                'occupation' => $occupation,
                'province' => $province,
                'city' => $city,
                'alipay' => $alipay,
                'remark' => $remark
            ]);

            $merchantData = request('_merchant');
            $merchant_type = array_get($merchantData, 'type');
            $merchant_name = array_get($merchantData, 'name', '');
            $merchant_goods_type = array_get($merchantData, 'goods_type', '');
            $merchant_url = array_get($merchantData, 'url', '');
            $merchant_credit = array_get($merchantData, 'credit', '');
            $merchant_manager = array_get($merchantData, 'manager', '');
            $merchant_user_lock = array_get($merchantData, 'user_lock', 0);

            if (!in_array($merchant_type, [1, 2, 3])) {
                throw new JsonException('店铺类型错误');
            }
            if (!in_array($merchant_user_lock, [0, 1])) {
                throw new JsonException('锁错误');
            }
            UserMerchant::create([
                'user_id' => $user->id,
                'type' => $merchant_type,
                'name' => $merchant_name,
                'goods_type' => $merchant_goods_type,
                'url' => $merchant_url,
                'credit' => $merchant_credit,
                'manager' => $merchant_manager,
                'user_lock' => $merchant_user_lock,
            ]);
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $type = request('type');
            $mobile = request('mobile');
            $qq = request('qq');
            $wx = request('wx');
            $ww = request('ww');

            $user = User::with('_profile', '_merchant')->findOrFail(request('id'));
            Taxonomy::where('pid', Taxonomy::USER_TYPE)->findOrFail($type);
            if ($mobile) {
                if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
                    throw new JsonException('手机号错误');
                }
                $exists = User::where('mobile', $mobile)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('手机号码已存在');
                }
            }
            if ($qq) {
                if (!preg_match('/^[1-9][0-9]{4,14}$/', $qq)) {
                    throw new JsonException('QQ号错误');
                }
                $exists = User::where('qq', $qq)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('QQ已存在');
                }
            } else {
                $qq = null;
            }
            if ($wx) {
                $exists = User::where('wx', $wx)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('微信已存在');
                }
            } else {
                $wx = null;
            }
            if ($ww) {
                $exists = User::where('ww', $ww)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('旺旺已存在');
                }
            } else {
                $ww = null;
            }
            $user->update([
                'type' => $type,
                'mobile' => $mobile,
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
            ]);

            $profileData = request('_profile');
            $name = array_get($profileData, 'name', '');
            $age = array_get($profileData, 'age', '');
            $gender = array_get($profileData, 'gender', 0);
            $occupation = array_get($profileData, 'occupation', '');
            $province = array_get($profileData, 'province', '');
            $city = array_get($profileData, 'city', '');
            $remark = array_get($profileData, 'remark', '');
            $alipay = array_get($profileData, 'alipay', '');
            $user_lock = array_get($profileData, 'user_lock', 0);

            if (!in_array($gender, [0, 1, 2,])) {
                throw new JsonException('性别错误');
            }
            $user->_profile->update([
                'name' => $name,
                'age' => $age,
                'gender' => $gender,
                'occupation' => $occupation,
                'province' => $province,
                'city' => $city,
                'alipay' => $alipay,
                'remark' => $remark,
                'user_lock' => $user_lock
            ]);


            $merchantData = request('_merchant');
            $merchant_type = array_get($merchantData, 'type', 0);
            $merchant_name = array_get($merchantData, 'name', '');
            $merchant_goods_type = array_get($merchantData, 'goods_type', '');
            $merchant_url = array_get($merchantData, 'url', '');
            $merchant_credit = array_get($merchantData, 'credit', '');
            $merchant_manager = array_get($merchantData, 'manager', '');
            $merchant_user_lock = array_get($merchantData, 'user_lock', 0);

            if (!in_array($merchant_type, [0, 1, 2, 3])) {
                throw new JsonException('店铺类型错误');
            }
            if (!in_array($merchant_user_lock, [0, 1])) {
                throw new JsonException('锁错误');
            }
            $merchant = $user->_merchant;
            if ($merchant) {
                $merchant->update([
                    'type' => $merchant_type,
                    'name' => $merchant_name,
                    'goods_type' => $merchant_goods_type,
                    'url' => $merchant_url,
                    'credit' => $merchant_credit,
                    'manager' => $merchant_manager,
                    'user_lock' => $merchant_user_lock,
                ]);
            } else {
                UserMerchant::create([
                    'user_id' => $user->id,
                    'type' => $merchant_type,
                    'name' => $merchant_name,
                    'goods_type' => $merchant_goods_type,
                    'url' => $merchant_url,
                    'credit' => $merchant_credit,
                    'manager' => $merchant_manager,
                    'user_lock' => $merchant_user_lock,
                ]);
            }
        });

        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $user = User::with('_profile')->findOrFail(request('id'));
            $user->syncTag([]);
            $user->_profile->delete();
            $user->delete();
        });

        return [];
    }
}