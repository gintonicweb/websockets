define(function(require) {

    var $ = require('jquery');
    var autobahn = require('autobahn');
    var instance = null;

    var token = getCookie('Jwt');
    var address = JSON.parse(decodeURIComponent(getCookie('Websockets')));

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

    function getAddress() {
        var ip = window.location.hostname;
        if (address.ip != '0.0.0.0') {
            ip = address.ip
        }
        return 'ws://' + ip + ':' + address.port + '/ws';
    }

    function Websocket() {

        if (instance != null) {
            return;
        }

        this.subscribes = [];
        this.session = null;
        that = this;

        var connection = new autobahn.Connection({
            url: getAddress(),
            realm: 'realm1',
            authmethods: ["jwt"],
            onchallenge: function (session, method, extra) {
                return token;
            }
        });

        connection.onopen = function (session, details) {
            console.log("connected session with ID " + session.id);
            that.session = session;
            that.subscribes.forEach(function(sub) {
                session.subscribe(sub['topic'], sub['method'], { match: 'prefix' });
            });
        };

        connection.onclose = function () {
            console.log("disconnected", arguments);
        }

        connection.open();
    }

    Websocket.prototype.subscribe = function(sub) {
        if (typeof sub.topic === 'undefined' || sub.method === 'undefined') {
            return;
        }
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
