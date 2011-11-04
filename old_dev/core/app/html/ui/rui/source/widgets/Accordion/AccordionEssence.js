/**
*
*/
Rack.widget.AccordionEssence = function (o) {
    this.setTarget(Rack.widget.Accordion);
    
    this.addParams({
        /** 
        * @property parent
        * @description The id, DOM element or Ext.Element container where this Accordion is to be rendered. 
        * @private 
        * @type String/HTMLElement/Ext.Element
        */
        parent: null,
        
        /** 
        * @property multi
        * @description Optional. 
        * @default false
        * @private 
        * @type Boolean
        */
        multi: false,
        
        /** 
        * @property stateId
        * @description Optional. The id used when state is shared between a group of Accordions.
        * @default null
        * @private 
        * @type String
        */
        stateId: null,
        
        /** 
        * @property panelHeight
        * @description Optional. The height of the panels.
        * @default null
        * @private 
        * @type Number
        */
        panelHeight: null,
        
        /** 
        * @property enableDD
        * @description Optional. True to enable drag and drop operations.
        * @default false
        * @private 
        * @type Boolean
        */
        enableDD: false
    });
    
    Rack.widget.AccordionEssence.superclass.constructor.call(this, o);
};

Ext.extend(Rack.widget.AccordionEssence, Rack.util.Essence, {
    validate: function () {
        var p = this.params;
        
        if (!p.parent) {
            throw new TypeError('AccordionEssence is not valid.  The parent parameter is required and must be an id, DOM element or Ext.Element object.');
        }
        
        p.parent = Ext.get(p.parent, true);
        if (!p.parent) {
            throw new TypeError('AccordionEssence is not valid.  The parent parameter is not a valid id, DOM element or Ext.Element object.');
        }
        
        return true;
    }
});
