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
use App\Tag;
use App\Taxonomy;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function list()
    {
        $type = request('type');
        $mobile = request('mobile');
        $qq = request('qq');
        $wx = request('wx');
        $ww = request('ww');

        $query = User::with('_profile', '_type')->orderBy('created_at', 'desc');
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

    public function create()
    {
        \DB::transaction(function () {
            $type = request('type');
            $mobile = request('mobile');
            $qq = request('qq');
            $wx = request('wx');
            $ww = request('ww');

            $name = request('_profile.name', '');
            $age = request('_profile.age', '');
            $gender = request('_profile.gender', 0);
            $occupation = request('_profile.occupation', '');
            $province = request('_profile.province', '');
            $city = request('_profile.city', '');
            $remark = request('_profile.remark', '');
            $alipay = request('_profile.alipay', '');

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

            if (!in_array($gender, [0, 1, 2,])) {
                throw new JsonException('性别错误');
            }

            $user = User::create([
                'type' => $type,
                'mobile' => $mobile,
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
            ]);

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

            $name = request('_profile.name', '');
            $age = request('_profile.age', '');
            $gender = request('_profile.gender', 0);
            $occupation = request('_profile.occupation', '');
            $province = request('_profile.province', '');
            $city = request('_profile.city', '');
            $remark = request('_profile.remark', '');
            $alipay = request('_profile.alipay', '');

            $user = User::with('_profile')->findOrFail(request('id'));

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

            if (!in_array($gender, [0, 1, 2,])) {
                throw new JsonException('性别错误');
            }

            $user->update([
                'type' => $type,
                'mobile' => $mobile,
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
            ]);

            $user->_profile->update([
                'name' => $name,
                'age' => $age,
                'gender' => $gender,
                'occupation' => $occupation,
                'province' => $province,
                'city' => $city,
                'alipay' => $alipay,
                'remark' => $remark
            ]);
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