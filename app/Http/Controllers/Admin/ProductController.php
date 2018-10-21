<?php

namespace App\Http\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::query()->paginate();

        return ProductResource::collection($product);
    }

    public function update()
    {
        
    }
}
