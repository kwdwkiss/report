<?php

namespace Modules\Common\Transformers;

use Modules\Common\Entities\AmountBill;
use Illuminate\Http\Resources\Json\Resource;

class AmountBillResource extends Resource
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
        $data['status_label'] = AmountBill::$status[$data['status']];
        $data['type_label'] = AmountBill::$types[$data['type']];
        $data['biz_type_label'] = AmountBill::$bizTypes[$data['biz_type']];
        return $data;
    }
}
