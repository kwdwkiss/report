<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'auth_start_at',
        'auth_end_at'
    ];

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }

    public function _auth_type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'auth_type');
    }

    public function _profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function _merchant()
    {
        return $this->hasOne(UserMerchant::class, 'user_id', 'id');
    }

    public function _amountBill()
    {
        return $this->hasMany(AmountBill::class, 'user_id', 'id');
    }

    public function _recharge()
    {
        return $this->hasMany(RechargeBill::class, 'user_id', 'id');
    }

    public function _account_report()
    {
        return $this->hasMany(AccountReport::class, 'user_id', 'id');
    }

    public function _tagEntities()
    {
        return $this->belongsTo(TagEntity::class, 'id', 'entity_id');
    }

    public function _tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_entity', 'entity_id')->wherePivot('entity_type', 'user');
    }

    public function syncTag($ids)
    {
        $ids = $this->parseIds($ids);
        $data = [];
        foreach ($ids as $key => $value) {
            $data[$value] = ['entity_type' => 'user'];
        }
        $this->_tags()->sync($data);
    }

    protected function parseIds($value)
    {
        if ($value instanceof Model) {
            return [$value->getKey()];
        }

        if ($value instanceof Collection) {
            return $value->modelKeys();
        }

        if ($value instanceof BaseCollection) {
            return $value->toArray();
        }

        return (array)$value;
    }

    public function isAuth()
    {
        return $this->auth_end_at > Carbon::now();
    }

    public function isCheck()
    {
        $result = false;
        switch ($this->type) {
            case 401://普通会员
                break;
            case 402://试客
                $result = $this->_profile->user_lock == true;
                break;
            case 403://商家
                $result = $this->_profile->user_lock == true;
                break;
            case 404://主持
                $result = $this->_profile->user_lock == true;
                break;
        }
        return $result;
    }

    public function updateApiKey()
    {
        $this->update(['api_key' => md5($this->id . microtime())]);
    }

    public function updateApiSecret()
    {
        $this->update(['api_secret' => md5($this->id . microtime())]);
    }

    public static function statement()
    {
        $data = \Cache::remember('statement.user.register', 10, function () {
            $total = User::query()->count();

            $today = User::query()
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();

            $todayInviter = User::query()
                ->whereHas('_profile', function ($query) {
                    $query->where('inviter', '!=', '');
                })
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->count();

            $yesterday = static::query()
                ->whereDate('created_at', Carbon::yesterday()->toDateString())
                ->count();

            $yesterdayInviter = User::query()
                ->whereHas('_profile', function ($query) {
                    $query->where('inviter', '!=', '');
                })
                ->whereDate('created_at', Carbon::yesterday()->toDateString())
                ->count();

            $month = User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $lastMonth = User::query()
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->subMonths(1)->month)
                ->count();

            return compact('total', 'today', 'yesterday', 'month', 'lastMonth',
                'todayInviter', 'yesterdayInviter');
        });

        return $data;
    }
}
