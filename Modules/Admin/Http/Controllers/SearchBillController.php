<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/23
 * Time: 下午12:12
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\SearchBillResource;
use Modules\Common\Entities\SearchBill;
use Carbon\Carbon;

class SearchBillController extends Controller
{
    public function index()
    {
        $type = request('type', 0);
        $mobile = request('mobile');
        $date = request('created_at');

        if (!in_array($type, [0, 1])) {
            throw new JsonException('argument type error');
        }

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

        $query->where('type', $type);
        if (!is_null($mobile)) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }
        if (!is_null($date)) {
            if ($type == 0) {
                $query->where('date', '>=', $date[0]);
                $query->where('date', '<=', $date[1]);
            } elseif ($type == 1) {
                $query->where('date', '>=', Carbon::parse($date[0])->format('Y-m'));
                $query->where('date', '<=', Carbon::parse($date[1])->format('Y-m'));
            }
        }

        return SearchBillResource::collection($query->paginate());
    }
}