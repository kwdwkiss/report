<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RechargeBill extends Model
{
    protected $table = 'recharge_bill';

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function generateBillNo($userId)
    {
        $userId = substr($userId, -4);
        return date('ymdHis', time()) . str_pad($userId, 4, '0', STR_PAD_LEFT);
    }

    public static function statement()
    {
        $data = \Cache::remember('statement.recharge.bill', 10, function () {
            $today = static::query()
                ->where('created_at', '>', Carbon::today())
                ->sum('money');

            $yesterday = static::query()
                ->where('created_at', '>', Carbon::yesterday())
                ->where('created_at', '<', Carbon::today())
                ->sum('money');

            $month = static::query()
                ->where('created_at', '>', Carbon::now()->startOfMonth())
                ->sum('money');

            $lastMonth = static::query()
                ->where('created_at', '>', Carbon::now()->subMonths(1)->startOfMonth())
                ->where('created_at', '<', Carbon::now()->startOfMonth())
                ->sum('money');

            return compact('today', 'yesterday', 'month', 'lastMonth');
        });

        return $data;
    }
}
