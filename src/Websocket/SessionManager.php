<?php

namespace Websockets\Websocket;

use Thruway\Peer\Client;

class SessionManager extends Client
{
    public $sessions = [];

    /**
     * @param \Thruway\ClientSession $session ClientSession
     * @param \Thruway\Transport\TransportInterface $transport Transport
     */
    public function onSessionStart($session, $transport)
    {
        $session->subscribe('wamp.metaevent.session.on_join', [$this, 'onJoin']);
        $session->subscribe('wamp.metaevent.session.on_leave', [$this, 'onLeave']);
        $session->register('server.get_user_sessions', [$this, 'getUserSession']);
    }

    /**
     * TODO doc block
     */
    public function onJoin($args)
    {
        $this->sessions[$args[0]->authid][] = $args[0]->session;
    }

    /**
     * TODO doc block
     */
    public function onLeave($args)
    {
        //remove the session
        if (!isset($this->sessions[$args[0]->authid])) {
            return;
        }

        foreach ($this->sessions[$args[0]->authid] as $k => $session) {
            if ($session === $args[0]->session) {
                unset($this->sessions[$args[0]->authid][$k]);
            }
        }
        //$this->sessions[$args[0]->authid] = $args[0]->session;
    }

    /**
     * TODO doc block
     */
    public function getUserSession(array $userIds)
    {
        $users = array_intersect_key($this->sessions, array_flip($userIds));
        $sessions = [];
        array_walk_recursive($users, function ($session) use (&$sessions) {
            $sessions[] = $session;
        });
        return $sessions;
    }
}
