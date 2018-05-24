<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Taxonomy;

class AccountController extends Controller
{
    public function list()
    {
        $name = request('name');
        $type = request('type');
        $status = request('status');

        $query = Account::query()->with('_type', '_status')->orderBy('id', 'desc');

        if (!is_null($name)) {
            $query->where('name', $name);
        }
        if (!is_null($type)) {
            $query->where('type', $type);
        }
        if (!is_null($status)) {
            $query->where('status', $status);
        }

        return AccountResource::collection($query->paginate());
    }

    public function show()
    {
        $account = Account::findOrFail(request('id'));

        return new AccountResource($account);
    }

    public function create()
    {
        \DB::transaction(function () {
            $item = json_decode(request()->getContent(), true);

            $type = array_get($item, 'type');
            $name = array_get($item, 'name');
            $status = array_get($item, 'status', 102);
            $auth_cash = array_get($item, 'auth_cash', 0);
            $address = array_get($item, 'address', '');
            $remark = array_get($item, 'remark', '');

            Taxonomy::where('pid', Taxonomy::ACCOUNT_TYPE)->findOrFail($type);
            Taxonomy::where('pid', Taxonomy::ACCOUNT_STATUS)->findOrFail($status);
            if (is_null($name)) {
                throw new JsonException('账号不能为空');
            }
            if ($type == 201 && !preg_match('/^[1-9][0-9]{4,14}$/', $name)) {
                throw new JsonException('QQ号码格式错误');
            }
            if ($type == 204 && !preg_match('/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/', $name)) {
                throw new JsonException('手机号码格式错误');
            }

            $account = Account::where('type', $type)->where('name', $name)->first();
            if ($account) {
                throw new JsonException('账号已存在');
            }

            Account::create([
                'type' => $type,
                'name' => $name,
                'status' => $status,
                'auth_cash' => $auth_cash,
                'address' => $address,
                'remark' => $remark
            ]);
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $item = json_decode(request()->getContent(), true);

            $id = array_get($item, 'id');
            $status = array_get($item, 'status');
            $auth_cash = array_get($item, 'auth_cash');
            $address = array_get($item, 'address');
            $remark = array_get($item, 'remark');

            Taxonomy::where('pid', Taxonomy::ACCOUNT_STATUS)->findOrFail($status);

            $account = Account::findOrFail($id);

            $account->update([
                'status' => $status,
                'auth_cash' => $auth_cash,
                'address' => $address,
                'remark' => $remark
            ]);
        });

        return [];
    }

    public function delete()
    {
        throw new JsonException('账号无需删除');
    }
}