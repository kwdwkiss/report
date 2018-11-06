<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/20
 * Time: 下午4:16
 */

namespace Modules\Index\Http\Controllers;

use Modules\Common\Entities\AmountBill;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\AmountBillResource;

class AmountController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        $amountBill = AmountBill::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return AmountBillResource::collection($amountBill);
    }
}