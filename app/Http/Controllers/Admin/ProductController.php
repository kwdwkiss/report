<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::query();

        return ProductResource::collection($query->paginate());
    }

    public function update()
    {

    }
}
