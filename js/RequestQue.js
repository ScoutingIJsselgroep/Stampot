

var RequestQue = new Class({
	que: [],
	current: null,
	request: null,
	request_delay: null,
	initialize : function(){
		//this.que = [];
		this.request = new Request({
		    method: 'get',
		    noCache: true,
		    onComplete: function(result_string){
		    	if(this.request.isSuccess()) {
		    		data = JSON.decode(result_string, true);
		    		if(data && !data.error) {
				        if(typeof window[this.current.type] == 'function') {
				        	window[this.current.type](data);
				        }
				        
			    		this.current = null;
				    	this.que.shift();
				    	// current ook uit de opgeslagen que halen
				    	LocalStorage.set('request_que', this.que);
				        this.run();
		    		} else if(data) {
		    			console.log(data);
		    		} else {
		    			console.log(result_string);
		    		}
		    	} else {
		    		// een minuut later nog een keer proberen
		    		this.request_delay = this.run.delay(60000, this);
		    	}
		    }.bind(this)
		});
		this.que = LocalStorage.get('request_que');
		if(!this.que) {
			this.que = [];
		} else {
			this.run();
		}
	},
	add: function(url, type){
		this.que.push({
			url: url,
			type: type
		});
		LocalStorage.set('request_que', this.que);
		
		this.run();
	},
	run: function() {
		if(this.request_delay) {
			clearTimeout(this.request_delay);
			this.request_delay = null;
		}
		if(!this.request.isRunning()) {
			if(this.current) {
				// de huidige is blijkbaar mislukt
				this.request.send({
					url: this.current.url
				});
			} else {
				this.current = this.que[0];
				if(this.current) {
					// de volgende starten
					this.request.send({
						url: this.current.url
					});
				}
			}
		}
	}
});