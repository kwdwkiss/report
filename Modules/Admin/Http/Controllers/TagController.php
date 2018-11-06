<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 下午3:11
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\TagResource;
use Modules\Common\Entities\Tag;
use Modules\Common\Entities\TagEntity;

class TagController extends Controller
{
    public function index()
    {
        return TagResource::collection(Tag::paginate());
    }

    public function create()
    {
        $name = request('name');

        $exists = Tag::where('name', $name)->first();
        if ($exists) {
            return [
                'code' => -1,
                'message' => '标签已存在'
            ];
        }

        Tag::create([
            'name' => $name,
        ]);

        return [];
    }

    public function update()
    {
        $this->validate(request(), [
            'id' => 'required',
            'name' => 'required'
        ]);

        $tag = Tag::findOrFail(request('id'));

        $exists = Tag::where('name', request('name'))->first();
        if ($exists && $exists->id != $tag->id) {
            return [
                'code' => -1,
                'message' => '标签已存在'
            ];
        }
        $tag->update([
            'name' => request('name')
        ]);

        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $tag = Tag::findOrFail(request('id'));
            TagEntity::where('tag_id', $tag->id)->delete();
            $tag->delete();
        });

        return [];
    }
}