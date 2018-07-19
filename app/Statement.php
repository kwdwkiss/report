<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    protected $guarded = [];

    public static function day($date)
    {
        $date = $date ? Carbon::parse($date)->toDateString() : Carbon::yesterday()->toDateString();

        $user_register = User::query()
            ->whereDate('created_at', $date)
            ->count();

        $user_register_inviter = User::query()
            ->whereHas('_profile', function ($query) {
                $query->where('inviter', '!=', '');
            })
            ->whereDate('created_at', $date)
            ->count();

        $account_report = AccountReport::query()
            ->whereDate('created_at', $date)
            ->count();

        $account_search = AccountSearch::query()
            ->whereDate('created_at', $date)
            ->count();

        $subQuery = AccountSearch::query()
            ->whereDate('created_at', $date)
            ->groupBy('user_id');
        $account_search_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_count = RechargeBill::query()
            ->whereDate('created_at', $date)
            ->count();

        $recharge_money = RechargeBill::query()
            ->whereDate('created_at', $date)
            ->sum('money');

        $userIds = RechargeBill::query()
            ->select('user_id')
            ->whereDate('created_at', $date)
            ->get()->pluck('user_id')->toArray();
        $subQuery = RechargeBill::query()
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->havingRaw('count(*)=1');
        $recharge_first_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $recharge_referer_count = AmountBill::query()
            ->whereDate('created_at', $date)
            ->where('biz_type', 2)
            ->count();

        $recharge_referer_amount = AmountBill::query()
            ->whereDate('created_at', $date)
            ->where('biz_type', 2)
            ->sum('amount');

        $excel_download_count = BehaviorLog::query()
            ->whereDate('created_at', $date)
            ->where('type', 2)
            ->count();

        $subQuery = BehaviorLog::query()
            ->whereDate('created_at', $date)
            ->groupBy('user_id');
        $excel_download_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))
            ->mergeBindings($subQuery->getQuery())->count();

        $excel_save_count = Excel::query()
            ->whereDate('created_at', $date)
            ->count();

        $subQuery = Excel::query()
            ->whereDate('created_at', $date)
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

    public static function profile($refresh = false)
    {
        if ($refresh) {
            \Cache::forget('statement.user.register.total');
            \Cache::forget('statement.user.register.today');
            \Cache::forget('statement.user.register.today_inviter');
            \Cache::forget('statement.user.register.month');
            \Cache::forget('statement.user.register.last_month');
            \Cache::forget('statement.user.report.today');
            \Cache::forget('statement.user.report.month');
            \Cache::forget('statement.user.report.last_month');
            \Cache::forget('statement.user.recharge.today');
            \Cache::forget('statement.user.recharge.month');
            \Cache::forget('statement.user.recharge.last_month');
            \Cache::forget('statement.user.recharge.today_once');
        }

        $registerTotal = \Cache::remember('statement.user.register.total', 10, function () {
            return User::query()->count();
        });

        $registerToday = \Cache::remember('statement.user.register.today', 10, function () {
            return User::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $registerTodayInviter = \Cache::remember('statement.user.register.today_inviter', 10, function () {
            return User::query()
                ->whereHas('_profile', function ($query) {
                    $query->where('inviter', '!=', '');
                })
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $registerMonth = \Cache::remember('statement.user.register.month', 10, function () {
            return User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $registerLastMonth = \Cache::remember('statement.user.register.last_month', 3600 * 24, function () {
            return User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $row = Statement::query()
            ->where('date', Carbon::yesterday()->toDateString())
            ->first();
        $registerYesterday = $row ? $row->user_register : 0;
        $registerYesterdayInviter = $row ? $row->user_register_inviter : 0;
        $reportYesterday = $row ? $row->account_report : 0;
        $searchYesterday = $row ? $row->account_search : 0;
        $rechargeYesterday = $row ? $row->recharge_money : 0;
        $rechargeYesterdayOnce = $row ? $row->recharge_first_user : 0;

        $reportToday = \Cache::remember('statement.user.report.today', 10, function () {
            return AccountReport::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $reportMonth = \Cache::remember('statement.user.report.month', 10, function () {
            return AccountReport::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $reportLastMonth = \Cache::remember('statement.user.report.last_month', 3600 * 24, function () {
            return AccountReport::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $searchToday = \Cache::remember('statement.user.search.today', 10, function () {
            return AccountSearch::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();
        });

        $searchMonth = \Cache::remember('statement.user.search.month', 10, function () {
            return AccountSearch::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();
        });

        $searchLastMonth = \Cache::remember('statement.user.search.last_month', 3600 * 24, function () {
            return AccountSearch::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();
        });

        $rechargeToday = \Cache::remember('statement.user.recharge.today', 10, function () {
            return RechargeBill::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->sum('money');
        });

        $rechargeMonth = \Cache::remember('statement.user.recharge.month', 10, function () {
            return RechargeBill::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('money');
        });

        $rechargeLastMonth = \Cache::remember('statement.user.recharge.last_month', 3600 * 24, function () {
            return RechargeBill::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->sum('money');
        });

        $rechargeTodayOnce = \Cache::remember('statement.user.recharge.today_once', 10, function () {
            $todayUserIds = RechargeBill::query()
                ->select('user_id')
                ->whereDate('created_at', Carbon::today()->toDateString())
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
