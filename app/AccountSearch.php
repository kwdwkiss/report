<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountSearch extends Model
{
    protected $table = 'account_search';

    protected $guarded = [];

    public static function statement()
    {
        $data = \Cache::remember('statement.account.search', 10, function () {
            $today = static::query()
                ->where('created_at', '>', Carbon::today())
                ->count();

            $yesterday = static::query()
                ->where('created_at', '>', Carbon::yesterday())
                ->where('created_at', '<', Carbon::today())
                ->count();

            $month = static::query()
                ->where('created_at', '>', Carbon::now()->startOfMonth())
                ->count();

            $lastMonth = static::query()
                ->where('created_at', '>', Carbon::now()->subMonths(1)->startOfMonth())
                ->where('created_at', '<', Carbon::now()->startOfMonth())
                ->count();

            return compact('today', 'yesterday', 'month', 'lastMonth');
        });

        return $data;
    }
}
