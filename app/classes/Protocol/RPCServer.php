<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-21
 * Time: 上午10:05
 */

namespace App\Protocol;


use App\Util\Base;
use App\Util\Logger;

class RPCServer extends Base
{

    function __construct()
    {
    }

    public function onConnect(\swoole_server $serv, $fd, $from_fd)
    {
        Logger::getInstance()->getLog()->trace('Client connected!');
    }

    public function onReceive(\swoole_server $serv, $fd, $from_fd, $data)
    {
        $request = $this->unpack($data, true);
        $result = call_user_func_array($request['method'], $request['params']);
        $serv->send($fd, $this->pack(array('result' => $result)));
    }

    public function onClose(\swoole_server $serv, $fd, $from_fd)
    {

    }
}