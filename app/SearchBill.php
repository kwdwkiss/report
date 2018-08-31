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

        $data = AccountSearch::query()
            ->select('user_id', \DB::raw('count(*) as count'))
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
            ->groupBy('user_id')
            ->get();

        \DB::transaction(function () use ($data, $date, $nextDate) {
            //清空旧数据
            $ids = SearchBill::query()
                ->select('id')
                ->where('type', 0)
                ->where('date', $date)
                ->get()->pluck('id')->toArray();
            SearchBill::destroy($ids);
            AmountBill::query()
                ->where('biz_type', 101)
                ->whereIn('biz_id', $ids)
                ->delete();

            $searchBillData = [];
            foreach ($data as $item) {
                $userId = $item['user_id'];
                $count = $item['count'];

                $amount = $count * 2;//一次查询消耗2积分

                $searchBillData[] = [
                    'type' => 0,
                    'date' => $date,
                    'user_id' => $userId,
                    'count' => $count,
                    'amount' => $amount,
                ];
            }
            $chunkData = array_chunk($searchBillData, 1000);
            //批量插入数据
            foreach ($chunkData as $insertItemData) {
                SearchBill::query()->insert($insertItemData);
            }

            $searchBills = SearchBill::query()
                ->where('type', 0)
                ->where('date', $date)
                ->get();

            //更新amount_bill
            $amountBillData = [];
            foreach ($searchBills as $item) {
                $amountBillData[] = [
                    'user_id' => $item['user_id'],
                    'bill_no' => AmountBill::generateBillNo($item['user_id']),
                    'biz_id' => $item['id'],
                    'biz_type' => 101,
                    'type' => 1,
                    'amount' => $item['amount'],
                    'description' => "${date}查询{$item['count']}次"
                ];
            }

            $chunkData = array_chunk($amountBillData, 1000);
            //批量插入数据
            foreach ($chunkData as $insertItemData) {
                AmountBill::query()->insert($insertItemData);
            }
        });
    }

    public static function settleMonth($date)
    {
        $date = $date ? Carbon::parse($date)->firstOfMonth()->toDateString()
            : Carbon::now()->firstOfMonth()->toDateString();
        $nextDate = Carbon::parse($date)->addMonth()->firstOfMonth()->toDateString();

        $data = AccountSearch::query()
            ->select('user_id', \DB::raw('count(*) as count'))
            ->where('created_at', '>=', $date)
            ->where('created_at', '<', $nextDate)
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
                $userId = $item['user_id'];
                $count = $item['count'];

                $amount = $count * 2;//一次查询消耗2积分

                $insertData[] = [
                    'type' => 1,
                    'date' => Carbon::parse($date)->format('Y-m'),
                    'user_id' => $userId,
                    'count' => $count,
                    'amount' => $amount
                ];
            }

            $chunkData = array_chunk($insertData, 1000);
            //批量插入数据
            foreach ($chunkData as $insertItemData) {
                SearchBill::query()->insert($insertItemData);
            }
        });
    }
}
