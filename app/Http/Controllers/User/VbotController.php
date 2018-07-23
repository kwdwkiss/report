<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/23
 * Time: 下午4:14
 */

namespace App\Http\Controllers\User;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use App\VbotJob;
use Cly\Vbot\VbotService;

class VbotController extends Controller
{
    public function index()
    {
        $user = \Auth::guard('user')->user();

        if (!$user) {
            throw new JsonException('请登录后再使用此功能');
        }
    }

    public function getQrcode()
    {
        $user = \Auth::guard('user')->user();

        if (!$user) {
            throw new JsonException('请登录后再使用此功能');
        }

        $vbotservice = new VbotService($user);

        return ['data' => $vbotservice->getQrCode($user)];
    }
}