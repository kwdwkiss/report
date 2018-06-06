<?php

namespace App\Http\Resources;

use App\UserMerchant;
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
        $data['type_label'] = $this->_type ? $this->_type->name : '';
        $data['_profile'] = new UserProfileResource($this->_profile);
        $data['_merchant'] = new UserMerchantResource($this->_merchant);

        if ($request->has('r_index')) {
            $secretData = [];

            $secretData['id'] = $data['id'];
            $secretData['type_label'] = $data['type_label'];
            $secretData['_profile'] = $data['_profile'];
            $secretData['_merchant'] = $data['_merchant'];

            $secretData['mobile'] = substr_replace($data['mobile'], '****', 3, 4);//186****3808
            $secretData['qq'] = $data['qq'] ? substr_replace($data['qq'], '**', -2) : '';
            $secretData['wx'] = $data['wx'] ? substr_replace($data['wx'], '**', -2) : '';
            $secretData['ww'] = $data['ww'] ? str_replace(mb_substr($data['ww'], -2), '**', $data['ww']) : '';

            return $secretData;
        }

        return $data;
    }
}
