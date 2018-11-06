<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 上午10:35
 */

namespace Modules\Index\Http\Controllers;


use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\ProductResource;
use Modules\Common\Entities\Product;

class ProductController extends Controller
{
    public function index()
    {
        $group = request('group');

        $query = Product::query();

        if ($group) {
            $query->where('group', $group);
        }

        return ProductResource::collection($query->get());
    }

    public function show()
    {
        $id = request('id');

        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }
}