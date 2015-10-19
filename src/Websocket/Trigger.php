<?php

namespace Websockets\Websocket;

use Cake\Contoller\Controller;
use Cake\Core\App;
use Psr\Log\NullLogger;
use Websockets\Websocket\ClientJwtAuthenticator;
use Thruway\Logging\Logger;

class Trigger extends Client
{
    public $_controller;
    public $_call;
    public $_users;

    /**
     * TODO doc block
     */
    public function __construct($controller)
    {
        Logger::set(new NullLogger());
        parent::__construct();
        $this->_controller = $controller;
        $this->setAuthId('server');
        $this->addClientAuthenticator(new ClientJwtAuthenticator('server'));
    }

    /**
     * @param \Thruway\ClientSession $session ClientSession
     * @param \Thruway\Transport\TransportInterface $transport Transport
     */
    public function onSessionStart($session, $transport)
    {
        if ($this->_users === null) {
            $session->publish(
                $this->_call[0],
                $this->_call[1],
                $this->_call[2],
                $this->_call[3]
            )->then([$this, 'success'], [$this, 'error']);
        }else{
            $session->call('server.get_user_sessions', $this->_users)->then(
                function ($res) use ($session) {
                    $session->publish(
                        $this->_call[0],
                        $this->_call[1],
                        $this->_call[2],
                        array_merge($this->_call[3], ['eligible' => $res[0]])
                    )->then([$this, 'success'], [$this, 'error']);
                }
            );
        }
    }

    /**
     * TODO doc block
     */
    public function publish($args = [], $users = null)
    {
        $argsKw = [];
        $topic = $this->_uri($this->_controller->name, $this->_controller->request->action);
        if (!is_array($args)) {
            $args = [$args];
        }

        $this->_users = $users;

        // this undocumented array should be a data type
        $this->_call = [$topic, [json_encode($args)], $argsKw, ["acknowledge" => true]];
        $this->execute();
    }

    /**
     * TODO doc block
     */
    public function success()
    {
        $this->getLoop()->stop();
    }

    /**
     * TODO doc block
     */
    public function error($error)
    {
        $this->getLoop()->stop();
    }
}
