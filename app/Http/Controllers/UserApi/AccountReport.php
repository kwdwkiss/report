<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/9
 * Time: 下午10:00
 */

namespace App\Http\Controllers\UserApi;


use App\AccountSearch;
use App\Console\Commands\Account;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;
use App\Http\Resources\AccountResource;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Support\Collection;

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

        $searchUsers = null;
        $accounts = null;
        $accountReports = null;
        \DB::transaction(function () use ($user, $name, &$searchUsers, &$accounts, &$accountReports) {
            $user->_profile->decrement('amount', 2);

            AccountSearch::create([
                'user_id' => $user ? $user->id : 0,
                'type' => 0,
                'name' => $name,
                'ip' => get_client_ip(),
                'success' => 1,
                'result' => ''
            ]);

            $searchUsers = User::query()->with('_profile')
                ->where('mobile', $name)
                ->orWhere('qq', $name)
                ->orWhere('ww', $name)
                ->orWhere('wx', $name)
                ->orWhere('jd', $name)
                ->orWhere('is', $name)
                ->get();

            $accounts = Account::where('name', $name)->get();

            //type 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
            $query = AccountReport::query()->with('_attachments');

            $accountReports = $query
                ->where('account_name', $name)
                ->where('display', 1)
                ->orderBy('created_at', 'desc')->get();

            $userCheckList = [];
            foreach ($searchUsers as $item) {
                if ($item->isCheck()) {
                    if ($item->mobile == $name) {
                        $userCheckList[204] = 1;
                        continue;
                    }
                    if ($item->qq == $name) {
                        $userCheckList[201] = 1;
                        continue;
                    }
                    if ($item->ww == $name) {
                        $userCheckList[202] = 1;
                        continue;
                    }
                    if ($item->wx == $name) {
                        $userCheckList[203] = 1;
                        continue;
                    }
                    if ($item->jd == $name) {
                        $userCheckList[205] = 1;
                        continue;
                    }
                    if ($item->is == $name) {
                        $userCheckList[207] = 1;
                        continue;
                    }
                }
            }

            $accountReportsData = [];
            foreach ($accountReports as $item) {
                if (isset($userCheckList[$item->account_type])) {
                    if ($item->description == null || empty($item->_attachments)) {
                        continue;
                    }
                }
                $accountReportsData[] = $item;
            }
            $accountReports = new Collection($accountReportsData);
        });

        request()->query->set('ip_hide', 1);
        request()->query->set('r_index', true);
        return [
            'data' => [
                'user' => UserResource::collection($searchUsers),
                'accounts' => AccountResource::collection($accounts),
                'account_reports' => AccountReportResource::collection($accountReports)
            ]
        ];
    }
}