/**
*
*/
Rack.widget.AccordionPanelEssence = function (o) {
    this.setTarget(Rack.widget.AccordionPanel);

    this.addParams({
        /** 
        * @property accordion
        * @description 
        * @private 
        * @type Rack.widget.Accordion
        */
        accordion: null,
        
        /** 
        * @property stateId
        * @description Optional. The id used when state is stored between a group of Accordions.
        * @default null
        * @private 
        * @type String
        */
        stateId: null,

        /** 
        * @property draggable
        * @description Optional. true if this Panel is movable.
        * @default true
        * @private 
        * @type Boolean
        */
        moveable: true
        });

    Rack.widget.AccordionPanelEssence.superclass.constructor.call(this, o);
};

Ext.extend(Rack.widget.AccordionPanelEssence, Rack.widget.PanelEssence, {
    validate: function () {
        var p = this.params;

        Rack.widget.AccordionPanelEssence.superclass.validate.call(this);
        
        if (!p.accordion) {
            throw new TypeError('AccordionPanelEssence is not valid.  The accordion parameter is required and must be a Rack.widget.Accordion object.');
        }
        
        return true;
    }
});