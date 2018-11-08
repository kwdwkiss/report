<?php

namespace Modules\Common\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Common\Entities\RechargeBill;

class RechargeBillResource extends Resource
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
        $data['pay_type_label'] = RechargeBill::$payTypes[$data['pay_type']];
        $data['status_label'] = RechargeBill::$status[$data['status']];
        return $data;
    }
}
