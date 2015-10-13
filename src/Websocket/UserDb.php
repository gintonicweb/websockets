<?php

namespace Websockets\Websocket;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use JWT;
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
        $this->controller->loadModel(Configure::read('Websockets.userModel'));
    }

    /**
     * Get user by id
     *
     * @param string $authId Username
     * @return bool
     */
    public function get($token)
    {
        $user = $this->_findUser($token); 

        if ($user) {
            return [
                "authid" => $user['id'],
                "key" => $token,
                "salt" => null
            ];
        }

        return false;
    }

    /**
     * Find a user record.
     *
     * @param string $token The token identifier.
     * @param string $password Unused password.
     * @return bool|array Either false on failure, or an array of user data.
     */
    protected function _findUser($token, $password = null)
    {
        try {
            $token = JWT::decode($token, Security::salt(), Configure::read('Websockets.allowedAlgs'));
        } catch (Exception $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            return false;
        }
        // Token has full user record.
        if (isset($token->record)) {
            // Trick to convert object of stdClass to array. Typecasting to
            // array doesn't convert property values which are themselves objects.
            return json_decode(json_encode($token->record), true);
        }
        // Token has full user record.
        if (isset($token->server)) {
            return json_decode(json_encode($token->record), true);
        }
        $fields = Configure::read('Websockets.fields');
        $table = TableRegistry::get(Configure::read('Websockets.userModel'));
        $conditions = [$table->aliasField($fields['id']) => $token->id];
        if (!empty(Configure::read('Websockets.scope'))) {
            $conditions = array_merge($conditions, Configure::read('Websockets.scope'));
        }
        $query = $table->find('all')
            ->where($conditions);
        if (Configure::read('Websockets.contain')) {
            $query = $query->contain(Configure::read('Websockets.contain'));
        }
        $result = $query->first();
        if (empty($result)) {
            return false;
        }
        return $result->toArray();
    }
}
