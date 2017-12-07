<?php

namespace App;

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

    public static function report($account_type, $name, $report_type, $ip)
    {
        \DB::transaction(function () use ($account_type, $name, $report_type, $ip
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

            AccountReport::create([
                'account_type' => $account_type,
                'account_name' => $name,
                'type' => $report_type,
                'ip' => $ip
            ]);
        });
    }
}
