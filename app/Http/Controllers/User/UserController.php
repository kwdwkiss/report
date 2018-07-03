<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/7
 * Time: 上午10:24
 */

namespace App\Http\Controllers\User;

use App\AmountBill;
use App\Attachment;
use App\Config;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Sms;
use App\User;
use App\UserMerchant;
use App\UserProfile;
use Carbon\Carbon;
use Cly\RegExp\RegExp;

class UserController extends Controller
{
    public function info()
    {
        $user = \Auth::guard('user')->user();
        return new UserResource($user);
    }

    public function login()
    {
        $remember = request('remember');

        if (\Auth::guard('user')->attempt([
            'mobile' => request('mobile'),
            'password' => request('password')
        ], $remember)) {
            $user = \Auth::guard('user')->user();
            $user->update(['last_ip' => get_client_ip()]);

            return [];
        }
        return [
            'code' => 100,
            'message' => '登录失败'
        ];
    }

    public function logout()
    {
        \Auth::guard('user')->logout();
        return [];
    }

    public function modifyPassword()
    {
        $this->validate(request(), [
            'newPassword' => 'required|min:8'
        ]);
        $user = \Auth::guard('user')->user();
        if (!\Hash::check(request('password'), $user->password)) {
            return [
                'code' => -1,
                'message' => '密码错误'
            ];
        }
        $user->update([
            'password' => bcrypt(request('newPassword'))
        ]);
        return [];
    }

    public function forgetPassword()
    {
        $mobile = request('mobile');
        $password = request('password');
        $code = request('code');
        $remember = request('remember');

        if (!preg_match(RegExp::MOBILE, $mobile)) {
            throw new JsonException('手机号码格式错误');
        }

        if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)(?![^a-zA-Z^\d]+$).{8,16}$/', $password)) {
            throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
        }

        $user = User::where('mobile', $mobile)->first();
        if (!$user) {
            throw new JsonException('用户不存在，请先注册');
        }

        //未使用，未过期的code
        $sms = Sms::where('mobile', $mobile)
            ->where('status', 0)
            ->where('code', $code)
            ->where('expired_at', '>', Carbon::now())
            ->first();
        if (!$sms) {
            throw new JsonException('验证码错误');
        }
        $sms->update(['status' => 1]);

        $user->update(['password' => bcrypt($password)]);

        \Auth::guard('user')->login($user, $remember);

        return [];
    }

    public function register()
    {
        //网站关闭注册
        $closeRegister = Config::get('site.close_register', 0);
        if ($closeRegister) {
            throw new JsonException('网站暂时关闭注册功能，具体开放时间请持续关注网站');
        }

        $mobile = request('mobile');
        $password = request('password');
        $code = request('code');
        $remember = request('remember');
        $inviterMobile = request('inviter', '');

        if (!preg_match(RegExp::MOBILE, $mobile)) {
            throw new JsonException('手机号码格式错误');
        }

        if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)(?![^a-zA-Z^\d]+$).{8,16}$/', $password)) {
            throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
        }

        if ($inviterMobile && !preg_match(RegExp::MOBILE, $inviterMobile)) {
            throw new JsonException('邀请人手机号码格式错误');
        }

        $user = User::where('mobile', $mobile)->first();
        if ($user) {
            throw new JsonException('用户已注册，请找回密码：登录->忘记密码');
        }

        //未使用，未过期的code
        $sms = Sms::where('mobile', $mobile)
            ->where('status', 0)
            ->where('code', $code)
            ->where('expired_at', '>', Carbon::now())
            ->first();
        if (!$sms) {
            throw new JsonException('验证码错误');
        }
        $sms->update(['status' => 1]);

        \DB::transaction(function () use (&$user, $mobile, $password, $inviterMobile, $code) {
            $user = User::create([
                'mobile' => $mobile,
                'type' => 401,
                'password' => bcrypt($password),
                'last_ip' => get_client_ip()
            ]);

            //新用户注册发放200积分
            $amount = 100;

            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 0,
                'amount' => $amount,
                'user_amount' => $userProfile->amount,
                'biz_type' => 0,
                'biz_id' => 4,
                'description' => "新用户注册赠送${amount}积分"
            ]);

            //邀请人发放100积分
            $inviterUser = User::with('_profile')
                ->where('id', '!=', $user->id)//邀请人不能是自己
                ->where('mobile', $inviterMobile)
                ->first();
            if ($inviterUser) {
                $inviterAmount = 50;
                $inviterUser->_profile->increment('amount', $inviterAmount);
                AmountBill::create([
                    'user_id' => $inviterUser->id,
                    'bill_no' => AmountBill::generateBillNo($inviterUser->id),
                    'type' => 0,
                    'amount' => $inviterAmount,
                    'user_amount' => $inviterUser->_profile->amount,
                    'biz_type' => 0,
                    'biz_id' => 5,
                    'description' => "邀请新用户注册赠送${inviterAmount}积分"
                ]);
                $userProfile->update(['inviter' => $inviterMobile]);
            }
        });

        \Auth::guard('user')->login($user, $remember);

        return [];
    }

    public function modify()
    {
        \DB::transaction(function () {
            $qq = request('qq');
            $wx = request('wx');
            $ww = request('ww');
            $jd = request('jd');
            $is = request('is');

            $profileData = request('_profile');
            $name = array_get($profileData, 'name', '');
            $age = array_get($profileData, 'age', '');
            $gender = array_get($profileData, 'gender', 0);
            $occupation = array_get($profileData, 'occupation', '');
            $province = array_get($profileData, 'province', '');
            $city = array_get($profileData, 'city', '');
            $alipay = array_get($profileData, 'alipay', '');
            $alipay_img = array_get($profileData, 'alipay_img', '');
            $identity_code = array_get($profileData, 'identity_code', '');
            $identity_front_img = array_get($profileData, 'identity_front_img', '');
            $identity_back_img = array_get($profileData, 'identity_back_img', '');

            $user = \Auth::guard('user')->user();
            $profile = $user->_profile;
            if (!$profile) {
                throw new JsonException('user_profile数据异常，请联系客服');
            }
            if ($profile->user_lock) {
                throw new JsonException('个人资料锁定，修改请联系客服');
            }

            if ($qq) {
                if (!preg_match('/^[1-9][0-9]{4,14}$/', $qq)) {
                    throw new JsonException('QQ号码格式错误');
                }
                if (!preg_match('/^[1-9][0-9]{4,14}$/', $qq)) {
                    throw new JsonException('QQ号错误');
                }
                $exists = User::where('qq', $qq)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('QQ已存在，请联系客服处理');
                }
            } else {
                $qq = null;
            }

            if ($wx) {
                if (!preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/', $wx)) {
                    throw new JsonException('微信号码格式错误');
                }
                $exists = User::where('wx', $wx)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('微信已存在，请联系客服处理');
                }
            } else {
                $wx = null;
            }

            if ($ww) {
                $exists = User::where('ww', $ww)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('旺旺已存在，请联系客服处理');
                }
            } else {
                $ww = null;
            }

            if ($jd) {
                $exists = User::where('jd', $jd)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('京东已存在，请联系客服处理');
                }
            } else {
                $jd = null;
            }

            if ($is) {
                $exists = User::where('is', $is)->first();
                if ($exists && $exists->id != $user->id) {
                    throw new JsonException('IS已存在，请联系客服处理');
                }
            } else {
                $is = null;
            }

            if (!in_array($gender, [0, 1, 2,])) {
                throw new JsonException('性别错误');
            }

            $user->update([
                'qq' => $qq,
                'wx' => $wx,
                'ww' => $ww,
                'jd' => $jd,
                'is' => $is
            ]);

            $profile->update([
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
            ]);
        });

        return [];
    }

    public function merchantModify()
    {
        $type = request('type');
        $name = request('name', '');
        $goods_type = request('goods_type', '');
        $url = request('url', '');
        $credit = request('credit', '');
        $manager = request('manager', '');

        if (!in_array($type, [1, 2, 3])) {
            throw new JsonException('店铺类型错误');
        }

        $user = \Auth::guard('user')->user();
        $merchant = $user->_merchant;

        if ($merchant) {
            if ($merchant->user_lock) {
                throw new JsonException('资料已锁定，若需修改，请联系客服');
            }
            $merchant->update([
                'type' => $type,
                'name' => $name,
                'goods_type' => $goods_type,
                'url' => $url,
                'credit' => $credit,
                'manager' => $manager
            ]);
        } else {
            UserMerchant::create([
                'user_id' => $user->id,
                'type' => $type,
                'name' => $name,
                'goods_type' => $goods_type,
                'url' => $url,
                'credit' => $credit,
                'manager' => $manager
            ]);
        }

        return [];
    }

    public function sms()
    {
        $mobile = request('mobile');
        $action = request('action');

        if (!preg_match(RegExp::MOBILE, $mobile)) {
            throw new JsonException('手机号码格式错误');
        }
        if ($action && $action == 'register') {
            $user = User::where('mobile', $mobile)->first();
            if ($user) {
                throw new JsonException('用户已注册，请找回密码：登录->忘记密码');
            }
        }
        if ($action && $action == 'forget') {
            $user = User::where('mobile', $mobile)->first();
            if (!$user) {
                throw new JsonException('用户不存在，请先注册');
            }
        }

        $ipCount = Sms::where('ip', request()->getClientIp())
            ->where('created_at', '>', Carbon::now()->subMinutes(30))
            ->count();
        if ($ipCount >= 30) {
            return [
                'code' => -1,
                'message' => '一个ip30分钟内只能发30次'
            ];
        }

        //60秒内，未过期的，未使用的code
        $sms = Sms::where('mobile', $mobile)
            ->where('status', 0)//未使用
            ->where('expired_at', '>', Carbon::now())
            ->where('created_at', '>', Carbon::now()->subSeconds(60))->first();

        if ($sms) {
            $leftTime = $sms->created_at->timestamp + 60 - Carbon::now()->timestamp;
            return [
                'code' => -1,
                'message' => "还剩${leftTime}秒才能发送"
            ];
        }

        $code = random_int(1000, 9999);

        if (env('ALIYUN_SMS_SEND_ENABLE', true)) {
            $result = app('aliyun.sms')->send($mobile, $code);
        } else {
            $result = ['success' => true];//调试，开发时不发送短信
        }

        if ($result['success']) {
            $sms = Sms::create([
                'mobile' => $mobile,
                'code' => $code,
                'status' => 0,
                'expired_at' => Carbon::now()->addMinutes(5),
                'ip' => request()->getClientIp(),
                'result' => json_encode($result)
            ]);

            return [
                'code' => 0,
                'message' => '发送验证码成功',
                'data' => [
                    'mobile' => $sms->mobile,
                    'expired_at' => $sms->expired_at
                ]
            ];
        } else {
            $sms = Sms::create([
                'mobile' => $mobile,
                'code' => $code,
                'status' => -1,
                'expired_at' => Carbon::now()->addMinutes(5),
                'ip' => request()->getClientIp(),
                'result' => json_encode($result)
            ]);

            return [
                'code' => -1,
                'message' => '发送验证码失败，请稍后再试，如果多次尝试未能成功，请联系客服。',
                'data' => [
                    'mobile' => $sms->mobile,
                    'expired_at' => $sms->expired_at,
                    'result' => $result
                ]
            ];
        }
    }
}