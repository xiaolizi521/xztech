// Custom behavior for Accordion Panels
Rack.behavior.AccordionPanelRemote = function (config) {
    this.p_loading = false;
    this.p_loaded = false;
    
    if (config.url) {
        this.setUrl(config.url, config.params, config.loadOnce, config.urlOptions);
    }
};

Ext.extend(Rack.behavior.AccordionPanelRemote, Rack.behavior.Remote, {
    /**
    * Get an object containing the default values for this class' parameters.
    */
    getParamDefaults: function () {
        return {
            url: null,
            params: {},
            loadOnce: false,
            urlOptions: {timeout: 10}
        };
    },

    /**
    * Set a URL to be used to load the content for this AccordianPanel.
    * @param {String/Function} url The url to load the content from or a function to call to get the url
    * @param {String/Object} params (optional) The string params for the update call or an object of the params. See {@link Ext.UpdateManager#update} for more details. (defaults to null)
    * @param {Boolean} loadOnce (optional) Whether to only load the content once. If this is false it makes the Ajax call every time this AccordianPanelItem is activated. (defaults to false)
    * @param {Object} config (optional) Configuration options for the update call. See {@link Ext.UpdateManager#update} for more details. (defaults to null)
    * @return {Ext.UpdateManager} The UpdateManager
    */
    setUrl: function (url, params, loadOnce, urlOptions) {
        if (this.p_currentRefresher) {
            // Discard current refresher
            this.un('beforeexpand', this.p_currentRefresher);
        }
        
        if (loadOnce) {
            this.p_loaded = false;
            this.showReloadButton();
        } else {
            this.hideReloadButton();
        }
        
        // Create a refresher bound to these parameters
        this.p_currentRefresher = this.p_refresher.createDelegate(this, [url, params, loadOnce, urlOptions]);
        
        // Add new refresher
        this.on('beforeexpand', this.p_currentRefresher);
        
        // Load now if we're already expanded
        if (!this.isCollapsed()) {
            this.p_currentRefresher();
        }
        return this.getUpdateManager();
    },
    
    p_refresher: function (url, params, loadOnce, urlOptions) {
        if (loadOnce && this.p_loaded) {
            return;
        }
        Rack.behavior.AccordionPanelRemote.superclass.p_refresher.call(this, url, params, urlOptions);
    }
});
