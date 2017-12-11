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
            $input = json_decode(request()->getContent(), true);
            $request = new Request($input);
            $this->validate($request, ['mobile' => 'required',]);

            $type = array_get($input, 'type');
            $mobile = trim(array_get($input, 'mobile'));
            $qq = trim(array_get($input, 'qq'));
            $wx = trim(array_get($input, 'wx'));
            $ww = trim(array_get($input, 'ww'));

            $name = trim(array_get($input, '_profile.name'));
            $age = array_get($input, '_profile.age');
            $gender = array_get($input, '_profile.gender');
            $occupation = array_get($input, '_profile.occupation');
            $province = array_get($input, '_profile.province');
            $city = array_get($input, '_profile.city');
            $remark = array_get($input, '_profile.remark');

            $tags = array_get($input, 'tags', []);

            $user = new User();
            $userProfile = new userProfile();

            Taxonomy::where('pid', Taxonomy::USER_TYPE)->findOrFail($type);
            $user->type = $type;

            if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
                throw new JsonException('手机号错误');
            }
            $exists = User::where('mobile', $mobile)->first();
            if ($exists && $exists->id != $user->id) {
                throw new \Exception('手机号码已存在');
            }
            $user->mobile = $mobile;

            if ($qq) {
                if (!preg_match('/[1-9][0-9]{4,14}/', $qq)) {
                    throw new JsonException('QQ号错误');
                }
                $exists = User::where('qq', $qq)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('QQ已存在');
                }
                $user->qq = $qq;
            }
            if ($wx) {
                $exists = User::where('wx', $wx)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('微信已存在');
                }
                $user->wx = $wx;
            }
            if ($ww) {
                $exists = User::where('ww', $ww)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('旺旺已存在');
                }
                $user->ww = $ww;
            }

            if ($name) {
                $userProfile->name = $name;
            }
            if ($age) {
                $userProfile->age = $age;
            }
            if ($gender) {
                $userProfile->gender = $gender;
            }
            if ($occupation) {
                $userProfile->occupation = $occupation;
            }
            if ($province) {
                $userProfile->province = $province;
            }
            if ($city) {
                $userProfile->city = $city;
            }
            if ($remark) {
                $userProfile->remark = $remark;
            }
            if ($tags) {
                foreach ($tags as $tag_id) {
                    Tag::findOrFail($tag_id);
                }
            }

            $user->save();
            $user->_profile()->save($userProfile);
            $user->syncTag($tags);
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            $request = new Request($input);
            $this->validate($request, ['mobile' => 'required',]);

            $type = array_get($input, 'type');
            $mobile = trim(array_get($input, 'mobile'));
            $qq = trim(array_get($input, 'qq'));
            $wx = trim(array_get($input, 'wx'));
            $ww = trim(array_get($input, 'ww'));

            $name = trim(array_get($input, '_profile.name'));
            $age = array_get($input, '_profile.age');
            $gender = array_get($input, '_profile.gender');
            $occupation = array_get($input, '_profile.occupation');
            $province = array_get($input, '_profile.province');
            $city = array_get($input, '_profile.city');
            $remark = array_get($input, '_profile.remark');

            $tags = array_get($input, 'tags', []);

            $user = User::with('_profile')->findOrFail(request('id'));
            $userProfile = $user->_profile;

            Taxonomy::where('pid', Taxonomy::USER_TYPE)->findOrFail($type);
            $user->type = $type;

            if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
                throw new JsonException('手机号错误');
            }
            $exists = User::where('mobile', $mobile)->first();
            if ($exists && $exists->id != $user->id) {
                throw new \Exception('手机号码已存在');
            }
            $user->mobile = $mobile;

            if ($qq) {
                if (!preg_match('/[1-9][0-9]{4,14}/', $qq)) {
                    throw new JsonException('QQ号错误');
                }
                $exists = User::where('qq', $qq)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('QQ已存在');
                }
                $user->qq = $qq;
            }
            if ($wx) {
                $exists = User::where('wx', $wx)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('微信已存在');
                }
                $user->wx = $wx;
            }
            if ($ww) {
                $exists = User::where('ww', $ww)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new \Exception('旺旺已存在');
                }
                $user->ww = $ww;
            }

            if ($name) {
                $userProfile->name = $name;
            }
            if ($age) {
                $userProfile->age = $age;
            }
            if ($gender) {
                $userProfile->gender = $gender;
            }
            if ($occupation) {
                $userProfile->occupation = $occupation;
            }
            if ($province) {
                $userProfile->province = $province;
            }
            if ($city) {
                $userProfile->city = $city;
            }
            if ($remark) {
                $userProfile->remark = $remark;
            }
            if ($tags) {
                foreach ($tags as $tag_id) {
                    Tag::findOrFail($tag_id);
                }
            }

            $user->save();
            $user->syncTag($tags);
            $userProfile->save();
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