<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/4
 * Time: 下午9:58
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\StatementResource;
use Modules\Common\Entities\Statement;

class StatementController extends Controller
{
    public function index()
    {
        $type = request('type', 1);

        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

        $date = request('created_at');

        $query = Statement::query();

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
        $query->orderBy('date', 'desc');

        if (!is_null($date)) {
            $query->where('date', '>=', $date[0]);
            $query->where('date', '<=', $date[1]);
        }
        if ($type) {
            $query->where('type', $type);
        }

        return StatementResource::collection($query->paginate());
    }

    public function profile()
    {
        $user = \Auth::guard('admin')->user();
        if ($user->id != 1) {
            throw new JsonException('无权访问');
        }

        $data = \Cache::get('statement.profile', []);

        if (!$data) {
            $data = Statement::profile();
        }

        return ['data' => $data];
    }
}