<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/4/15
 * Time: 下午2:38
 */

namespace Cly\Session;

class SessionGuard extends \Illuminate\Auth\SessionGuard
{
    protected function updateSession($id)
    {
        $this->session->put($this->getName(), $id);
    }
}