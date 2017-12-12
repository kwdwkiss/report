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

    }

    public function show()
    {
        $id = request('id');
        $article = Article::findOrFail($id);
        return new ArticleResource($article);
    }
}