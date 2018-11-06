<?php

namespace Modules\Common\Transformers;


use Illuminate\Http\Resources\Json\Resource;
use Modules\Common\Entities\VbotJob;

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

        $send_list = is_array($data['send_list']) ? $data['send_list'] : [];
        $sent_list = is_array($data['sent_list']) ? $data['sent_list'] : [];
        $data['send_list_count'] = count($send_list);
        $data['sent_list_count'] = count($sent_list);

        return $data;
    }
}
