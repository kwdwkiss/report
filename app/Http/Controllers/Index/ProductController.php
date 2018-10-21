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
        $product = Product::all();

        return ProductResource::collection($product);
    }

    public function show()
    {
        $id = request('id');

        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }
}