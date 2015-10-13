<?php
use Cake\Core\Configure;
Configure::write('Websockets', [
    'userModel' => 'Users',
    'allowedAlgs' => ['HS256'],
    'scope' => ['Users.active' => 1],
    'fields' => [
        'id' => 'id'
    ],
    'contain' => false

]);
