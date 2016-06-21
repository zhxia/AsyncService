<?php
/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-21
 * Time: 上午10:35
 */
$server = array(
    'host' => '127.0.0.1',
    'port' => 10086,
    'server_name' => 'rpc',
    'pid_file' => '/tmp/rpc.pid',
    'swoole' => array(
        'log_file' => ROOT_PATH . '/logs/',
        'worker_num' => 2,
        'daemonize' => false,
        'max_request' => 0,
        'open_length_check' => true,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_body_offset' => 4,
    ),
);
return $server;