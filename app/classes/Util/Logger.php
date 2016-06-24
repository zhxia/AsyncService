<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-23
 * Time: 下午7:17
 */

namespace App\Util;


use Swoole\IFace\Log;
use Swoole\Log\EchoLog;

class Logger
{
    /**
     * @var Logger
     */
    private static $instance = null;

    /**
     * @var Log
     */
    private static $log;

    private function __construct()
    {
    }


    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return Log|EchoLog
     */
    public function getLog()
    {
        if (!self::$log) {
            self::$log = new EchoLog(true);
        }
        return self::$log;
    }
}