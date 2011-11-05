Rack.behavior.ShowHide = function (config) {
    this.p_showing = false;
    this.p_showCallback = null;
    this.p_hiding = false;
    this.p_hideCallback = null;
    this.hidden = false;
    
    this.showDuration = config.showDuration;
    this.hideDuration = config.hideDuration;
    
    this.addEvents({
        /**
        * @event beforeshow
        * Fires before this panel is shownn
        * @param {Object} this
        * @param {Object} e Set cancel to true on this object to cancel the activation
        */
        'beforeshow': true,
        /**
        * @event expand
        * Fires after this panel is shownn
        * @param {Object} this
        */
        'show': true,
        /**
        * @event beforehide
        * Fires before this panel is hidden
        * @param {Object} this
        * @param {Object} e Set cancel to true on this object to cancel the activation
        */
        'beforehide': true,
        /**
        * @event hide
        * Fires after this panel is hidden
        * @param {Object} this
        */
        'hide': true
    });
    
    if (config.hidden) {
        this.hide(null, true);
    } else {
        this.show(null, true);
    }
};

Rack.behavior.ShowHide.prototype = {
    /**
    * Get an object containing the default values for this class' parameters.
    */
    getParamDefaults: function () {
        return {
            hidden: false,
            showDuration: 0.30,
            hideDuration: 0.20
        };
    },
    
    show: function (cb, now) {
        if (this.p_showing || !this.hidden) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforeshow', this, e);
        if (e.cancel !== true) {
            this.p_showing = true;
            this.p_showCallback = cb;
            
            if (now || !this.showDuration) {
                this.getContainer().setVisible(true);
                this.afterShow();
            } else {
                this.getContainer().fadeIn({
                    endOpacity: 1,
                    duration: this.showDuration,
                    callback: Rack.scope([this.afterShow, this])
                });
            }
        }
        
        return this;
    },
    
    afterShow: function () {
        this.p_showing = false;
        this.hidden = false;

        if (this.p_showCallback) {
            this.p_showCallback();
        }
        
        this.fireEvent('show', this);
    },
    
    hide: function (cb, now) {
        if (this.p_hiding || this.hidden) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforehide', this, e);
        if (e.cancel !== true) {
            this.p_hiding = true;
            this.p_hideCallback = cb;
            
            if (now || !this.hideDuration) {
                this.getContainer().setVisible(false);
                this.afterHide();
            } else {
                this.getContainer().fadeOut({
                    endOpacity: 0,
                    duration: this.hideDuration,
                    callback: Rack.scope([this.afterHide, this])
                });
            }
        }
        
        return this;
    },
    
    afterHide: function () {
        this.p_hiding = false;
        this.hidden = true;

        if (this.p_hideCallback) {
            this.p_hideCallback();
        }
        
        this.fireEvent('hide', this);
    },
    
    isHidden: function () {
        return this.hidden;
    }
};