<?php

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__).'/../../../config/bootstrap.php';

use GintonicCMS\Websocket\UserDb;
use GintonicCMS\Websocket\SessionManager;
use Thruway\Authentication\WampCraAuthProvider;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use Thruway\Authentication\AuthenticationManager;

$router = new Router();


// setup some users to auth against
$userDb = new UserDb();

$authMgr = new AuthenticationManager();
$router->setAuthenticationManager($authMgr);
$router->addInternalClient($authMgr);

$authProvClient = new WampCraAuthProvider(["realm1"]);
$authProvClient->setUserDb($userDb);
$router->addInternalClient($authProvClient);


$sessionMgr = new SessionManager("realm1");
$router->addInternalClient($sessionMgr);


$transportProvider = new RatchetTransportProvider("127.0.0.1", 9090);
$router->addTransportProvider($transportProvider);
$router->start();
