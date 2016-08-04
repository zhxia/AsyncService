<?php
/**
 * Created by PhpStorm.
 * User: zhxia84@gmail.com
 * Date: 2016/8/4
 * Time: 17:08
 */

namespace App;


class Log
{
    public static function __callStatic($name, $msg)
    {
        \Swoole::$php->log->$name(self::format($msg));
    }

    private static function format($msg)
    {
        $worker = "";
        if (\App\Server::$worker_id) {
            $worker = "worker@" . \APP\Server::$worker_id;
        }
        return "pid:" . posix_getpid() . " {$worker} " . implode(',', $msg);
    }
}