<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/24
 * Time: 下午3:47
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\ProductBillResource;
use Modules\Common\Entities\ProductBill;

class ProductBillController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');

        $query = ProductBill::query()->with('_user', '_product');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }

        $query->orderBy('id', 'desc');

        return ProductBillResource::collection($query->paginate());
    }
}