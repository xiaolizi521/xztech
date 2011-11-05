Rack.behavior.ExpandCollapse = function (config) {
    this.p_expanding = false;
    this.p_expandCallback = null;
    this.p_collapsing = false;
    this.p_collapseCallback = null;
    this.collapsed = false;
    
    this.alwaysExpanded = config.alwaysExpanded;
    this.alwaysCollapsed = config.alwaysCollapsed;
    
    this.expandDuration = config.expandDuration;
    this.expandTransition = config.expandTransition;
    this.collapseDuration = config.collapseDuration;
    this.collapseTransition = config.collapseTransition;
    
    this.hideContentOnExpandCollapse = config.hideContentOnExpandCollapse;
    
    this.addEvents({
        /**
        * @event beforeexpand
        * Fires before this panel is expanded
        * @param {Object} this
        * @param {Object} e Set cancel to true on this object to cancel the activation
        */
        'beforeexpand': true,
        /**
        * @event expand
        * Fires after this panel is expanded
        * @param {Object} this
        */
        'expand': true,
        /**
        * @event beforecollapse
        * Fires before this panel is collapsed
        * @param {Object} this
        * @param {Object} e Set cancel to true on this object to cancel the activation
        */
        'beforecollapse': true,
        /**
        * @event collapse
        * Fires after this panel is collapsed
        * @param {Object} this
        */
        'collapse': true
    });
    
    if (this.alwaysExpanded) {
        this.expand(null, true);
    } else if (this.alwaysCollapsed) {
        this.collapse(null, true);
    } else if (config.collapsed) {
        this.collapse(null, true);
    } else {
        this.expand(null, true);
    }
};

Rack.behavior.ExpandCollapse.prototype = {
    /**
    * Get an object containing the default values for this class' parameters.
    */
    getParamDefaults: function () {
        return {
            collapsed: false,
            alwaysExpanded: false,
            alwaysCollapsed: false,
            expandDuration: 0.35,
            expandTransition: 'easeOut',
            collapseDuration: 0.35,
            collapseTransition: 'easeOut',
            hideContentOnExpandCollapse: false
        };
    },
    
    getAlwaysExpanded: function () {
        return this.alwaysExpanded;
    },
    
    getAlwaysCollapsed: function () {
        return this.alwaysCollapsed;
    },
    
    expand: function (cb, now) {
        var bc = this.getBodyContainer();
        
        if (this.p_expanding || !this.collapsed) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforeexpand', this, e);
        if (e.cancel !== true) {
            this.p_expanding = true;
            this.p_expandCallback = cb;
            
            bc.removeClass('rack-hide');
            
            if (now || !this.expandDuration) {
                bc.setHeight(this.getBody().getHeight());
                this.afterExpand();
            } else {
                bc.setHeight(this.bodyHeight || this.getBody().getHeight(), {
                    duration: this.expandDuration, 
                    callback: Rack.scope([this.afterExpand, this]), 
                    method: this.expandTransition
                });
            }
        }
        return this;
    },
    
    afterExpand: function () {
        if (this.hideContentOnExpandCollapse) {
            this.getBody().removeClass('rack-hide');
        }
            
        this.getBodyContainer().removeClass('rack-panel-body-clip');
        this.syncContentHeight();
        
        this.p_expanding = false;
        this.collapsed = false;
        
        if (this.p_expandCallback) {
            this.p_expandCallback();
        }
        
        this.fireEvent('expand', this);
    },
    
    collapse: function (cb, now) {
        var bc = this.getBodyContainer();
        
        if (this.p_collapsing || this.collapsed) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforecollapse', this, e);
        if (e.cancel !== true) {
            this.p_collapsing = true;
            this.p_collapseCallback = cb;
            
            if (this.hideContentOnExpandCollapse) {
                this.getBody().addClass('rack-hide');
            }
            
            bc.addClass('rack-panel-body-clip');
            if (now || !this.collapseDuration) {
                bc.setHeight(1);
                this.afterCollapse();
            } else {
                bc.setHeight(1, {
                    duration: this.collapseDuration,
                    callback: Rack.scope([this.afterCollapse, this]),
                    method: this.collapseTransition
                }); 
            }
        }
    },
    
    afterCollapse: function () {
        this.getBodyContainer().addClass('rack-hide');
        
        this.p_collapsing = false;
        this.collapsed = true;

        if (this.p_collapseCallback) {
            this.p_collapseCallback();
        }
        
        this.fireEvent('collapse', this);
    },
    
    isCollapsed: function () {
        return this.collapsed;
    }
};