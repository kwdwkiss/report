<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 下午3:11
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Tag;
use App\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function list()
    {
        $mobile = request('mobile');
        $qq = request('qq');
        $wx = request('wx');

        $query = UserProfile::query()->with('_tags');
        if ($mobile) {
            $query->where('mobile', $mobile);
        }
        if ($qq) {
            $query->where('qq', $qq);
        }
        if ($wx) {
            $query->where('wx', $wx);
        }

        return UserProfileResource::collection($query->paginate());
    }

    public function create()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            $request = new Request($input);
            $this->validate($request, [
                'name' => 'required',
                'age' => 'required',
                'gender' => 'required',
                'mobile' => 'required',
                'qq' => 'required',
                'wx' => 'required',
                'occupation' => 'required',
                'province' => 'required',
                'city' => 'required',
            ]);

            $name = array_get($input, 'name');
            $age = array_get($input, 'age');
            $gender = array_get($input, 'gender');
            $mobile = array_get($input, 'mobile');
            $qq = array_get($input, 'qq');
            $wx = array_get($input, 'wx');
            $occupation = array_get($input, 'occupation');
            $province = array_get($input, 'province');
            $city = array_get($input, 'city');
            $tags = array_get($input, 'tags');

            $userProfile = new UserProfile();

            if ($name) {
                $userProfile->name = $name;
            }
            if ($age) {
                $userProfile->age = $age;
            }
            if ($gender) {
                $userProfile->gender = $gender;
            }
            if ($mobile) {
                $exists = UserProfile::where('mobile', request('mobile'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('手机号码已存在');
                }
                $userProfile->mobile = $mobile;
            }
            if ($qq) {
                $exists = UserProfile::where('qq', request('qq'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('QQ已存在');
                }
                $userProfile->qq = $qq;
            }
            if ($wx) {
                $exists = UserProfile::where('wx', request('wx'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('微信已存在');
                }
                $userProfile->wx = $wx;
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
            if ($tags) {
                foreach ($tags as $tag_id) {
                    Tag::findOrFail($tag_id);
                }
            }

            $userProfile->save();
            $userProfile->syncTag($tags);
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            $name = array_get($input, 'name');
            $age = array_get($input, 'age');
            $gender = array_get($input, 'gender');
            $mobile = array_get($input, 'mobile');
            $qq = array_get($input, 'qq');
            $wx = array_get($input, 'wx');
            $occupation = array_get($input, 'occupation');
            $province = array_get($input, 'province');
            $city = array_get($input, 'city');
            $tags = array_get($input, 'tags');

            $userProfile = UserProfile::findOrFail(request('id'));

            if ($name) {
                $userProfile->name = $name;
            }
            if ($age) {
                $userProfile->age = $age;
            }
            if ($gender) {
                $userProfile->gender = $gender;
            }
            if ($mobile) {
                $exists = UserProfile::where('mobile', request('mobile'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('手机号码已存在');
                }
                $userProfile->mobile = $mobile;
            }
            if ($qq) {
                $exists = UserProfile::where('qq', request('qq'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('QQ已存在');
                }
                $userProfile->qq = $qq;
            }
            if ($wx) {
                $exists = UserProfile::where('wx', request('wx'))->first();
                if ($exists && $exists->id != $userProfile->id) {
                    throw new \Exception('微信已存在');
                }
                $userProfile->wx = $wx;
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
            if ($tags) {
                foreach ($tags as $tag_id) {
                    Tag::findOrFail($tag_id);
                }
            }

            $userProfile->save();
            $userProfile->syncTag($tags);
        });

        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $userProfile = UserProfile::findOrFail(request('id'));
            $userProfile->syncTag([]);
            $userProfile->delete();
        });

        return [];
    }
}