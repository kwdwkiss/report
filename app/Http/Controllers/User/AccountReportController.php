<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/16
 * Time: 下午9:24
 */

namespace App\Http\Controllers\User;


use App\AccountReport;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;

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

        $accountReport = AccountReport::where('user_id', $user->id)->findOrFail($id);

        $accountReport->update([
            'display' => 0
        ]);

        return [];
    }
}