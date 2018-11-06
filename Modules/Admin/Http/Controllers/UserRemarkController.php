<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/21
 * Time: 下午1:16
 */

namespace Modules\Admin\Http\Controllers;


use Modules\Common\Exceptions\JsonException;
use Modules\Common\Http\Controllers\Controller;
use Modules\Common\Transformers\UserRemarkResource;
use Modules\Common\Entities\User;
use Modules\Common\Entities\UserRemark;

class UserRemarkController extends Controller
{
    public function index()
    {
        $id = request('id');

        $user = User::findOrFail($id);

        return UserRemarkResource::collection($user->_remark);
    }

    public function create()
    {
        $id = request('id');
        $content = request('content');

        User::findOrFail($id);

        if (!$content) {
            throw new JsonException('备注内容不能为空');
        }

        UserRemark::create([
            'user_id' => $id,
            'content' => $content
        ]);

        return [];
    }
}