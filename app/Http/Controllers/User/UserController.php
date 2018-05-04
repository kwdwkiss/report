<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/7
 * Time: 上午10:24
 */

namespace App\Http\Controllers\User;

use App\AmountBill;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Sms;
use App\User;
use App\UserProfile;
use Carbon\Carbon;

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

        if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
            throw new JsonException('手机号码格式错误');
        }

        if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)(?![^a-zA-Z^\d]+$).{8,16}$/', $password)) {
            throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
        }

        //未使用，未过期的code
        $sms = Sms::where('mobile', $mobile)
            ->where('status', 0)
            ->where('code', $code)
            ->where('expired_at', '>', Carbon::now())
            ->first();

        if (!$sms) {
            new JsonException('验证码错误');
        }

        $sms->update(['status' => 1]);

        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            throw new JsonException('用户不存在');
        }

        $user->update(['password' => bcrypt($password)]);

        \Auth::guard('user')->login($user, $remember);

        return [];
    }

    public function register()
    {
        $mobile = request('mobile');
        $password = request('password');
        $code = request('code');
        $remember = request('remember');

        if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
            throw new JsonException('手机号码格式错误');
        }

        if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)(?![^a-zA-Z^\d]+$).{8,16}$/', $password)) {
            throw new JsonException('密码必须包含字母、数字、符号两种组合且长度为8-16');
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

        $user = User::where('mobile', $mobile)->first();

        //忘记密码
        if ($user) {
            $user->update(['password' => bcrypt($password)]);
        } else {//注册
            $inviter = request('inviter', '');
            if ($inviter && !preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $inviter)) {
                return [
                    'code' => -1,
                    'message' => '邀请人手机号码格式错误'
                ];
            }

            $user = null;
            \DB::transaction(function () use ($mobile, $password, $inviter, &$user) {
                $user = User::create([
                    'mobile' => $mobile,
                    'type' => 401,
                    'password' => bcrypt($password)
                ]);

                //新用户注册发放200积分
                $amount = 200;
                AmountBill::create([
                    'user_id' => $user->id,
                    'bill_no' => AmountBill::generateBillNo($user->id),
                    'type' => 0,
                    'amount' => $amount,
                    'biz_type' => 0,
                    'biz_id' => 1,
                    'description' => "新用户注册赠送${amount}积分"
                ]);

                //邀请人发放100积分
                if ($mobile == $inviter) {
                    $inviter = '';
                } else {
                    $inviterUser = User::with('_profile')->where('mobile', $inviter)->first();
                    if ($inviterUser) {
                        $inviterAmount = 100;
                        $inviterUser->_profile->increment('amount', $inviterAmount);
                        AmountBill::create([
                            'user_id' => $inviterUser->id,
                            'bill_no' => AmountBill::generateBillNo($inviterUser->id),
                            'type' => 0,
                            'amount' => $inviterAmount,
                            'biz_type' => 0,
                            'biz_id' => 2,
                            'description' => "邀请新用户注册赠送${inviterAmount}积分"
                        ]);
                    }
                }

                UserProfile::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'inviter' => $inviter
                ]);
            });
        }
        \Auth::guard('user')->login($user, $remember);

        return [
            'code' => 0,
            'message' => '成功'
        ];
    }

    public function sms()
    {
        $mobile = request('mobile');

        if (!preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $mobile)) {
            return [
                'code' => -1,
                'message' => '手机号码格式错误'
            ];
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