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

    public static function settleMonth($date)
    {
        //用日统计的累加起来计算月统计的
        $date = $date ? Carbon::parse($date)->firstOfMonth()->toDateString()
            : Carbon::now()->firstOfMonth()->toDateString();
        $nextDate = Carbon::parse($date)->addMonth()->firstOfMonth()->toDateString();

        $data = SearchBill::query()
            ->select('user_id', \DB::raw('group_concat(count) as count_str'))
            ->where('date', '>=', $date)
            ->where('date', '<', $nextDate)
            ->groupBy('user_id')
            ->get();

        \DB::transaction(function () use ($data, $date, $nextDate) {
            //删除旧数据
            SearchBill::query()
                ->where('type', 1)
                ->where('date', Carbon::parse($date)->format('Y-m'))
                ->delete();

            $insertData = [];
            foreach ($data as $item) {
                $countStr = $item['count_str'];
                $userId = $item['user_id'];

                $count = array_sum(explode(',', $countStr));

                $amount = $count * 2;//一次查询消耗2积分

                $insertData[] = [
                    'type' => 1,
                    'date' => Carbon::parse($date)->format('Y-m'),
                    'user_id' => $userId,
                    'count' => $count,
                    'amount' => $amount
                ];
            }

            $chunkData = array_chunk($insertData, 10000);
            //批量插入数据
            foreach ($chunkData as $insertItemData) {
                SearchBill::query()->insert($insertItemData);
            }
        });
    }
}
