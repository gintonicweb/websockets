define(function(require) {

    var autobahn = require('autobahn');
    var instance = null;

    var user = key = getCookie('Jwt');

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

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
        if (method === "wampcra") {
            var keyToUse = key;
            if (typeof extra.salt !== 'undefined') {
                keyToUse = autobahn.auth_cra.derive_key(key, extra.salt);
            }
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
