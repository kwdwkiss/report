<?php

namespace Modules\Common\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class AdminResource extends Resource
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

        if (isset($data['roles'])) {
            $data['roles_label'] = implode(',', array_column($data['roles'], 'title'));
            $data['roles_ids'] = array_column($data['roles'], 'id');
        }

        return $data;
    }
}
