<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2017/11/21
 * Time: 下午4:01
 */

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

class JsonException extends \Exception
{
    public function report()
    {

    }

    public function render()
    {
        return new JsonResponse([
            'code' => $this->getCode() === 0 ? -1 : $this->getCode(),
            'message' => $this->getMessage()
        ]);
    }
}