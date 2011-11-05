// Accordion Panel Item Drop Target
Rack.widget.AccordionPanelDropTarget = function (panel, config) {
    this.panel = panel;
    this.el = panel.getContainer();
    
    Ext.apply(this, config);
    
    Ext.dd.ScrollManager.register(this.el);
    Ext.dd.DropTarget.superclass.constructor.call(this, this.el.dom, this.ddGroup || this.group, {isTarget: true});
};

Ext.extend(Rack.widget.AccordionPanelDropTarget, Ext.dd.DDTarget, {
    isTarget: true,
    isNotifyTarget: true,
    dropAllowed: "x-dd-drop-ok",
    dropNotAllowed: "x-dd-drop-nodrop",
    
    notifyEnter: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.showOver();
            return this.dropAllowed;
        }
        return this.dropNotAllowed;
    },
    
    notifyOver: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            return this.dropAllowed;
        }
        return this.dropNotAllowed;
    },
    
    notifyOut: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.hideOver();
        }
    },
    
    notifyDrop: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.hideOver();
            data.panel.insertBefore(this.panel.getId());
            return true;
        }
        return false;
    },
    
    overClass: 'rack-accordion-panel-on',
    
    showOver: function () {
        this.el.addClass(this.overClass);
    },
    
    hideOver: function () {
        this.el.removeClass(this.overClass);
    }
});
