<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/11/3
 * Time: 下午10:00
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Entities\AccountFavor;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AccountFavorResource;

class AccountFavorController extends Controller
{
    public function index()
    {
        $account_name = request('account_name');
        $account_type = request('account_type');
        $created_at = request('created_at');

        $query = AccountFavor::query()->with('_user', '_accountType')->orderBy('id', 'desc');

        if ($account_name) {
            $query->where('account_name', $account_name);
        }
        if ($account_type) {
            $query->where('account_type', $account_type);
        }
        if ($created_at) {
            $query->where('created_at', '>', $created_at[0]);
            $query->where('created_at', '<', date('Y-m-d', strtotime($created_at[1] . ' +1 day')));
        }

        return AccountFavorResource::collection($query->paginate());
    }

    public function show()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }
}