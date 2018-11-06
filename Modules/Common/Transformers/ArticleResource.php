<?php

namespace Modules\Common\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ArticleResource extends Resource
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
        $data['article_type'] = $this->_type->name;
        $data['type_label'] = $this->_type->name;
        $data['url'] = url('/') . '#/article/' . $this->resource->id;
        return $data;
    }
}
