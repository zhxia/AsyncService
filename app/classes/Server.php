<?php

/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-20
 * Time: 下午7:40
 */
namespace App;

use App\Protocol\RPCServer;
use Swoole\Log;

class Server
{
    private static $SERVER_NAME = 'RPC';
    private static $server;
    private static $worker_id;
    private static $config;
    private $protocol;
    private $pid_file;
    /**
     * @var \Swoole\IFace\Log
     */
    private $logger;


    function __construct($config = array())
    {
        self::$SERVER_NAME = $config['server_name'];
        self::$server = new \swoole_server('127.0.0.1', 10086, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->protocol = new RPCServer();
        $this->pid_file = '/tmp/' . self::$SERVER_NAME . '.pid';
        $this->logger = new Log\EchoLog(true);
        self::$config = $config;
    }

    public function run()
    {
        $config = array();
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

    public function onMasterStart(\swoole_server $serv)
    {
        \swoole_set_process_name(self::$SERVER_NAME . '-Master');
        file_put_contents($this->pid_file, self::$server->master_pid);
        $this->logger->trace('master start');
    }

    public function onMasterShutdown(\swoole_server $serv)
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }
        $this->logger->trace('master shutdown');
    }

    public function onManagerStart(\swoole_server $serv)
    {
        \swoole_set_process_name(self::$SERVER_NAME . '-Manager');
        $this->logger->trace('manager start');
    }

    public function onManagerStop(\swoole_server $serv)
    {
        $this->logger->trace('manager shutdown');
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
        Echo $this->logger->trace('worker-' . $worker_id . ' shutdown');
    }
}