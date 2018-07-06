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
        $account_search_user = \DB::table(\DB::raw("({$subQuery->toSql()}) as a"))->mergeBindings($subQuery->getQuery())->count();

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

        $excel_download_user = BehaviorLog::query()
            ->whereDate('created_at', $date)
            ->where('type', 2)
            ->groupBy('user_id')
            ->count();

        $excel_save_count = Excel::query()
            ->whereDate('created_at', $date)
            ->count();

        $excel_save_user = Excel::query()
            ->whereDate('created_at', $date)
            ->groupBy('user_id')
            ->count();

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
}
