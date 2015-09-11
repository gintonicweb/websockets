define(function(require) {

    var autobahn = require('autobahn');
    var instance = null;

    var user = "1";
    var key = "phil.laf@gmail.com";

    function Websocket() {

        if (instance != null) {
            return;
        }

        this.subscribes = [];
        this.session = null;
        that = this;

        var connection = new autobahn.Connection({
            url: 'ws://127.0.0.1:9090/ws',
            realm: 'realm1',
            authmethods: ["wampcra"],
            authid: user,
            onchallenge: onchallenge
        });

        connection.onopen = function (session, details) {
            console.log("connected session with ID " + session.id);
            console.log("authenticated using method '" + details.authmethod + "' and provider '" + details.authprovider + "'");
            console.log("authenticated with authid '" + details.authid + "' and authrole '" + details.authrole + "'");
            that.session = session;
            that.subscribes.forEach(function(sub) {
                session.subscribe(sub['topic'], sub['method'])
            });
        };

        connection.onclose = function () {
            console.log("disconnected", arguments);
        }

        connection.open();
    }

    function onchallenge (session, method, extra) {
        console.log(method, extra);
        if (method === "wampcra") {
            var keyToUse = key;
            if (typeof extra.salt !== 'undefined') {
                keyToUse = autobahn.auth_cra.derive_key(key, extra.salt);
            }
            console.log("authenticating via '" + method + "' and challenge '" + extra.challenge + "'");
            return autobahn.auth_cra.sign(keyToUse, extra.challenge);
        } else {
            throw "don't know how to authenticate using '" + method + "'";
        }
    }

    Websocket.prototype.subscribe = function(sub) {
        if (this.session == null) {
            this.subscribes.push(sub);
        } else {
            this.session.subscribe(sub['topic'], sub['method']);
        }
    }

    Websocket.getInstance = function(){
        if (instance === null) {
            instance = new Websocket();
        }
        return instance;
    }

    return Websocket.getInstance();

});
