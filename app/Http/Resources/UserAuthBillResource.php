<?php

namespace App\Http\Resources;

use App\Taxonomy;
use App\UserAuthBill;
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

        $data['status_label'] = UserAuthBill::$statusTypes[$data['status']];
        $data['type_label'] = Taxonomy::query()
            ->where('pid', Taxonomy::USER_TYPE)
            ->findOrFail($data['type'])
            ->name;
        $data['duration_label'] = $data['duration'] . '个月';

        return $data;
    }
}
