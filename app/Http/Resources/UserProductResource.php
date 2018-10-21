<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/10/21
 * Time: 上午10:36
 */

namespace App\Http\Resources;


use App\UserProduct;
use Illuminate\Http\Resources\Json\Resource;

class UserProductResource extends Resource
{
    public function toArray($request)
    {
        $data = parent::toArray($request);

        $data['status_label'] = UserProduct::$status[$data['status']];

        return $data;
    }
}