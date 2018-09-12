<?php

namespace App\Http\Resources;

use App\VbotJob;
use Illuminate\Http\Resources\Json\Resource;

class VbotJobResource extends Resource
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

        $data['status_label'] = VbotJob::$statusTypes[$data['status']];

        $data['send_list_count'] = count($data['send_list']);
        $data['sent_list_count'] = count($data['sent_list']);

        return $data;
    }
}
