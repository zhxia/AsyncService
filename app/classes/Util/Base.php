<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-23
 * Time: 下午3:43
 */

namespace App\Util;


class Base
{
    private $encrypt = false;

    public function enableEncrypt($flag = false)
    {
        $this->encrypt = $flag;
    }

    public function pack($data, $jsonEncode = true)
    {
        $strData = $jsonEncode ? json_encode($data) : $data;
        return pack('N', strlen($strData)) . $strData;
    }

    public function unpack($data, $jsonDecode = true)
    {
        $data = substr($data, 4);
        return $jsonDecode ? json_decode($data, true) : $data;
    }
}