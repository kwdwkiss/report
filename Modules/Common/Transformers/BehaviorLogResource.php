<?php

namespace Modules\Common\Transformers;

use Modules\Common\Entities\BehaviorLog;
use Illuminate\Http\Resources\Json\Resource;

class BehaviorLogResource extends Resource
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

        $data['user_mobile'] = '';
        if (isset($data['_user'])) {
            $data['user_mobile'] = $data['_user']['mobile'];
        }

        $data['content_html'] = str_replace("\n", '<br>', $data['content']);
        $data['type_label'] = BehaviorLog::$types[$data['type']];

        return $data;
    }
}
