Rack.behavior.Remote = function (config) {
    this.p_loading = false;
    this.p_loaded = false;
    
    if (config.url) {
        this.setUrl(config.url, config.params, config.urlOptions);
    }
};

Rack.behavior.Remote.prototype = {
    /**
    * Get an object containing the default values for this class' parameters.
    */
    getParamDefaults: function () {
        return {
            url: null,
            params: {},
            urlOptions: {timeout: 10}
        };
    },
    
    /**
     * Get the Ext.UpdateManager for this widget. Enables you to perform Ajax updates.
     * @return {Ext.UpdateManager} 
     */
    getUpdateManager: function () {
        return this.getBody().getUpdateManager();
    },
    
    /**
     * Set a URL to be used to load the content for this widget.
     * @param {String/Function} url The url to load the content from or a function to call to get the url
     * @param {String/Object} params (optional) The string params for the update call or an object of the params. See Ext.UpdateManager.update() for more details. (defaults to null)
     * @param {Object} config (optional) Configuration options for the update call. See Ext.UpdateManager.update() for more details. (defaults to null)
     * @return {Ext.UpdateManager} 
     */
    setUrl: function (url, params, urlOptions) {
        // Create a refresher bound to these parameters
        this.p_currentRefresher = this.p_refresher.createDelegate(this, [url, params, urlOptions]);
        this.refresh();
        return this.getUpdateManager();
    },
    
    /** @private */
    p_refresher: function (url, params, urlOptions) {
        var update = Rack.copy(urlOptions);
        if (!this.p_loading) {
            this.getBodyContainer().addClass('rack-panel-body-clip');
            
            update.url = url;
            update.params = params;
            update.callback = update.callback ? 
                    Rack.sequence([this.p_contentReady, this], [update.callback, update.scope]) :
                    Rack.scope([this.p_contentReady, this]);
            
            this.p_loading = true;
            this.getUpdateManager().update(update);
        }
    },
    
    /**
     * Force a content refresh from the url specified in the setUrl() method.
     * Will fail silently if the setUrl method has not been called.
     * @return {Object} 
     */
    refresh: function () {
        if (this.p_currentRefresher) {
            this.p_loaded = false;
            this.p_currentRefresher();
        }
        return this;
    },

    // Callback for update completion
    /** @private */
    p_contentReady: function (el, s, r) {
        if (!s) {
            this.setContent('Failed to load content from server.');
        }
        
        this.syncContentHeight();
        
        this.getBodyContainer().removeClass('rack-panel-body-clip');
        
        this.p_loading = false;
        this.p_loaded = true;
    }
};