<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\ProductResource;
use Modules\Common\Entities\Product;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::query();

        $query->orderBy('id', 'desc');

        return ProductResource::collection($query->paginate());
    }

    public function update()
    {

    }
}
