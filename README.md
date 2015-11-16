# Websockets plugin for CakePHP

This is a very early work in progress. Do not use.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require gintonicweb/websockets
```

Add the following to your `bootstrap.php` file

## Controller setup

Load the websockets component in the Controller of your choice

```
$this->loadComponent('Websockets.Websocket');
```

When an actoin is trigged via regular http, it can in turn push data to the targeted 
users registered via websockets. Use the `_ws` keyword to define the wbsocket  content
and the options `users` and `data` 

```
$this->set('_ws', [
    'users' => $this->Users->find()->all()->toArray(),
    'data' => $message->toArray()
]);
```

## Authenticating users

TODO (override src/Websocket/UserDb)

## Runnin the websocket server

You can use systemd to use the websocket server as a linux service. Create the
file ```/etc/systemd/system/wsserver.service``` with the following content

```
[Unit]
Description=Websockets Server

[Service]
ExecStart=/bin/sh -c "cd /var/www/YOURAPP && vendor/bin/wsserver"

[Install]
WantedBy=multi-user.target
```

then run the following commands

```
systemctl enable wsserver.service
systemctl start wsserver.service
```
