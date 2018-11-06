<?php

namespace Modules\Common\Transformers;

use Modules\Common\Entities\Permission;
use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
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
        if (isset($data['permissions'])) {
            $data['group_permissions'] = $this->resource->permissions->groupBy('controller');
        }

        return $data;
    }
}
