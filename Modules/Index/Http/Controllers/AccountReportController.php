<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/16
 * Time: 下午9:24
 */

namespace Modules\Index\Http\Controllers;


use Modules\Common\Entities\AccountReport;
use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AccountReportResource;

class AccountReportController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $accountReports = AccountReport::query()
            ->where('user_id', $user->id)
            ->where('display', 1)
            ->orderBy('id', 'desc')
            ->paginate();

        return AccountReportResource::collection($accountReports);
    }

    public function hide()
    {
        $id = request('id');

        $user = \Auth::guard('user')->user();

        if (!$user->isAuth()) {
            throw new JsonException('认证用户才能删除记录');
        }

        $accountReport = AccountReport::where('user_id', $user->id)->findOrFail($id);

        $accountReport->update([
            'display' => 0
        ]);

        return [];
    }
}