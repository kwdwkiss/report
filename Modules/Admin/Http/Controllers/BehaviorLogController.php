<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/23
 * Time: 下午12:12
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\BehaviorLog;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\BehaviorLogResource;

class BehaviorLogController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

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