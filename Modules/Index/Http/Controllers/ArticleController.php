<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/12
 * Time: 下午4:54
 */

namespace Modules\Index\Http\Controllers;

use Modules\Common\Entities\Article;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\ArticleResource;

class ArticleController extends Controller
{
    public function list()
    {
        $id = request('id');
        $query = Article::query()->select([
            'id', 'type', 'title', 'remark', 'created_at', 'updated_at'
        ])->orderBy('created_at', 'desc');
        if ($id) {
            $query->where('type', $id);
        }
        return ArticleResource::collection($query->paginate());
    }

    public function show()
    {
        $id = request('id');
        return new ArticleResource(Article::findOrFail($id));
    }
}