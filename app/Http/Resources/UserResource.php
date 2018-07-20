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
        $data['auth_type_label'] = $this->_auth_type ? $this->_auth_type->name : '';
        $data['auth_duration_label'] = $data['auth_duration'] . '个月';
        $data['is_auth'] = $this->resource->isAuth();
        $data['report_detail_enable'] = $this->resource->isAuth() && in_array($data['type'], [403, 404]);
        $data['report_detail_label'] = '认证主持或商家才能查看详情';

        $data['_profile'] = new UserProfileResource($this->_profile);
        $data['_merchant'] = new UserMerchantResource($this->_merchant);


        if ($request->has('r_index')) {
            $secretData = [];

            $secretData['id'] = $data['id'];
            $secretData['type'] = $data['type'];
            $secretData['type_label'] = $data['type_label'];
            $secretData['_profile'] = $data['_profile'];
            $secretData['_merchant'] = $data['_merchant'];
            $secretData['is_auth'] = $data['is_auth'];
            $secretData['report_detail_enable'] = $this->resource->isAuth() && in_array($data['type'], [403, 404]);
            $secretData['report_detail_label'] = '认证主持或商家才能查看详情';

            $secretData['mobile'] = substr_replace($data['mobile'], '****', 3, 4);//186****3808
            $secretData['qq'] = $data['qq'] ? substr_replace($data['qq'], '**', -2) : '';
            $secretData['wx'] = $data['wx'] ? substr_replace($data['wx'], '**', -2) : '';
            $secretData['ww'] = $data['ww'] ? str_replace(mb_substr($data['ww'], -2), '**', $data['ww']) : '';
            $secretData['jd'] = $data['jd'] ? str_replace(mb_substr($data['jd'], -2), '**', $data['jd']) : '';
            $secretData['is'] = $data['is'] ? str_replace(mb_substr($data['is'], -2), '**', $data['is']) : '';

            return $secretData;
        }

        return $data;
    }
}
