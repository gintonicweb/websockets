<?php

namespace Websockets\Websocket;

use Cake\Core\Configure;
use Psr\Log\NullLogger;
use Thruway\Logging\Logger;
use Thruway\Peer\Client as ThruwayClient;
use Thruway\Transport\PawlTransportProvider;

class Client extends ThruwayClient
{
    public $timeout = 5;

    /**
     * TODO doc block
     */
    public function __construct()
    {
        Logger::set(new NullLogger());
        parent::__construct('realm1');
        $ip = Configure::read('Websockets.ip');
        $port = Configure::read('Websockets.port');
        $this->addTransportProvider(new PawlTransportProvider("ws://" . $ip . ":" . $port . "/"));
    }

    /**
     * TODO doc block
     */
    protected function _uri($controller, $action)
    {
        return strtolower($controller . '.' . $action);
    }

    /**
     * TODO doc block
     */
    public function execute()
    {
        $this->start(false);
        $this->getLoop()->addTimer($this->timeout, [$this, 'kill']);
        $this->getLoop()->run();
    }

    /**
     * TODO doc block
     */
    public function kill()
    {
        $this->getLoop()->stop();
    }
}
