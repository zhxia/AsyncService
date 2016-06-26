<?php
/**
 * Created by PhpStorm.
 * User: zhxia84@gmail.com
 * Date: 6/26/16
 * Time: 9:40 PM
 */

namespace App\Service;


class Common
{
    public static function getTime($isTimestamp = false)
    {
        return $isTimestamp ? time() : date('Y-m-d H:i:s');
    }
}