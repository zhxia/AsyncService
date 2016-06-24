<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-24
 * Time: 下午7:45
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