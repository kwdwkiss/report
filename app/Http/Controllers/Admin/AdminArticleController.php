<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AdminArticle;
use App\Article;
use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminArticleResource;
use App\Http\Resources\ArticleResource;
use App\Taxonomy;

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