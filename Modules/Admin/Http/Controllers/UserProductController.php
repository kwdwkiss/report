<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\UserProductResource;
use Modules\Common\Entities\UserProduct;

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
