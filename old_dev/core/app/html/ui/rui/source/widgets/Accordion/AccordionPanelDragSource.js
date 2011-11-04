// Accordion Panel Item Drag Source
Rack.widget.AccordionPanelDragSource = function (el, config) {
    this.proxy = new Rack.widget.AccordionPanelStatusProxy();

    Rack.widget.AccordionPanelDragSource.superclass.constructor.call(this, el, config);
};

Ext.extend(Rack.widget.AccordionPanelDragSource, Ext.dd.DragSource, {
    afterRepair: function () {
        this.dragging = false;
    }
});
