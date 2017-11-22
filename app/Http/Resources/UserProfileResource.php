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
        $data['tags'] = $this->_tags->modelKeys();
        unset($data['_tags']);
        return $data;
    }
}
