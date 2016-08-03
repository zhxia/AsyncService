#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-24
 * Time: 下午7:49
 */
define('DEBUG', 'on');
define('FRAMEWORK_PATH', '/home/zhxia/workspace/php/framework');
define('WEBPATH', __DIR__);
require FRAMEWORK_PATH . '/libs/lib_config.php';

$cloud = \Swoole\Client\SOA::getInstance();
$cloud->setEncodeType(false, true);
/*$cloud->putEnv('app', 'test');
$cloud->putEnv('appKey', 'test1234');*/
$cloud->addServers(array('127.0.0.1:10086'));
while(true) {
    $ret = $cloud->task('App\Service\Common::testRedis');
    $cloud->wait(0.01);
    var_dump($ret->getResult());
}