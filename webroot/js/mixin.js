define(["require","websockets/websocket","jquery"],function(e){var t=e("websockets/websocket"),n=e("jquery"),r={componentDidMount:function(){t.subscribe({topic:this.recieveUri,method:this.recieve}),this.fetch()},recieve:function(t){var t=JSON.parse(t[0]);n.isFunction(this.recieved)&&this.recieved(t)},fetch:function(){var t=this;n.ajax({url:this.fetchUrl+"/"+this.props.id+".json",method:"GET",dataType:"json",cache:!1}).done(function(e){n.isFunction(t.fetched)&&t.fetched(e)}).fail(function(e){console.log("Unable to fetch data")})},send:function(t){var r=this;n.ajax({url:this.sendUrl+".json",method:"POST",dataType:"json",cache:!1,data:t}).done(function(e){n.isFunction(r.sent)&&r.sent(e)}).fail(function(e){console.log("Unable to send data")})}};return r});