<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/3
 * Time: 下午4:13
 */

namespace App\Http\Controllers\User;


use App\Excel;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExcelResource;

class ExcelController extends Controller
{
    public function index()
    {
        $title = request('title');
        $body = request('body');

        $user = \Auth::guard('user')->user();

        $query = Excel::query()
            ->where('user_id', $user->id);

        if ($title) {
            $query->where('title', 'like', "%${title}%");
        }
        if ($body) {
            $query->where('body', 'like', "%${body}%");
        }

        return ExcelResource::collection($query->paginate());
    }

    public function create()
    {
        $data = request('data', []);
        $title = request('title');

        if (!is_array($data)) {
            throw new JsonException('数据格式错误');
        }
        if (empty($data)) {
            throw new JsonException('数据为空');
        }
        if (!$title) {
            throw new JsonException('标题为空');
        }

        $user = \Auth::guard('user')->user();

        $count = Excel::query()->where('user_id', $user->id)->count();
        $countLimit = 30;
        if ($count >= $countLimit) {
            throw new JsonException("每个用户只能保存${countLimit}份表格，请删除旧表格再保存");
        }

        Excel::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'columns' => count($data[0]),
            'rows' => count($data)
        ]);

        return [];
    }

    public function update()
    {
        $id = request('id');
        $data = request('data');
        $title = request('title');

        if (!is_array($data)) {
            throw new JsonException('数据格式错误');
        }
        if (empty($data)) {
            throw new JsonException('数据为空');
        }
        if (!$title) {
            throw new JsonException('标题为空');
        }

        $user = \Auth::guard('user')->user();

        Excel::query()->where('user_id', $user->id)->where('id', $id)->update([
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);

        return [];
    }

    public function delete()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        Excel::query()->where('user_id', $user->id)->where('id', $id)->delete();

        return [];
    }
}