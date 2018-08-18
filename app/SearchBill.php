<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SearchBill extends Model
{
    protected $table = 'search_bill';

    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function settleDay($date)
    {
        $date = $date ? Carbon::parse($date)->toDateString() : Carbon::yesterday()->toDateString();
        $nextDate = Carbon::parse($date)->addDay()->toDateString();

        $userIds = AccountSearch::query()
            ->select('user_id')
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id')
            ->get()->pluck('user_id');

        foreach ($userIds as $userId) {
            \DB::transaction(function () use ($userId, $date, $nextDate) {
                $count = AccountSearch::query()
                    ->where('created_at', '>=', $date)
                    ->where('created_at', '<', $nextDate)
                    ->where('user_id', $userId)
                    ->count();

                $amount = $count * 2;//一次查询消耗2积分

                $searchBill = static::updateOrCreate([
                    'date' => $date,
                    'user_id' => $userId
                ], [
                    'count' => $count,
                    'amount' => $amount
                ]);

                $amountBill = AmountBill::query()
                    ->where('user_id', $userId)
                    ->where('biz_id', $searchBill->id)
                    ->where('biz_type', 101)
                    ->first();

                if (!$amountBill) {
                    AmountBill::create([
                        'user_id' => $userId,
                        'bill_no' => AmountBill::generateBillNo($userId),
                        'biz_id' => $searchBill->id,
                        'biz_type' => 101,
                        'type' => 1,
                        'amount' => $searchBill->amount,
                        'description' => "${date}查询${count}次"
                    ]);
                } else {
                    $amountBill->update([
                        'type' => 1,
                        'amount' => $searchBill->amount,
                        'description' => "${date}查询${count}次"
                    ]);
                }
            });
        }
    }
}
