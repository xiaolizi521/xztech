requesters = []

function LastRequester() {
  requesters.push(this);
	this.next_request = [];
	this.updater = null;
}

LastRequester.prototype.next_request; 
LastRequester.prototype.updater;

LastRequester.prototype = {
  run: function(element, url, options) {
    this.next_request[0] = [ element, url, options ];
    this._processNext();		
  },

  _processNext: function() {
    if (this.updater == null || this.updater._complete) {
      if (this.next_request[0] != null) {
        request = this.next_request.shift();
        this.updater = new Ajax.Updater(request[0], request[1], request[2]);
      }
    }   
  }
}

Ajax.Responders.register({
  onComplete: function() { requesters.each( function(requester) { requester._processNext(); }) }
});

