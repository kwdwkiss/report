<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Cly\Vbot\Contact;

class Officials extends Contacts
{
    public function isOfficial($verifyFlag)
    {
        return ($verifyFlag & 8) != 0;
    }
}
