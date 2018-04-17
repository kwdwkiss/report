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
use App\Http\Resources\RechargeBillResource;
use App\RechargeBill;
use App\Taxonomy;

class RechargeController extends Controller
{
    public function list()
    {
        $mobile = request('mobile');
        $payType = request('pay_type');
        $payNo = request('pay_no');
        $created_at = request('created_at');

        $query = RechargeBill::query()->with('_user')->orderBy('created_at', 'desc');

        if (!is_null($mobile)) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }
        if (!is_null($payType)) {
            $query->where('pay_type', $payType);
        }
        if (!is_null($payNo)) {
            $query->where('pay_no', $payNo);
        }
        if (!is_null($created_at)) {
            $query->where('created_at', '>', $created_at[0]);
            $query->where('created_at', '<', date('Y-m-d', strtotime($created_at[1] . ' +1 day')));
        }

        return RechargeBillResource::collection($query->paginate());
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

                $article = RechargeBill::findOrFail(array_get($item, 'id'));

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
        //RechargeBill::destroy(request('id'));

        return [];
    }
}