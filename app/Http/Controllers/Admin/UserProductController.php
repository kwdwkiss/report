<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProductResource;
use App\UserProduct;

class UserProductController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');

        $query = UserProduct::query()->with('_user', '_product');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }

        $query->orderBy('id', 'desc');

        return UserProductResource::collection($query->paginate());
    }
}
