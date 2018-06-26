<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/23
 * Time: 下午12:12
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchBillResource;
use App\SearchBill;

class SearchBillController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $date = request('created_at');

        $query = SearchBill::query()
            ->with('_user');

        $order_query = json_decode(request('order_query'), true);
        $order_field = array_get($order_query, 'field');
        $order_order = array_get($order_query, 'order');
        if ($order_field) {
            if ($order_order == 'ascending') {
                $query->orderBy($order_field, 'asc');
            } elseif ($order_order == 'descending') {
                $query->orderBy($order_field, 'desc');
            }
        }
        $query->orderBy('id', 'desc');

        if (!is_null($mobile)) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }
        if (!is_null($date)) {
            $query->where('date', '>=', $date[0]);
            $query->where('date', '<=', $date[1]);
        }

        return SearchBillResource::collection($query->paginate());
    }
}