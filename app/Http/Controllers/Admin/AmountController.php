<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/23
 * Time: 下午12:12
 */

namespace App\Http\Controllers\Admin;

use App\AmountBill;
use App\Http\Controllers\Controller;
use App\Http\Resources\AmountBillResource;

class AmountController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $bill_no = request('bill_no');
        $created_at = request('created_at');

        $query = AmountBill::query()
            ->with('_user')
            ->orderBy('created_at', 'desc');

        if (!is_null($mobile)) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }
        if (!is_null($bill_no)) {
            $query->where('bill_no', $bill_no);
        }
        if (!is_null($created_at)) {
            $query->where('created_at', '>', $created_at[0]);
            $query->where('created_at', '<', date('Y-m-d', strtotime($created_at[1] . ' +1 day')));
        }

        return AmountBillResource::collection($query->paginate());
    }
}