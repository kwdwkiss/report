<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/12/12
 * Time: 下午4:54
 */

namespace App\Http\Controllers\Index;

use App\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function list()
    {
        $id = request('id');
        $query = Article::query()->orderBy('created_at', 'desc');
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