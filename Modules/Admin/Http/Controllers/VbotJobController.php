<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/9/12
 * Time: 下午12:09
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\VbotJobResource;
use Modules\Common\Entities\VbotJob;
use Carbon\Carbon;

class VbotJobController extends Controller
{
    public function index()
    {
        $mobile = request('mobile');
        $date = request('created_at');

        $query = VbotJob::query()->with('_user');

        if ($mobile) {
            $query->whereHas('_user', function ($query) use ($mobile) {
                $query->where('mobile', $mobile);
            });
        }

        if (!is_null($date)) {
            $query->where('created_at', '>=', Carbon::parse($date[0]));
            $query->where('created_at', '<=', Carbon::parse($date[1]));
        }

        $query->orderByDesc('id');

        return VbotJobResource::collection($query->paginate());
    }
}