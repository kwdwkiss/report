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

    public function _type()
    {
        return $this->hasOne(Taxonomy::class, 'id', 'type');
    }

    public function _profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function _amountBill()
    {
        return $this->hasMany(AmountBill::class, 'user_id', 'id');
    }

    public function _recharge()
    {
        return $this->hasMany(RechargeBill::class, 'user_id', 'id');
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

    public static function statement()
    {
        $data = \Cache::remember('statement.user.register', 10, function () {
            $total = static::query()->count();

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

            return compact('total', 'today', 'yesterday', 'month', 'lastMonth');
        });

        return $data;
    }
}
