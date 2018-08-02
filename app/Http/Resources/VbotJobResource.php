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
        $data['run_status_label'] = VbotJob::$runStatusTypes[$data['run_status']];

        return $data;
    }
}
