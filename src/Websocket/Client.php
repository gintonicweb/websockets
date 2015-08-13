<?php

namespace GintonicCMS\Websocket;

use Cake\Contoller\Controller;
use Cake\Core\App;
use Cake\Network\Request;
use GintonicCMS\Websocket\Procedure\PublishProcedure;
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
        $this->addTransportProvider(new PawlTransportProvider("ws://127.0.0.1:9090/"));
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
        debug('killed');
        $this->getLoop()->stop();
    }
}
