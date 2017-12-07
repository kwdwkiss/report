<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AccountReportResource extends Resource
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
        $data['account_type_label'] = $this->_accountType->name;
        $data['type_label'] = $this->_type->name;
        if ($request->has('ip_hide')) {
            $data['ip'] = preg_replace('/(\d+\.\d+)\.\d+\.\d+/', '$1.*.*', $data['ip']);
        }
        return $data;
    }
}
