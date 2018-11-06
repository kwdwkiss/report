<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Entities\Account;
use Modules\Common\Entities\AdminArticle;
use Modules\Common\Entities\Article;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AdminArticleResource;
use Modules\Common\Transformers\ArticleResource;
use Modules\Common\Entities\Taxonomy;

class AdminArticleController extends Controller
{
    public function index()
    {
        $type = request('type');
        $title = request('title');

        $query = AdminArticle::query()->orderBy('id', 'desc');

        if (!is_null($type)) {
            $query->where('type', $type);
        }
        if (!is_null($title)) {
            $query->where('title', 'like', "%$title%");
        }

        return AdminArticleResource::collection($query->paginate());
    }

    public function show()
    {
        $article = AdminArticle::findOrFail(request('id'));

        return new AdminArticleResource($article);
    }

    public function showLast()
    {
        $article = AdminArticle::query()->orderBy('id', 'desc')->first();

        return $article ? new AdminArticleResource($article) : [];
    }

    public function create()
    {
        \DB::transaction(function () {
            $type = request('type');
            $title = request('title');
            $remark = request('remark', '');
            $content = request('content', '');

            if (!isset(AdminArticle::$types[$type])) {
                throw new JsonException('文章类型错误');
            }
            if (!$title) {
                throw new JsonException('文章标题不能为空');
            }

            AdminArticle::create([
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

            $article = AdminArticle::findOrFail(request('id'));

            if (!isset(AdminArticle::$types[$type])) {
                throw new JsonException('文章类型错误');
            }
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
        AdminArticle::destroy(request('id'));

        return [];
    }
}