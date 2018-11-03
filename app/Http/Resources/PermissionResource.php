<?php

namespace App\Http\Resources;

use App\Permission;
use Illuminate\Http\Resources\Json\Resource;

class PermissionResource extends Resource
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

        $data['guard_title'] = Permission::$guardTitle[$data['guard_name']];

        return $data;
    }
}
