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
            'Auth.afterIdentify' => 'setSocketAddress',
            'Users.afterSignup' => 'setSocketAddress',
        ];
    }

    /**
     * todo
     */
    public function setSocketAddress(Event $event, array $user)
    {
        $cookie = $event->subject()
            ->_registry
            ->getController()
            ->loadComponent('Cookie');

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
