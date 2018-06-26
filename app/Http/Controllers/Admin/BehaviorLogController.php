<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/23
 * Time: 下午12:12
 */

namespace App\Http\Controllers\Admin;

use App\BehaviorLog;
use App\Http\Controllers\Controller;
use App\Http\Resources\BehaviorLogResource;

class BehaviorLogController extends Controller
{
    public function index()
    {
        $type = request('type');
        $date = request('created_at');

        $query = BehaviorLog::query()
            ->with('_user');

//        $order_query = json_decode(request('order_query'), true);
//        $order_field = array_get($order_query, 'field');
//        $order_order = array_get($order_query, 'order');
//        if ($order_field) {
//            if ($order_order == 'ascending') {
//                $query->orderBy($order_field, 'asc');
//            } elseif ($order_order == 'descending') {
//                $query->orderBy($order_field, 'desc');
//            }
//        }
        $query->orderBy('id', 'desc');

        if ($type) {
            $query->where('type', $type);
        }
        if (!is_null($date)) {
            $query->where('created_at', '>=', $date[0]);
            $query->where('created_at', '<=', $date[1]);
        }

        return BehaviorLogResource::collection($query->paginate());
    }
}