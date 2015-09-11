define(function(require) {

    var websocket = require('gintonic_c_m_s/js/websocket/websocket');
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
            this.recieved(data);
        },

        fetch: function(){
            var that = this;
            $.ajax({
                url: this.fetchUrl,
                method: "POST",
                dataType: 'json',
                cache: false,
                data: {
                    id: [this.props.id]
                }
            })
            .done(function(data){
                that.fetched(data);
            })
            .fail(function(data){
                console.log('fail');
                console.log(data);
            });
        }, 

        send: function(data) {
            var that = this;
            $.ajax({
                url: this.sendUrl,
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
                console.log('fail');
                console.log(data);
            });
        },
    };
    
    return CommunicationMixin;

});
