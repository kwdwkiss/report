<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountFavor extends Model
{
    protected $guarded = [];

    public function _user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function _accountType()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'account_type');
    }

    public static function autoEnable()
    {
        $userIds = RechargeBill::query()
            ->whereHas('_user', function ($query) {
                $query->doesntHave('_favor')
                    ->where('created_at', '<', Carbon::now()->subMonth(1));
            })
            ->groupBy('user_id')
            ->havingRaw('count(*)>?', [3])
            ->havingRaw('sum(money)>?', [10])
            ->pluck('user_id')->toArray();

        $insertData = [];
        foreach ($userIds as $userId) {
            $insertData[] = [
                'user_id' => $userId,
                'admin_id' => 1,
                'total' => 150,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        }

        $chunkData = array_chunk($insertData, 1000);
        //批量插入数据
        foreach ($chunkData as $insertItemData) {
            UserFavor::query()->insert($insertItemData);
        }

        return count($chunkData);
    }
}
