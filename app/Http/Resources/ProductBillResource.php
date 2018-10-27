<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 上午10:36
 */

namespace App\Http\Resources;


use App\Product;
use App\ProductBill;
use Illuminate\Http\Resources\Json\Resource;

class ProductBillResource extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['pay_status_label'] = ProductBill::$payStatus[$data['pay_status']];

        return $data;
    }
}