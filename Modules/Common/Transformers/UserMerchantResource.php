<?php

namespace Modules\Common\Transformers;

use Modules\Common\Entities\UserMerchant;
use Illuminate\Http\Resources\Json\Resource;

class UserMerchantResource extends Resource
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
        $data['type_label'] = UserMerchant::$types[$this->type];

        if ($request->has('r_index')) {
            $secretData = [];

            $secretData['user_lock'] = $data['user_lock'];

            return $secretData;
        }

        return $data;
    }
}
