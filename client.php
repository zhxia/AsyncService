#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-24
 * Time: 下午7:49
 */
define('DEBUG', 'on');
define('FRAMEWORK_PATH', '/data/www/public/framework');
define('WEBPATH', __DIR__);
require FRAMEWORK_PATH . '/libs/lib_config.php';

$cloud = \Swoole\Client\SOA::getInstance();
$cloud->setEncodeType(false, true);
/*$cloud->putEnv('app', 'test');
$cloud->putEnv('appKey', 'test1234');*/
$cloud->addServers(array('127.0.0.1:10086'));
while (true) {
    $data = array(
        'ecode' => 'C18070',
        'car_model' => '77',
        'openUDID' => 'b9cc07eab27ea214585d3488709d076a310a7925',
        'vcode' => 'C18070',
        'os' => 'iOS',
        'cartype' => '02',
        'systemVersion' => '9.3.2',
        'step' => '3',
        'carno' => '皖AXT891',
        'app' => 'QueryViolations',
        'appChannel' => 'App Store',
        'cUDID' => 'BE39F37C-2BE9-46AD-8A72-E8DA874A36B1',
        'appVersion' => '5.9.0',
        'model' => 'iPhone7,1',
        '_cityCode' => '0551',
        'apikey' => 'hefei_ahedt',
        'api' => 'hefei_ahedt',
    );
    $ret = $cloud->task('App\Service\Prefetch::doPrefetch', array($data));
    $cloud->wait(0.01);
    var_dump($ret->getResult());
    sleep(1);
}