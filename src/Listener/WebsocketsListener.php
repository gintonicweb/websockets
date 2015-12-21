<?php

namespace Websockets\Listener;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Permissions\Model\Entity\Role;

class WebsocketsListener implements EventListenerInterface
{
    /**
     * Callbacks definition
     */
    public function implementedEvents()
    {
        return [
            'Auth.afterIdentify' => 'afterIdentify',
            'Users.afterSignup' => 'afterSignup',
        ];
    }

    /**
     * todo
     */
    public function afterIdentify(Event $event, array $user)
    {
        $cookie = $event->subject()
            ->_registry
            ->getController()
            ->loadComponent('Cookie');

        $this->_setSocketAddress($cookie);
    }

    /**
     * todo
     */
    public function afterSignup(Event $event)
    {
        $cookie = $event->subject()->loadComponent('Cookie');
        $this->_setSocketAddress($cookie);
    }

    /**
     * todo
     */
    protected function _setSocketAddress($cookie)
    {
        $cookie->configKey('Websockets', [
            'encryption' => false,
            'expires' => 0,
        ]);
        
        $address = [
            'ip' => Configure::read('Websockets.ip'),
            'port' => Configure::read('Websockets.port'),
        ];

        $cookie->write('Websockets', json_encode($address));
    }
}
