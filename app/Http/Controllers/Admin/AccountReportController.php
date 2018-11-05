<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountReport;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;
use App\Taxonomy;

class AccountReportController extends Controller
{
    public function index()
    {
        $account_name = request('account_name');
        $account_type = request('account_type');
        $report_type = request('report_type');
        $created_at = request('created_at');

        $query = AccountReport::query()->with('_user', '_accountType', '_type', '_attachments')
            ->orderBy('id', 'desc');

        if ($account_name) {
            $query->where('account_name', $account_name);
        }
        if ($account_type) {
            $query->where('account_type', $account_type);
        }
        if ($report_type) {
            $query->where('type', $report_type);
        }
        if ($created_at) {
            $query->where('created_at', '>', $created_at[0]);
            $query->where('created_at', '<', date('Y-m-d', strtotime($created_at[1] . ' +1 day')));
        }

        return AccountReportResource::collection($query->paginate());
    }

    public function show()
    {
        $accountReport = AccountReport::findOrFail(request('id'));

        return new AccountReportResource($accountReport);
    }

    public function create()
    {
        //管理员无需在后台添加举报信息
    }

    public function update()
    {
        \DB::transaction(function () {
            $item = json_decode(request()->getContent(), true);

            $id = array_get($item, 'id');
            $type = array_get($item, 'type');
            $display = array_get($item, 'display', 0);
            $remark = array_get($item, 'remark', '');

            $accountReport = AccountReport::findOrFail($id);

            Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($type);

            $accountReport->update([
                'type' => $type,
                'display' => $display,
                'remark' => $remark
            ]);
        });

        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $accountReport = AccountReport::find(request('id'));
            if ($accountReport) {
                $accountReport->delete();
                $account = Account::where('name', $accountReport->account_name)
                    ->where('type', $accountReport->account_type)
                    ->first();
                $account->decrement('report_count');
            }
        });

        return [];
    }
}