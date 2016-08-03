<?php
/**
 * Created by PhpStorm.
 * User: zhxia84@gmail.com
 * Date: 6/26/16
 * Time: 9:40 PM
 */

namespace App\Service;

ini_set('default_socket_timeout', -1);
class Common
{
    /**
     * @var \Redis
     */
    private static $redis = null;

    public static function getTime($isTimestamp = false)
    {
        return $isTimestamp ? time() : date('Y-m-d H:i:s');
    }

    public static function testRedis()
    {
        if (self::$redis == null) {
            $config = \swoole::$php->config['common'];
            self::$redis = new \Redis();
            self::$redis->pconnect($config['redis']['host'], $config['redis']['port'], 5);
            self::$redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
            file_put_contents('/tmp/zhxia.log', 'connect!' . PHP_EOL,FILE_APPEND);
        }
        return self::$redis->incr('count');
    }
}