<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\Account;
use App\Article;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Taxonomy;

class ArticleController extends Controller
{
    public function list()
    {
        $type = request('type');
        $title = request('title');

        $query = Article::query()->with('_type')->orderBy('created_at', 'desc');

        if (!is_null($type)) {
            $query->where('type', $type);
        }
        if (!is_null($title)) {
            $query->where('title', $title);
        }

        return ArticleResource::collection($query->paginate());
    }

    public function show()
    {
        $article = Article::findOrFail(request('id'));

        return new ArticleResource($article);
    }

    public function create()
    {
        \DB::transaction(function () {
            $type = request('type');
            $title = request('title');
            $remark = request('remark');
            $content = request('content');

            Taxonomy::where('pid', Taxonomy::ARTICLE_TYPE)->findOrFail($type);
            if (!$title) {
                throw new JsonException('文章标题不能为空');
            }

            Article::create([
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'remark' => $remark
            ]);
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $type = request('type');
            $title = request('title');
            $remark = request('remark');
            $content = request('content');
            $display = request('display');

            $article = Article::findOrFail(request('id'));

            Taxonomy::where('pid', Taxonomy::ARTICLE_TYPE)->findOrFail($type);
            if (!$title) {
                throw new JsonException('文章标题不能为空');
            }

            $article->update([
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'remark' => $remark,
                'display' => $display
            ]);
        });

        return [];
    }

    public function delete()
    {
        Article::destroy(request('id'));

        return [];
    }
}