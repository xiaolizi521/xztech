/**
*
*/
Rack.widget.TitlebarEssence = function (o) {
    this.setTarget(Rack.widget.Titlebar);

    this.addParams({
        /** 
        * @property parent
        * @description The id, DOM element or Ext.Element container where this Titlebar is to be rendered. 
        * @private 
        * @type String/HTMLElement/Ext.Element
        */
        parent: null,
        
        /** 
        * @property id
        * @description Optional. The id to name this Titlebar.
        * @default generated id
        * @private 
        * @type String
        */
        id: Ext.id(),
        
        /** 
        * @property text
        * @description Optional. The text to use for this Titlebar.
        * @default ''
        * @private 
        * @type String
        */
        text: '&#160;',
        
        /** 
        * @property buttons
        * @description Optional. Array of button definitions.
        * @default []
        * @private 
        * @type [Object]
        */
        buttons: [],
        
        /** 
        * @property style
        * @description Optional. Style of titlebar.
        * @default dlg
        * @private 
        * @type String
        */
        style: 'dlg'
    });
    
    Rack.widget.TitlebarEssence.superclass.constructor.call(this, o);
};

Ext.extend(Rack.widget.TitlebarEssence, Rack.util.Essence, {
    validate: function () {
        var p = this.params;
        
        if (!p.parent) {
            throw new TypeError('Invalid TitlebarEssence.  The parent parameter is required and must be an id, DOM element or Ext.Element object.');
        }
        
        p.parent = Ext.get(p.parent, true);
        if (!p.parent) {
            throw new TypeError('Invalid TitlebarEssence.  The parent parameter is not a valid id, DOM element or Ext.Element object.');
        }
        
        return true;
    }
});


/**
* Creates a Titlebar component.
* @namespace Rack.widget
* @class Titlebar
* @constructor
* @param {Object} config Configuration object to set any properties for this Titlebar. 
*/
Rack.widget.Titlebar = function (config) {
    this.parent = config.parent;
    this.id = config.id;
    this.style = config.style;
    this.el = Ext.get(this.baseTemplate.append(this.parent, {id: this.id, style: this.style}));
    this.text = this.el.child('.rack-titlebar-text');
    this.tools = this.el.child('.rack-titlebar-tools');
    
    this.setText(config.text);

    this.addButtons(config.buttons);
};

Rack.widget.Titlebar.prototype = {
    setText: function (text) {
        this.title = text;
        this.text.update(text);
        this.text.dom.title = text;
        return this;
    },
    
    addButton: function (title, cls, fn, scope) {
        var el = Ext.get(this.buttonTemplate.append(this.tools, {title: title, cls: cls}));
        el.addClassOnOver('rack-titlebar-icon-on-' + this.style);
        
        if (fn) {
            el.on('click', fn, scope);
        }
        
        return el;
    },
    
    addButtons: function (buttons) {
        buttons.forEach(function (e, i, a) {
            this.addButton(e.title, e.cls, e.click, e.scope);
        }.createDelegate(this));
    },
    
    getHeight: function () {
        return this.el.getHeight();
    },
    
    destroy: function (removeEl) {
        YAHOO.util.Event.purgeElement(this.el.dom, true);
        this.el.remove();
    }
};

Rack.widget.Titlebar.prototype.baseTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({
        tag: 'div', 
        id: '{id}', 
        cls: 'rack-titlebar rack-titlebar-{style}', 
        children: [
            {tag: 'span', cls: 'rack-titlebar-text rack-titlebar-text-{style}'},
            {tag: 'div', cls: 'rack-titlebar-tools'}
        ]
    });
    tpl.compile();
    return tpl;
}();

Rack.widget.Titlebar.prototype.buttonTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({
        tag: 'div', 
        cls: 'rack-titlebar-icon', 
        title: '{title}', 
        children: [
            {tag: 'div', cls: 'rack-titlebar-icon-inner {cls}', title: '{title}', html: '&#160;'}
        ]
    });
    tpl.compile();
    return tpl;
}();