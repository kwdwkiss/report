<?php

namespace Modules\Common\Transformers;

use Modules\Common\Entities\UserAuthBill;
use Illuminate\Http\Resources\Json\Resource;

class UserAuthBillResource extends Resource
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

        if (isset($data['_product'])) {
            $data['_product'] = new ProductResource($this->resource->_product);
        }
        if (isset($data['_product_bill'])) {
            $data['_product_bill'] = new ProductBillResource($this->resource->_productBill);
        }

        $data['status_label'] = UserAuthBill::$statusTypes[$data['status']];

        return $data;
    }
}
