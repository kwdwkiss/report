<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/9/12
 * Time: 下午12:32
 */

namespace App\Http\Controllers\Admin;


use App\Excel;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExcelResource;
use Carbon\Carbon;

class ExcelController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $date = request('created_at');

        $query = Excel::query()->with('_user');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }

        if (!is_null($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0]));
            $query->where('created_at', '<=', Carbon::parse($date[1]));
        }

        $query->orderByDesc('id');

        return ExcelResource::collection($query->paginate());
    }
}