<?php

namespace App\Http\Resources;

use App\AdminArticle;
use Illuminate\Http\Resources\Json\Resource;

class AdminArticleResource extends Resource
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
        $data['type_label'] = AdminArticle::$types[$data['type']];
//        $data['url'] = url('/') . '#/article/' . $this->resource->id;
        return $data;
    }
}
