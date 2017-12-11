<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserProfileResource extends Resource
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
        $data['gender_label'] = ['未知', '男', '女'][$this->resource->gender];
        return $data;
    }
}
