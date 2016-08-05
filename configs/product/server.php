<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-21
 * Time: 上午10:35
 */
$server = array(
    'port' => 10086,
    'server_name' => 'PServer',
    'pid_file' => WEBPATH . '/run/%s.pid',
    'swoole' => array(
        'log_file' => WEBPATH . '/logs/swoole.log',
        'worker_num' => 5,
        'daemonize' => true,
        'max_request' => 0,
        'package_max_length'=>2465792, //2M的包体
        'open_length_check' => true,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_body_offset' => \Swoole\Protocol\SOAServer::HEADER_SIZE,
    ),
    'php' => array(
        'error_log' => WEBPATH . '/logs/php_errors.log'
    ),
);
return $server;