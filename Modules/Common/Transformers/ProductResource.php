<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 上午10:36
 */

namespace Modules\Common\Transformers;


use Modules\Common\Entities\Product;
use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['type_label'] = Product::$types[$data['type']];
        $data['duration_label']=implode(',',$data['duration']);

        return $data;
    }
}