<?php

namespace App\Http\Resources;

use App\UserMerchant;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['type_label'] = $this->_type ? $this->_type->name : '';
        $data['_profile'] = new UserProfileResource($this->_profile);
        $data['_merchant'] = new UserMerchantResource($this->_merchant);
        return $data;
    }
}
