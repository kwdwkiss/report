<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/28
 * Time: 下午4:03
 */

namespace App\Http\Controllers\Admin;

use App\AccountReport;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccountReportResource;
use App\Taxonomy;

class AccountReportController extends Controller
{
    public function list()
    {
        $account_name = request('account_name');
        $account_type = request('account_type');
        $report_type = request('report_type');
        $created_at = request('created_at');

        $query = AccountReport::query()->with('_account._type', '_type')->orderBy('created_at', 'desc');

        if ($account_name) {
            $query->whereHas('_account', function ($query) use ($account_name) {
                $query->where('name', $account_name);
            });
        }
        if ($account_type) {
            $query->whereHas('_account', function ($query) use ($account_type) {
                $query->where('type', $account_type);
            });
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

    public function create()
    {
        //管理员无需在后台添加举报信息
    }

    public function update()
    {
        \DB::transaction(function () {
            $input = json_decode(request()->getContent(), true);
            foreach ($input as $item) {
                $id = array_get($item, 'id');
                $type = array_get($item, 'type');
                $display = array_get($item, 'display');
                $remark = array_get($item, 'remark');

                $accountReport = AccountReport::findOrFail($id);

                if (!is_null($type)) {
                    Taxonomy::where('pid', Taxonomy::REPORT_TYPE)->findOrFail($type);
                    $accountReport->type = $type;
                }
                if (!is_null($display)) {
                    $accountReport->display = $display;
                }
                if (!is_null($remark)) {
                    $accountReport->remark = $remark;
                }
                $accountReport->save();
            }
        });

        return [];
    }

    public function delete()
    {
        \DB::transaction(function () {
            $accountReport = AccountReport::with('_account')->find(request('id'));
            if ($accountReport) {
                $accountReport->delete();
                $accountReport->_account->decrement('report_count');
            }
        });

        return [];
    }
}