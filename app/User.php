<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

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

    public function _remark()
    {
        return $this->hasMany(UserRemark::class, 'user_id', 'id');
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

    public function _product()
    {
        return $this->hasMany(UserProduct::class, 'user_id', 'id');
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

    public static function register($mobile, $password, $inviterMobile)
    {
        $user = null;
        \DB::transaction(function () use (&$user, $mobile, $password, $inviterMobile) {
            $user = User::create([
                'mobile' => $mobile,
                'type' => 401,
                'password' => bcrypt($password),
                'last_ip' => get_client_ip()
            ]);

            //新用户注册发放200积分
            $amount = 100;

            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            AmountBill::create([
                'user_id' => $user->id,
                'bill_no' => AmountBill::generateBillNo($user->id),
                'type' => 0,
                'amount' => $amount,
                'user_amount' => $userProfile->amount,
                'biz_type' => 0,
                'biz_id' => 4,
                'description' => "新用户注册赠送${amount}积分"
            ]);

            //邀请人发放100积分
            $inviterUser = User::with('_profile')
                ->where('id', '!=', $user->id)//邀请人不能是自己
                ->where('mobile', $inviterMobile)
                ->first();
            if ($inviterUser) {
                $inviterAmount = 50;
                $inviterUser->_profile->increment('amount', $inviterAmount);
                AmountBill::create([
                    'user_id' => $inviterUser->id,
                    'bill_no' => AmountBill::generateBillNo($inviterUser->id),
                    'type' => 0,
                    'amount' => $inviterAmount,
                    'user_amount' => $inviterUser->_profile->amount,
                    'biz_type' => 0,
                    'biz_id' => 5,
                    'description' => "邀请新用户注册赠送${inviterAmount}积分"
                ]);
                $userProfile->update(['inviter' => $inviterMobile]);
            }
        });
        return $user;
    }
}
