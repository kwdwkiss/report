<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AccountResource extends Resource
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
        $data['account_type'] = $this->_type->name;
        $data['account_status'] = $this->_status->name;
        return $data;
    }
}
