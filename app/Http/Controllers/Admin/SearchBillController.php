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
use App\Http\Resources\SearchBillResource;
use App\SearchBill;

class SearchBillController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $date = request('created_at');

        $query = SearchBill::query()
            ->with('_user')
            ->orderBy('id', 'desc');

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