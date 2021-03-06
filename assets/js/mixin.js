define(function(require) {

    var websocket = require('websockets/websocket');
    var $ = require('jquery');

    var CommunicationMixin = {

        componentDidMount: function() {
            websocket.subscribe({
                topic: this.recieveUri,
                method: this.recieve,
            });
            this.fetch();
        },

        recieve: function(data){
            var data = JSON.parse(data[0]);
            if ($.isFunction(this.recieved)) {
                this.recieved(data);
            }
        },

        fetch: function(){
            var that = this;
            $.ajax({
                url: this.fetchUrl + '/' + this.props.id + '.json',
                method: "GET",
                dataType: 'json',
                cache: false,
            })
            .done(function(data){
                if ($.isFunction(that.fetched)) {
                    that.fetched(data);
                }
            })
            .fail(function(data){
                console.log('Unable to fetch data');
            });
        }, 

        send: function(data) {
            var that = this;
            $.ajax({
                url: this.sendUrl + '.json',
                method: "POST",
                dataType: 'json',
                cache: false,
                data: data
            })
            .done(function(data){
                if ( $.isFunction(that.sent) ) {
                    that.sent(data);
                }
            })
            .fail(function(data){
                console.log('Unable to send data');
            });
        },
    };
    
    return CommunicationMixin;

});
