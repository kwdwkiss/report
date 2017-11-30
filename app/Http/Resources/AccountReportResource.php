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
        $data['account_type'] = $this->_account->_type->name;
        $data['account_name'] = $this->_account->name;
        $data['report_type'] = $this->_type->name;

        return $data;
    }
}
