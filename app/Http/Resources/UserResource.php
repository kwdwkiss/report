<?php

namespace App\Http\Resources;

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
        $data['type_label'] = $this->resource->_type ? $this->resource->_type->name : '';
        $data['tags'] = $this->_tags->modelKeys();
        unset($data['_tags']);
        $data['_profile'] = new UserProfileResource($this->resource->_profile);
        return $data;
    }
}
