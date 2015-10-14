<?php

namespace Websockets\Websocket;


use Cake\Utility\Security;
use JWT;
use Thruway\Authentication\ClientAuthenticationInterface;
use Thruway\Common\Utils;
use Thruway\Logging\Logger;
use Thruway\Message\AuthenticateMessage;
use Thruway\Message\ChallengeMessage;

/**
 * Class ClientWampCraAuthenticator
 */
class ClientJwtAuthenticator implements ClientAuthenticationInterface
{

    /**
     * @var string|int
     */
    public $authid;


    /**
     * Constructor
     *
     * @param string|int $authid
     * @param string $key
     */
    public function __construct($authid)
    {
        $this->authid = $authid;
    }

    /**
     * Get Authenticate message from challenge message
     *
     * @param \Thruway\Message\ChallengeMessage $msg
     * @return \Thruway\Message\AuthenticateMessage|boolean
     */
    public function getAuthenticateFromChallenge(ChallengeMessage $msg)
    {
        $token = [
            'id' => $this->authid,
            'exp' => time() + 60,
        ];
        $token = JWT::encode($token, Security::salt());
        return new AuthenticateMessage($token);
    }

    /**
     * Get authentication ID
     *
     * @return string
     */
    public function getAuthId()
    {
        return $this->authid;
    }

    /**
     * Set authentication ID
     *
     * @param string $authid
     */
    public function setAuthId($authid)
    {
        $this->authid = $authid;
    }

    /**
     * Get list authenticate methods
     *
     * @return array
     */
    public function getAuthMethods()
    {
        return ['jwt'];
    }

}
