<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountReport extends Model
{
    protected $table = 'account_report';

    protected $guarded = [];

    public static $imageLimit = [
        202 => [301, 302, 304, 307]
    ];

    public function _accountType()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'account_type');
    }

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }

    public function _attachments()
    {
        return $this->belongsToMany(Attachment::class, 'account_report_attachment', 'account_report_id')
            ->withTimestamps();
    }

    public static function statement()
    {
        $data = \Cache::remember('statement.account.report', 10, function () {
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
