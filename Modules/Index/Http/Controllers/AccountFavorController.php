<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/3
 * Time: 下午10:03
 */

namespace Modules\Index\Http\Controllers;


use Modules\Common\Entities\AccountFavor;
use Modules\Common\Entities\AmountBill;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Entities\Taxonomy;
use Carbon\Carbon;
use Cly\RegExp\RegExp;

class AccountFavorController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        $user = \Auth::guard('user')->user();
        if (!$user) {
            throw new JsonException('用户未登录，请登录后再举报');
        }

        $account_type = request('account_type');
        $account_name = trim(request('account_name'));
        $description = request('description');
        $ip = get_client_ip();

        \DB::transaction(function () use ($user, $account_type, $account_name, $description, $ip) {

            if (!$account_name) {
                throw new JsonException('账号不能为空');
            }
            if ($account_type == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $account_name)) {
                throw new JsonException('QQ号码格式错误');
            }
            if ($account_type == 203 && !preg_match('/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/', $account_name)) {
                throw new JsonException('微信号码格式错误');
            }
            if ($account_type == 204 && !preg_match(RegExp::MOBILE, $account_name)) {
                throw new JsonException('手机号码格式错误');
            }
            Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);

            $userFavor = $user->_favor;
            if (!$userFavor) {
                throw new JsonException('未开通点赞功能，请联系客服');
            }
            if ($userFavor->total <= 0) {
                throw new JsonException('本月点赞次数已用完');
            }

            $todayFavorCount = AccountFavor::query()
                ->where('user_id', $user->id)
                ->where('created_at', '>', Carbon::today())
                ->count();
            if ($todayFavorCount >= 5) {
                throw new JsonException('每天只能点赞5次');
            }

            $oldFavor = AccountFavor::query()
                ->where('account_type', $account_type)
                ->where('account_name', $account_name)
                ->where('user_id', $user->id)
                ->where('created_at', '>', Carbon::now()->subDay(15))
                ->count();
            if ($oldFavor) {
                throw new JsonException('15天后才能对同一账号再次点赞');
            }


            $userFavor->decrement('total');

            AccountFavor::create([
                'user_id' => $user->id,
                'account_type' => $account_type,
                'account_name' => $account_name,
                'ip' => $ip,
                'description' => $description,
            ]);

            //点赞送2积分
            $amount = 2;
            $user->_profile->increment('amount', 2);
            AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'biz_id' => 8,
                'biz_type' => 4,
                'type' => 0,
                'amount' => $amount,
                'description' => "点赞送积分，{$amount}积分",
                'created_at' => Carbon::now()->toDateTimeString(),
            ]);
        });

        return ['message' => '点赞成功，送2积分'];
    }
}