<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountReport extends Model
{
    protected $table = 'account_report';

    protected $guarded = [];

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

    public static function report($account_type, $name, $report_type, $ip, $description, $attachment = null)
    {
        \DB::transaction(function () use ($account_type, $name, $report_type, $ip, $description, $attachment
        ) {
            Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($account_type);
            Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($report_type);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \Exception('ip invalid');
            }

            $account = Account::where('type', $account_type)->where('name', $name)->first();
            if (!$account) {
                $account = Account::create([
                    'type' => $account_type,
                    'name' => $name,
                    'status' => 102
                ]);
            }
            $account->increment('report_count');

            $accountReport = AccountReport::create([
                'account_type' => $account_type,
                'account_name' => $name,
                'type' => $report_type,
                'ip' => $ip,
                'description' => $description
            ]);

            //添加附件
            if ($attachment) {
                $accountReport->_attachments()->attach($attachment);
            }
        });
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
