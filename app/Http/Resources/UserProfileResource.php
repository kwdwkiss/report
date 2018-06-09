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
        $data['gender_label'] = ['未知', '男', '女'][$this->resource->gender];

        if ($request->has('r_index')) {
            $secretData = [];

            $name = $data['name'];
            $secretData['name'] = mb_substr($name, 0, 1) . str_pad('', mb_strlen($name) - 1, '*');
            //$secretData['age'] = $data['age'] ? $data['age'] : '';
            $secretData['gender_label'] = $data['gender_label'];
            $secretData['province'] = $data['province'];
            $secretData['city'] = $data['city'];
            $secretData['address'] = $data['province'] . $data['city'];

            $secretData['user_lock'] = $data['user_lock'];
            //$secretData['alipay'] = substr_replace($data['alipay'], '***', 2, 3);

            return $secretData;
        }

        return $data;
    }
}
