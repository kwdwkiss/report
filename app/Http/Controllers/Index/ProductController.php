<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 上午10:35
 */

namespace App\Http\Controllers\Index;


use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;

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