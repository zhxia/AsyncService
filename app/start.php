<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-20
 * Time: 下午7:24
 */

define('ROOT_PATH', dirname(__DIR__));
define('FRAMEWORK_PATH', '../framework');
define('WEBPATH', dirname(__DIR__));
require FRAMEWORK_PATH . '/libs/lib_config.php';
Swoole\Loader::addNameSpace('App', __DIR__ . '/classes');
Swoole::$php->config->setPath(__DIR__ . '/configs');
$evn_name = 'dev';
if ($evn_name == 'dev') {
    Swoole::$php->config->setPath(__DIR__ . '/configs/dev');
}
$config = Swoole::$php->config['server'];
//override php.ini configuration
if (isset($config['php']) && is_array($config['php'])) {
    foreach ($config['php'] as $key => $val) {
        ini_set($key, $val);
    }
}
$server = new \App\Server($config);
$server->run();
