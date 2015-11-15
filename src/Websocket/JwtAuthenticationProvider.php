<?php

namespace Websockets\Websocket;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use JWT;
use Thruway\Authentication\AbstractAuthProviderClient;

class JwtAuthenticationProvider extends AbstractAuthProviderClient
{
    /**
     * Get which auth method this AuthProvider uses
     *
     * return string
     */
    public function getMethodName()
    {
        return 'jwt';
    }

    /**
     * Authenticate users based on their JWT. This is inspired by
     * the method _findUser in admads JWT plugin
     *
     * @see https://github.com/ADmad/cakephp-jwt-auth/blob/master/src/Auth/JwtAuthenticate.php
     * @param string $token The token identifier.
     * @param mixed $extra Unused
     * @return array
     */
    public function processAuthenticate($token, $extra = null)
    {
        try {
            $token = JWT::decode($token, Security::salt(), Configure::read('Websockets.allowedAlgs'));
        } catch (Exception $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            return ["FAILURE"];
        }

        if ($token->id == 'server') {
            return ["SUCCESS", ["authid" => $token->id]];
        }

        $fields = Configure::read('Websockets.fields');
        $table = TableRegistry::get(Configure::read('Websockets.userModel'));

        $conditions = [$table->aliasField($fields['id']) => $token->id];
        if (!empty(Configure::read('Websockets.scope'))) {
            $conditions = array_merge($conditions, Configure::read('Websockets.scope'));
        }
        $result = $table->find('all')
            ->where($conditions)
            ->first();

        if (empty($result)) {
            return ["FAILURE"];
        }
        return ["SUCCESS", ["authid" => $result->id]];
    }
}
