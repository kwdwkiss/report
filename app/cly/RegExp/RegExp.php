<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/4
 * Time: 下午2:35
 */

namespace Cly\RegExp;


class RegExp
{
    const QQ = '/^[1-9][0-9]{4,14}$/';

    const WX = '/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,19}+$/';

    const MOBILE = '/^1(3[0-9]|4[579]|5[0-35-9]|7[0-9]|8[0-9])\d{8}$/';

    //密码必须包含字母、数字、符号两种组合且长度为8-16
    const PASSWORD='/^(?![0-9]+$)(?![a-zA-Z]+$)(?![^a-zA-Z^\d]+$).{8,16}$/';
}