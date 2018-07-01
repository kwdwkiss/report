<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

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
        //1-手工充值 2-支付宝 3-财付通 4-手Q 5-微信
        $payTypes = [
            0 => '无',
            1 => '手工充值',
            2 => '支付宝',
            3 => '财付通',
            4 => '手Q',
            5 => '微信',
        ];
        $status = [
            0 => '待支付',
            1 => '已支付'
        ];

        $data = parent::toArray($request);
        $data['pay_type_label'] = $payTypes[$data['pay_type']];
        $data['status_label'] = $status[$data['status']];
        return $data;
    }
}
