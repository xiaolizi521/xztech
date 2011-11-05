// Accordion Panel Item Status Proxy
Rack.widget.AccordionPanelStatusProxy = function (config) {
    Ext.apply(this, config);
    this.id = this.id || Ext.id();
    this.el = new Ext.Layer({
        dh: {
            id: this.id, 
            tag: "div", 
            cls: "x-dd-drag-proxy " + this.dropNotAllowed, 
            children: [
                {tag: "div", cls: "x-dd-drop-icon"},
                {
                    tag: 'div', 
                    cls: 'x-dd-drag-ghost rack-accordion-panel-ghost', 
                    children: [
                        {tag: "div", cls: "rack-accordion-panel-ghost-inner"}
                    ]
                }
            ]
        }, 
        shadow: !config || config.shadow !== false
    });
    this.ghost = Ext.get(this.el.dom.childNodes[1].childNodes[0]);
    this.dropStatus = this.dropNotAllowed;
};

Ext.extend(Rack.widget.AccordionPanelStatusProxy, Ext.dd.StatusProxy);
