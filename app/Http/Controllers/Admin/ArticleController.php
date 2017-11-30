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

    public function create()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            foreach ($input as $item) {
                $type = array_get($item, 'type');
                $title = array_get($item, 'title');
                $remark = array_get($item, 'remark');
                $content = array_get($item, 'content');

                $article = new Article();

                if (!is_null($type)) {
                    Taxonomy::where('pid', Taxonomy::ARTICLE_TYPE)->findOrFail($type);
                    $article->type = $type;
                } else {
                    throw new JsonException('文章类型不能为空');
                }
                if (!is_null($title)) {
                    $article->title = $title;
                } else {
                    throw new JsonException('文章标题不能为空');
                }
                if (!is_null($remark)) {
                    $article->remark = $remark;
                }
                if (!is_null($content)) {
                    $article->content = $content;
                }
                $article->save();
            }
        });

        return [];
    }

    public function update()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            foreach ($input as $item) {
                $type = array_get($item, 'type');
                $title = array_get($item, 'title');
                $remark = array_get($item, 'remark');
                $content = array_get($item, 'content');
                $display = array_get($item, 'display');

                $article = Article::findOrFail(array_get($item, 'id'));

                if (!is_null($type)) {
                    Taxonomy::where('pid', Taxonomy::ARTICLE_TYPE)->findOrFail($type);
                    $article->type = $type;
                }
                if (!is_null($title)) {
                    $article->title = $title;
                }
                if (!is_null($remark)) {
                    $article->remark = $remark;
                }
                if (!is_null($content)) {
                    $article->content = $content;
                }
                if (!is_null($display)) {
                    $article->display = $display;
                }
                $article->save();
            }
        });

        return [];
    }

    public function delete()
    {
        Article::destroy(request('id'));

        return [];
    }
}