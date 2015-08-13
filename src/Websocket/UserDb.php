<?php

namespace GintonicCMS\Websocket;

use Cake\Controller\Controller;
use Thruway\Authentication\WampCraUserDbInterface;

/**
 * Class UserDb
 */
class UserDb implements WampCraUserDbInterface
{

    /**
     * @var Controller
     */
    private $controller;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controller = new Controller();
        $this->controller->loadModel('Users');
    }

    /**
     * Get user by id
     *
     * @param string $authId Username
     * @return bool
     */
    public function get($id)
    {
        if ($id == 'server') {
            return [
                // config via config information
                "authid" => 'server',
                "key" => 'server',
                "salt" => null
            ];
        }

        $user = $this->controller->Users->findById($id)->first();
        if ($user) {
            return [
                "authid" => $user->id,
                "key" => $user->email,
                "salt" => null
            ];
        }

        return false;
    }
}
