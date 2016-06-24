<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-20
 * Time: 下午7:24
 */
require './init.php';
$config = Swoole::$php->config['server'];
//override php.ini configuration
if (isset($config['php']) && is_array($config['php'])) {
    foreach ($config['php'] as $key => $val) {
        ini_set($key, $val);
    }
}
$server = new \App\Server($config);
$server->run();
