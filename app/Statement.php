<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $guarded = [];

    public static function day($date)
    {
        $date = $date ? Carbon::parse($date)->toDateString() : Carbon::now()->toDateString();
        $nextDate = Carbon::parse($date)->addDay()->toDateString();

        $user_register = User::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $user_register_inviter = User::query()
            ->whereHas('_profile', function ($query) {
                $query->where('inviter', '!=', '');
            })
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $account_report = AccountReport::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $account_search = AccountSearch::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $subQuery = AccountSearch::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $account_search_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_count = RechargeBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $recharge_money = RechargeBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->sum('money');

        $userIds = RechargeBill::query()
            ->select('user_id')
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->get()->pluck('user_id')->toArray();
        $subQuery = RechargeBill::query()
            ->where('created_at', '<', $nextDate)
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->havingRaw('count(*)=1');
        $recharge_first_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_referer_count = AmountBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('biz_type', 2)
            ->count();

        $recharge_referer_amount = AmountBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('biz_type', 2)
            ->sum('amount');

        $excel_download_count = BehaviorLog::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('type', 2)
            ->count();

        $subQuery = BehaviorLog::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $excel_download_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $excel_save_count = Excel::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $subQuery = Excel::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $excel_save_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        Statement::updateOrCreate([
            'date' => $date,
            'type' => 1,
        ], [
            'user_register' => $user_register,
            'user_register_inviter' => $user_register_inviter,
            'account_report' => $account_report,
            'account_search' => $account_search,
            'account_search_user' => $account_search_user,
            'recharge_count' => $recharge_count,
            'recharge_money' => $recharge_money,
            'recharge_first_user' => $recharge_first_user,
            'recharge_referer_count' => $recharge_referer_count,
            'recharge_referer_amount' => $recharge_referer_amount,
            'excel_download_count' => $excel_download_count,
            'excel_download_user' => $excel_download_user,
            'excel_save_count' => $excel_save_count,
            'excel_save_user' => $excel_save_user,
        ]);
    }

    public static function month($date)
    {
        $date = $date ? Carbon::parse($date)->firstOfMonth()->toDateString()
            : Carbon::now()->firstOfMonth()->toDateString();
        $nextDate = Carbon::parse($date)->addMonth()->toDateString();

        $user_register = User::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $user_register_inviter = User::query()
            ->whereHas('_profile', function ($query) {
                $query->where('inviter', '!=', '');
            })
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $account_report = AccountReport::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $account_search = AccountSearch::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $subQuery = AccountSearch::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $account_search_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_count = RechargeBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $recharge_money = RechargeBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->sum('money');

        $userIds = RechargeBill::query()
            ->select('user_id')
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->get()->pluck('user_id')->toArray();
        $subQuery = RechargeBill::query()
            ->where('created_at', '<', $nextDate)
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->havingRaw('count(*)=1');
        $recharge_first_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_referer_count = AmountBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('biz_type', 2)
            ->count();

        $recharge_referer_amount = AmountBill::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('biz_type', 2)
            ->sum('amount');

        $excel_download_count = BehaviorLog::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->where('type', 2)
            ->count();

        $subQuery = BehaviorLog::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $excel_download_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $excel_save_count = Excel::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->count();

        $subQuery = Excel::query()
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id');
        $excel_save_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        Statement::updateOrCreate([
            'date' => Carbon::parse($date)->format('Y-m'),
            'type' => 2,
        ], [
            'user_register' => $user_register,
            'user_register_inviter' => $user_register_inviter,
            'account_report' => $account_report,
            'account_search' => $account_search,
            'account_search_user' => $account_search_user,
            'recharge_count' => $recharge_count,
            'recharge_money' => $recharge_money,
            'recharge_first_user' => $recharge_first_user,
            'recharge_referer_count' => $recharge_referer_count,
            'recharge_referer_amount' => $recharge_referer_amount,
            'excel_download_count' => $excel_download_count,
            'excel_download_user' => $excel_download_user,
            'excel_save_count' => $excel_save_count,
            'excel_save_user' => $excel_save_user,
        ]);
    }

    public static function profile()
    {
        $row = Statement::query()
            ->where('date', Carbon::yesterday()->toDateString())
            ->first();
        $registerYesterday = $row ? $row->user_register : 0;
        $registerYesterdayInviter = $row ? $row->user_register_inviter : 0;
        $reportYesterday = $row ? $row->account_report : 0;
        $searchYesterday = $row ? $row->account_search : 0;
        $rechargeYesterday = $row ? $row->recharge_money : 0;
        $rechargeYesterdayOnce = $row ? $row->recharge_first_user : 0;

        $row = Statement::query()
            ->where('date', date('Y-m', Carbon::now()->subMonth()->getTimestamp()))
            ->first();
        $registerLastMonth = $row ? $row->user_register : 0;
        $reportLastMonth = $row ? $row->account_report : 0;
        $searchLastMonth = $row ? $row->account_search : 0;
        $rechargeLastMonth = $row ? $row->recharge_money : 0;

        $registerTotal = \Cache::remember('statement.user.register.total', rand(10, 20), function () {
            return User::query()->count();
        });

        $registerToday = \Cache::remember('statement.user.register.today', rand(10, 20), function () {
            return User::query()
                ->where('created_at', '>=', Carbon::today())
                ->count();
        });

        $registerTodayInviter = \Cache::remember('statement.user.register.today_inviter', rand(10, 20), function () {
            return User::query()
                ->whereHas('_profile', function ($query) {
                    $query->where('inviter', '!=', '');
                })
                ->where('created_at', '>=', Carbon::today())
                ->count();
        });

        $registerMonth = \Cache::remember('statement.user.register.month', rand(10, 20), function () {
            return User::query()
                ->where('created_at', '>=', Carbon::now()->firstOfMonth())
                ->count();
        });

        $reportToday = \Cache::remember('statement.user.report.today', rand(10, 20), function () {
            return AccountReport::query()
                ->where('created_at', '>=', Carbon::today())
                ->count();
        });

        $reportMonth = \Cache::remember('statement.user.report.month', rand(10, 20), function () {
            return AccountReport::query()
                ->where('created_at', '>=', Carbon::now()->firstOfMonth())
                ->count();
        });

        $searchToday = \Cache::remember('statement.user.search.today', rand(10, 20), function () {
            return AccountSearch::query()
                ->where('created_at', '>=', Carbon::today())
                ->count();
        });

        $searchMonth = \Cache::remember('statement.user.search.month', rand(10, 20), function () {
            return AccountSearch::query()
                ->where('created_at', '>=', Carbon::now()->firstOfMonth())
                ->count();
        });

        $rechargeToday = \Cache::remember('statement.user.recharge.today', rand(10, 20), function () {
            return RechargeBill::query()
                ->where('created_at', '>=', Carbon::today())
                ->sum('money');
        });

        $rechargeMonth = \Cache::remember('statement.user.recharge.month', rand(10, 20), function () {
            return RechargeBill::query()
                ->where('created_at', '>=', Carbon::now()->firstOfMonth())
                ->sum('money');
        });

        $rechargeTodayOnce = \Cache::remember('statement.user.recharge.today_once', rand(10, 20), function () {
            $todayUserIds = RechargeBill::query()
                ->select('user_id')
                ->where('created_at', '>=', Carbon::today())
                ->get()->pluck('user_id')->toArray();
            $subQuery = RechargeBill::query()
                ->whereIn('user_id', $todayUserIds)
                ->groupBy('user_id')
                ->havingRaw('count(*)=1');
            return \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))->mergeBindings($subQuery->getQuery())->count();
        });

        return [
            'userRegister' => [
                'total' => $registerTotal,
                'today' => $registerToday,
                'yesterday' => $registerYesterday,
                'month' => $registerMonth,
                'lastMonth' => $registerLastMonth,
                'todayInviter' => $registerTodayInviter,
                'yesterdayInviter' => $registerYesterdayInviter
            ],
            'accountReport' => [
                'today' => $reportToday,
                'yesterday' => $reportYesterday,
                'month' => $reportMonth,
                'lastMonth' => $reportLastMonth
            ],
            'accountSearch' => [
                'today' => $searchToday,
                'yesterday' => $searchYesterday,
                'month' => $searchMonth,
                'lastMonth' => $searchLastMonth
            ],
            'rechargeBill' => [
                'today' => $rechargeToday,
                'yesterday' => $rechargeYesterday,
                'month' => $rechargeMonth,
                'lastMonth' => $rechargeLastMonth,
                'todayOnce' => $rechargeTodayOnce,
                'yesterdayOnce' => $rechargeYesterdayOnce
            ]
        ];
    }
}
