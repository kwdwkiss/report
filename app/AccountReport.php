<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountReport extends Model
{
    protected $table = 'account_report';

    protected $guarded = [];

    public function _account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }

    public static function report($accountTypeId, $name, $reportTypeId, $ip)
    {
        \DB::transaction(function () use ($accountTypeId, $name, $reportTypeId, $ip) {

            Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($accountTypeId);
            Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($reportTypeId);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \Exception('ip invalid');
            }

            $account = Account::where('type', $accountTypeId)->where('name', $name)->first();
            if (!$account) {
                $account = Account::create([
                    'type' => $accountTypeId,
                    'name' => $name,
                    'status' => 102
                ]);
            }
            $account->increment('report_count');
            AccountReport::create([
                'account_id' => $account->id,
                'type' => $reportTypeId,
                'ip' => $ip
            ]);
        });
    }
}
