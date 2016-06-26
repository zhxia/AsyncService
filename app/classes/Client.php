<?php

/**
 * Created by PhpStorm.
 * User: zhxia
 * Date: 16-6-23
 * Time: 下午3:26
 */
namespace App;

use App\Util\Base;
use App\Util\Logger;

class Client extends Base
{
    private $client;

    public function __construct()
    {
        $this->client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $this->client->on("connect", array($this, 'onConnect'));
        $this->client->on("receive", array($this, 'onReceive'));
        $this->client->on("error", array($this, 'onError'));
        $this->client->on("close", array($this, 'onClose'));
    }

    public function run()
    {
        //connect to server
        $this->client->connect('127.0.0.1', 10086, 0.5);
    }

    public function onConnect($cli)
    {
        $data = array('method' => '\App\Service\Common::getTime', 'params'=>array());
        $cli->send($this->pack($data));
    }

    public function onReceive($cli, $data)
    {
        print_r($this->unpack($data, true));
        $cli->close();
    }

    public function onError($cli)
    {
        Logger::getInstance()->getLog()->trace('error');
    }

    public function onClose($cli)
    {
        Logger::getInstance()->getLog()->trace('close');
    }
}