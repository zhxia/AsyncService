#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-20
 * Time: 下午7:24
 */
define('DEBUG', 'on');
define('FRAMEWORK_PATH', '/home/zhxia/workspace/php/framework');
define('WEBPATH', __DIR__);
require FRAMEWORK_PATH . '/libs/lib_config.php';
Swoole\Loader::addNameSpace('App', __DIR__ . '/apps/classes');
Swoole::$php->config->setPath(__DIR__ . '/configs');

$evn_name = get_cfg_var('env.name');
if (empty($evn_name)) {
    $evn_name = 'dev';
}
Swoole::$php->config->setPath(__DIR__ . '/configs/' . $evn_name);
$config = Swoole::$php->config['server'];
//override php.ini configuration
if (isset($config['php']) && is_array($config['php'])) {
    foreach ($config['php'] as $key => $val) {
        ini_set($key, $val);
    }
}

$cmd = isset($argv[1]) ? strtolower($argv[1]) : '';
if (empty($cmd)) {
    goto usage;
}
if (empty($config['pid_file'])) {
    die('config item:"pid_file" not exist!' . PHP_EOL);
}
$pid_file = sprintf($config['pid_file'], $config['server_name']);

if (is_file($pid_file)) {
    $server_pid = file_get_contents($pid_file);
} else {
    $server_pid = 0;
}

if ($cmd == 'reload') {
    if (empty($server_pid)) {
        exit("Server is not running");
    }
    posix_kill($server_pid, SIGUSR1);
    exit;
} elseif ($cmd == 'stop') {
    if (empty($server_pid)) {
        exit("Server is not running\n");
    }
    posix_kill($server_pid, SIGTERM);
    exit;
} elseif ($cmd == 'start') {
    //已存在ServerPID，并且进程存在
    if (!empty($server_pid) and posix_kill($server_pid, 0)) {
        exit("Server is already running.\n");
    }
} else {
    usage:
    exit("Usage: php {$argv[0]} start|stop|reload\n");

}

$server = new \App\Server($config);
$server->run();
