<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\Account;
use Modules\Common\Entities\Article;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\ArticleResource;
use Modules\Common\Entities\Taxonomy;

class ArticleController extends Controller
{
    public function index()
    {
        $type = request('type');
        $title = request('title');

        $query = Article::query()->with('_type')->orderBy('id', 'desc');

        if (!is_null($type)) {
            $query->where('type', $type);
        }
        if (!is_null($title)) {
            $query->where('title', 'like', "%$title%");
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
            $remark = request('remark', '');
            $content = request('content', '');

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
        $id = request('id');
        $type = request('type', null);
        $title = request('title', null);
        $remark = request('remark', null);
        $content = request('content', null);
        $display = request('display', null);

        $article = Article::findOrFail($id);

        if (!is_null($type)) {
            Taxonomy::where('pid', Taxonomy::ARTICLE_TYPE)->findOrFail($type);
            $article->type = $type;
        }
        if (!is_null($title)) {
            if (empty($title)) {
                throw new JsonException('标题不能为空');
            }
            $article->title = $title;
        }
        if (!is_null($remark)) {
            $article->remark = $remark;
        }
        if (!is_null($content)) {
            $article->content = $content;
        }
        if (!is_null($display)) {
            if (!in_array($display, [0, 1])) {
                throw new JsonException('display error');
            }
            $article->display = $display;
        }
        $article->save();

        return [];
    }

    public function delete()
    {
        Article::destroy(request('id'));

        return [];
    }
}