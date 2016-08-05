<?php

/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-20
 * Time: 下午7:40
 */
namespace App;

use Swoole\Log;

class Server
{
    private static $SERVER_NAME;
    private static $server;
    private static $worker_id;
    private static $config;
    private $protocol;
    private $pid_file;

    function __construct($config = array())
    {
        self::$SERVER_NAME = $config['server_name'];
        self::$config = $config;
        if (!isset(self::$config['host'])) {
            $listenHost = '127.0.0.1';
            $ipList = swoole_get_local_ip();
            foreach ($ipList as $ip) {
                if (strpos($ip, '192.168') === 0 || strpos($ip, '172.16') === 0) {
                    $listenHost = $ip;
                    break;
                }
            }
            self::$config['host'] = $listenHost;
        }
        self::$server = new \swoole_server(self::$config['host'], self::$config['port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->protocol = new \Swoole\Protocol\SOAServer();
        $this->protocol->server = $this::$server;
        $this->pid_file = sprintf(self::$config['pid_file'], self::$SERVER_NAME);

    }

    public function run($config = array())
    {
        $config = array_merge(self::$config['swoole'], $config);
        self::$server->set($config);
        self::$server->on('start', array($this, 'onMasterStart'));
        self::$server->on('shutdown', array($this, 'onMasterShutdown'));
        self::$server->on('managerStart', array($this, 'onManagerStart'));
        self::$server->on('managerStop', array($this, 'onManagerStop'));
        self::$server->on('workerStart', array($this, 'onWorkerStart'));
        self::$server->on('workerStop', array($this, 'onWorkerStop'));
        self::$server->on('connect', array($this->protocol, 'onConnect'));
        self::$server->on('receive', array($this->protocol, 'onReceive'));
        self::$server->on('close', array($this->protocol, 'onClose'));
        self::$server->start();
    }

    public static function getWorkerId()
    {
        return self::$worker_id;
    }

    public function onMasterStart(\swoole_server $serv)
    {
        \swoole_set_process_name(self::$SERVER_NAME . '-Master');
        file_put_contents($this->pid_file, self::$server->master_pid);
        \App\Log::trace('master start');
    }

    public function onMasterShutdown(\swoole_server $serv)
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }
        \App\Log::trace('master shutdown');
    }

    public function onManagerStart(\swoole_server $serv)
    {
        \swoole_set_process_name(self::$SERVER_NAME . '-Manager');
        \App\Log::trace('manager start');
    }

    public function onManagerStop(\swoole_server $serv)
    {
        \App\Log::trace('manager shutdown');
    }

    public function onWorkerStart(\swoole_server $serv, $worker_id)
    {
        self::$worker_id = $worker_id;
        if (self::$worker_id < self::$config['swoole']['worker_num']) {
            \swoole_set_process_name(self::$SERVER_NAME . ' Worker-' . $worker_id);
        } else {
            \swoole_set_process_name(self::$SERVER_NAME . ' Tasker- ' . $worker_id);
        }
    }

    public function onWorkerStop(\swoole_server $serv, $worker_id)
    {
        \App\Log::trace('worker-' . $worker_id . ' shutdown');
    }
}