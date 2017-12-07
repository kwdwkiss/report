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
        $data['type_label'] = $this->_type->name;
        $data['status_label'] = $this->_status->name;
        return $data;
    }
}
