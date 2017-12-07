<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/27
 * Time: 下午4:51
 */

namespace App\Http\Controllers\Admin;

use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyResource;
use App\Taxonomy;

class TaxonomyController extends Controller
{
    public function list()
    {
        $pid = request('pid');

        $query = Taxonomy::query()->with('_parent')->orderBy('order');

        if (!is_null($pid)) {
            $query->where('pid', $pid);
        }

        return TaxonomyResource::collection($query->paginate());
    }

    public function create()
    {
        $this->validate(request(), [
            'pid' => 'required',
            'name' => 'required',
        ]);
        $pid = request('pid');
        $name = request('name');
        $order = request('order');
        $display = request('display');
        $remark = request('remark');

        $taxonomy = new Taxonomy();

        if (!is_null($pid)) {
            if ($pid !== 0) {
                Taxonomy::findOrFail($pid);
            }
            $taxonomy->pid = $pid;
        }
        if (!is_null($name)) {
            $taxonomy->name = $name;
        }
        if (!is_null($order)) {
            $taxonomy->order = $order;
        }
        if (!is_null($display)) {
            if (!in_array($display, [0, 1])) {
                throw new JsonException('display参数错误');
            }
            $taxonomy->display = $display;
        }
        if (!is_null($remark)) {
            $taxonomy->remark = $remark;
        }
        $taxonomy->save();

        return new TaxonomyResource($taxonomy);
    }

    public function update()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            foreach ($input as $item) {
                $pid = array_get($item, 'pid');
                $name = array_get($item, 'name');
                $order = array_get($item, 'order');
                $display = array_get($item, 'display');
                $remark = array_get($item, 'remark');

                $taxonomy = Taxonomy::findOrFail(array_get($item, 'id'));

                if (!is_null($pid)) {
                    if ($pid !== 0) {
                        Taxonomy::findOrFail($pid);
                    }
                    $taxonomy->pid = $pid;
                }
                if (!is_null($name)) {
                    $taxonomy->name = $name;
                }
                if (!is_null($order)) {
                    $taxonomy->order = $order;
                }
                if (!is_null($display)) {
                    if (!in_array($display, [0, 1])) {
                        throw new JsonException('display参数错误');
                    }
                    $taxonomy->display = $display;
                }
                if (!is_null($remark)) {
                    $taxonomy->remark = $remark;
                }
                $taxonomy->save();
            }
        });

        return [];
    }

    public function delete()
    {
        $taxonomy = Taxonomy::findOrFail(request('id'));

        if ($taxonomy->pid === 0) {
            throw new JsonException('不能删除顶级分类');
        }

        $taxonomy->delete();

        return [];
    }
}