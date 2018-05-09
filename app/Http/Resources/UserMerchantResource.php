<?php

namespace App\Http\Resources;

use App\UserMerchant;
use Illuminate\Http\Resources\Json\Resource;

class UserMerchantResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['type_label'] = UserMerchant::$types[$this->type];
        return $data;
    }
}
