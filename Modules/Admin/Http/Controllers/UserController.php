<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 下午3:11
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\DepositBill;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\UserResource;
use Modules\Common\Entities\Taxonomy;
use Modules\Common\Entities\User;
use Modules\Common\Entities\UserFavor;
use Modules\Common\Entities\UserMerchant;
use Modules\Common\Entities\UserProfile;
use Carbon\Carbon;
use Cly\RegExp\RegExp;

class UserController extends Controller
{
    public function index()
    {
        $type = request('type');
        $mobile = request('mobile');
        $qq = request('qq');
        $wx = request('wx');
        $ww = request('ww');
        $jd = request('jd');
        $is = request('is');

        $query = User::with('_profile', '_type', '_auth_type', '_merchant', '_favor')->orderBy('id', 'desc');
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
            $query->where('ww', $ww);
        }
        if ($jd) {
            $query->where('jd', $jd);
        }
        if ($is) {
            $query->where('is', $is);
        }

        return UserResource::collection($query->paginate());
    }

    public function show()
    {
        $user = User::with('_profile', '_type', '_merchant', '_favor')->findOrFail(request('id'));

        return new UserResource($user);
    }

    public function create()
    {
        $password = request('password');
        $mobile = request('mobile');
        $inviterMobile = request('inviter', '');

        if ($mobile) {
            //带+为国际号码，不是中国的不处理
            if (strpos($mobile, '+') === 0 && strpos($mobile, '+86') !== 0) {

            } elseif (!preg_match(RegExp::MOBILE, $mobile)) {
                throw new JsonException('手机号错误');
            }
            $exists = User::where('mobile', $mobile)->first();
            if ($exists) {
                throw new JsonException('手机号码已存在');
            }
        } else {
            throw new JsonException('手机号不能为空');
        }
        if ($password) {
            if (!preg_match(RegExp::PASSWORD, $password)) {
                throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
            }
            $createData['password'] = bcrypt($password);
        }
        if ($inviterMobile && !preg_match(RegExp::MOBILE, $inviterMobile)) {
            throw new JsonException('邀请人手机号码格式错误');
        }

        User::register($mobile, $password, $inviterMobile);

        return [];
    }

    public function update()
    {
        $user = null;
        \DB::transaction(function () use (&$user) {
            $password = request('password');
            $mobile = request('mobile');
            $qq = request('qq');
            $wx = request('wx');
            $ww = request('ww');
            $jd = request('jd');
            $is = request('is');
            $deny_login = request('deny_login', 0);

            $user = User::with('_profile', '_merchant')->findOrFail(request('id'));
            if (!$mobile) {
                throw new JsonException('手机号不为空');
            }
            if ($mobile) {
                if (!preg_match(RegExp::MOBILE, $mobile)) {
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
            if ($jd) {
                $exists = User::where('jd', $jd)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('京东已存在');
                }
            } else {
                $jd = null;
            }
            if ($is) {
                $exists = User::where('is', $is)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('IS已存在');
                }
            } else {
                $is = null;
            }
            $updateData = [
                'mobile' => $mobile,
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
                'jd' => $jd,
                'is' => $is,
                'deny_login' => $deny_login
            ];
            if ($password) {
                if (!preg_match(RegExp::PASSWORD, $password)) {
                    throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
                }
                $updateData['password'] = bcrypt($password);
            }
            $user->update($updateData);

            $profileData = request('_profile');
            $name = array_get($profileData, 'name', '');
            $age = array_get($profileData, 'age', '');
            $gender = array_get($profileData, 'gender', 0);
            $occupation = array_get($profileData, 'occupation', '');
            $province = array_get($profileData, 'province', '');
            $city = array_get($profileData, 'city', '');
            $remark = array_get($profileData, 'remark', '');
            $alipay = array_get($profileData, 'alipay', '');
            $alipay_img = array_get($profileData, 'alipay_img', '');
            $identity_code = array_get($profileData, 'identity_code', '');
            $identity_front_img = array_get($profileData, 'identity_front_img', '');
            $identity_back_img = array_get($profileData, 'identity_back_img', '');
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
                'alipay_img' => $alipay_img,
                'identity_code' => $identity_code,
                'identity_front_img' => $identity_front_img,
                'identity_back_img' => $identity_back_img,
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

        if ($user->deny_login) {
            \Auth::guard('user')->login($user);
            \Auth::guard('user')->logout();
        }

        return [];
    }

    public function enableFavor()
    {
        $id = request('id');

        $user = User::findOrfail($id);
        $admin = \Auth::guard('admin')->user();

        if ($user->_favor) {
            throw new JsonException('用户已经开通了');
        }

        UserFavor::create([
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'total' => UserFavor::$total,
        ]);

        return [];
    }

    public function updateAuth()
    {
        //todo:delete
        //throw new JsonException('此方法已废弃');

        $auth_duration = (int)request('auth_duration');
        if ($auth_duration < 0) {
            throw new JsonException('auth_duration error');
        }
        $auth_type = request('auth_type');
        Taxonomy::where('pid', Taxonomy::USER_TYPE)->findOrFail($auth_type);
        $user = User::findOrFail(request('id'));

        $auth_start_at = Carbon::now();
        $auth_end_at = Carbon::now()->addMonths($auth_duration);
        $user->update([
            'type' => $auth_type,
            'auth_type' => $auth_type,
            'auth_duration' => $auth_duration,
            'auth_start_at' => $auth_start_at,
            'auth_end_at' => $auth_end_at
        ]);

        $user->_profile->update([
            'user_lock' => 1,
        ]);

        return [];
    }

    public function updateApiKey()
    {
        $user = User::findOrFail(request('id'));
        $user->updateApiKey();
        return [];
    }

    public function updateApiSecret()
    {
        $user = User::findOrFail(request('id'));
        $user->updateApiSecret();
        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $user = User::with('_profile')->findOrFail(request('id'));
            $user->syncTag([]);
            $user->_profile->delete();

            $merchant = $user->_merchant;
            if ($merchant) {
                $merchant->delete();
            }

            $user->delete();
        });

        return [];
    }

    public function addDeposit()
    {
        $deposit = request('deposit');

        $user = User::findOrFail(request('id'));

        if ($deposit < 0) {
            throw new JsonException('保证金数值不能为负');
        }

        \DB::transaction(function () use ($deposit, $user) {
            $user->_profile->increment('deposit', $deposit);

            DepositBill::create([
                'user_id' => $user->id,
                'type' => 0,
                'deposit' => $deposit
            ]);
        });

        return [];
    }

    public function subDeposit()
    {
        $deposit = request('deposit');

        $user = User::findOrFail(request('id'));

        if ($deposit < 0) {
            throw new JsonException('保证金数值不能为负');
        }

        if ($user->_profile->deposit < $deposit) {
            throw new JsonException('保证金不足');
        }

        \DB::transaction(function () use ($deposit, $user) {
            $user->_profile->decrement('deposit', $deposit);

            DepositBill::create([
                'user_id' => $user->id,
                'type' => 1,
                'deposit' => $deposit
            ]);
        });

        return [];
    }
}