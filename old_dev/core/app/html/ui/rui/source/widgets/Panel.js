/**
* @module Panel
* @description 
* @title Panel
* @namespace Rack.widget
* @requires 
*/


/**
* Class that contains the default property values for Panels.
* @namespace Rack.widget
* @class PanelConfiguration
* @cfg {Object} header A Rack.PanelHeader configuration object (defaults to null)
* @cfg {[Object]} toolbar Array of elements to add to the toolbar (defaults to null)
* @cfg {String/HTMLelement/Ext.element} body The id, DOM element or Ext.element to use as the body of this Panel (defaults to null)
* @cfg {[Object]} footer Array of elements to add to the footer (defaults to null)
* @cfg {String} content The content of the body (defaults to null)
* @cfg {Number} bodyHeight The height of the body (defaults to null)
* @cfg {String} scroll The scroll setting of the body (defaults to auto)
*/
Rack.widget.PanelEssence = function (o) {
    this.setTarget(Rack.widget.Panel);
    
    this.addParams({
        parent: null,
        
        /** 
        * @property header
        * @description Object literal representing the configuration options for this Panel's header.  
        * See Rack.widget.Titlebar class for configuration options.
        * @default null
        * @private
        * @type Number
        */
        header: null,
        
        
        toolbar: null,
        
        
        body: null,
        
        
        footer: null,
        
        
        content: null,
        
        
        loadScripts: false,
        
        
        grid: null,
        
        
        bodyHeight: null
    });
    
    Rack.widget.PanelEssence.superclass.constructor.call(this, o);
};

Ext.extend(Rack.widget.PanelEssence, Rack.util.BehavioralEssence, {
    validate: function () {
        var p = this.params;
        
        if (!p.parent) {
            throw new TypeError('Invalid PanelEssence.  The parent parameter is required and must be an id, DOM element or Ext.element object.');
        }
        
        p.parent = Ext.get(p.parent, true);
        if (!p.parent) {
            throw new TypeError('Invalid PanelEssence.  The parent parameter is not a valid id, DOM element or Ext.element object.');
        }
        
        return true;
    }
});


/**
* A basic Panel that can contain a header, toolbar, body and footer elements.
* @namespace Rack.widget
* @class Panel
* @extends Ext.util.Observable
* @constructor
* @param {Object} config Configuration object to set any properties for this Panel. 
*/
Rack.widget.Panel = function (config) {
    // Parent element
    this.parent = config.parent;
    
    // Container element
    this.container = Ext.get(this.containerTemplate.append(this.parent, {}));
    
    // Header element
    this.headerContainer = this.container.child('.rack-panel-header');
    
    // Default Header widget
    this.header = null;
    if (config.header) {
        this.header = (new Rack.widget.TitlebarEssence(config.header)).
            setParent(this.headerContainer).
            create();
    }
    
    // Toolbar element
    this.toolbarContainer = this.container.child('.rack-panel-toolbar');
    
    // Default Toolbar widget
    this.toolbar = null;
    if (Array.si(config.toolbar)) {
        this.toolbar = new Ext.Toolbar(this.toolbarContainer, config.toolbar);
    }
    
    // Body element
    this.bodyContainer = this.container.child('.rack-panel-body');
    
    // Default Body widget
    this.body = this.bodyContainer.child('.rack-panel-body-widget');
    
    // Passed Body widget 
    // If present, replaces Default Body widget.
    if (config.body && !config.grid) {
        this.body = Ext.get(config.body).replace(this.body);
    }
    
    // Grid widget
    this.grid = null;
    if (config.grid) {
        this.grid = this.createGrid(config.grid);
        if (this.grid) {
            this.body.appendChild(this.grid.container);
            this.body.removeClass('rack-panel-body-widget');
        }
    }
    
    // Body Height
    this.bodyHeight = null;
    if (this.bodyHeight) {
        this.setHeight(config.bodyHeight);
    }
    
    // Body Width
    this.bodyWidth = null;
    if (this.bodyWidth) {
        this.setWidth(config.bodyWidth);
    }
    
    // Body Content
    this.content = null;
    this.loadScripts = null;
    if (this.content) {
        this.setContent(config.content, config.loadScripts);
    }
    
    // Footer element
    this.footerContainer = this.container.child('.rack-panel-footer');
    
    // Default Footer widget
    this.footer = null;
    if (Array.si(config.footer)) {
        this.footer = new Ext.Toolbar(this.footerContainer, config.footer);
    } 

    // Widget Id - Use the Body's Id or a unique Id if a Body is not present.
    this.id = (this.body) ? this.body.id : Ext.id();
};

Ext.extend(Rack.widget.Panel, Ext.util.Observable, {
    getId: function () {
        return this.id;
    },
    
    getParent: function () {
        return this.parent;
    },
    
    getContainer: function () {
        return this.container;
    },
    
    /**
    * Get the Header element for this Panel.
    * @return {Ext.Element}
    */
    getHeaderContainer: function () {
        return this.headerContainer;
    },
    
    /**
    * Get the Header widget for this Panel.
    * @return {Rack.widget.Titlebar}
    */
    getHeader: function () {
        return this.header;
    },
    
    /**
    * Get the Toolbar element for this Panel.
    * @return {Ext.Element}
    */
    getToolbarContainer: function () {
        return this.toolbarContainer;
    },
    
    /**
    * Get the toolbar for this Panel.
    * @return {Ext.Toolbar}
    */
    getToolbar: function () {
        return this.toolbar;
    },
    
    /**
    * Get the Body element for this Panel.
    * @return {Ext.Element}
    */
    getBodyContainer: function () {
        return this.bodyContainer;
    },
    
    /**
    * Get the Body widget for this Panel.
    * @return {Ext.Element}
    */
    getBody: function () {
        return this.body;
    },
    
    /**
    * Get the Footer element for this Panel.
    * @return {Ext.Element}
    */
    getFooterContainer: function () {
        return this.footerContainer;
    },
    
    /**
    * Get the Footer widget for this Panel.  
    * @return {Ext.Toolbar}
    */
    getFooter: function () {
        return this.footer;
    },
    
    /**
    * Set the content of the body.
    * @param {String} content The content
    * @param {Boolean} loadScripts true to look for and load scripts
    * @return {Rack.widget.Panel} 
    */
    setContent: function (content, loadScripts) {
        this.content = content;
        this.loadScripts = loadScripts;
        this.body.update(content, loadScripts);
        
        this.syncContentHeight();
        
        return this;
    },
    
    /**
    * Resize the Body's height to the height of its content.
    * @return {Rack.widget.Panel}
    */
    syncContentHeight: function () {
        this.bodyContainer.setHeight(this.bodyHeight || this.body.getHeight());
        return this;
    },
    
    /**
    * Get the height of the Body.
    * @return {Number}
    */
    getHeight: function () {
        return this.bodyHeight;
    },
    
    /**
    * Set the height of the Body.
    * @return {Rack.widget.Panel}
    */
    setHeight: function (x) {
        if (x < 0) {
            this.bodyHeight = null;
            this.bodyContainer.
                setStyle('height', '').
                removeClass('rack-panel-body-setheight');
        } else {
            this.bodyHeight = x;
            this.bodyContainer.
                setHeight(x).
                addClass('rack-panel-body-setheight');
        }
        return this;
    },
    
    /**
    * Get the width of the Body.
    * @return {Number}
    */
    getWidth: function () {
        return this.bodyWidth;
    },
    
    /**
    * Set the width of the Body.
    * @return {Rack.widget.Panel}
    */
    setWidth: function (x) {
        this.bodyWidth = x;
        this.bodyContainer.setWidth(x);
        return this;
    },
    
    createGrid: function (grid) {
        return (Function.si(grid)) ? grid() : grid;
    },
    
    getGrid: function () {
        return this.grid;
    },
    
    destroy: function (remove) {
        // Destroy elements from the top down.

        if (this.header && this.header.destroy) {
            this.header.destroy(true);
        }
        this.header = null;
        
        this.headerContainer.removeAllListeners().remove();
        this.headerContainer = null;
        
        if (this.toolbar && this.toolbar.destroy) {
            this.toolbar.destroy(true);
        }
        this.toolbar = null;
        
        this.toolbarContainer.removeAllListeners().remove();
        this.toolbarContainer = null;

        if (this.footer && this.footer.destroy) {
            this.footer.destroy(true);
        }
        this.footer = null;
        
        this.footerContainer.removeAllListeners().remove();
        this.footerContainer = null;
        
        if (this.grid && this.grid.destroy) {
            this.grid.destroy(true);
        }
        this.grid = null;
        
        this.body.removeAllListeners().remove();
        this.body = null;
        
        this.bodyContainer.removeAllListeners().remove();
        this.bodyContainer = null;
        
        this.container.removeAllListeners().remove();
        this.container = null;
        
        this.purgeListeners();
    }
});

Rack.widget.Panel.prototype.containerTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({
        tag: 'div', 
        cls: 'rack-panel',
        children: [
            {tag: 'div', cls: 'rack-panel-header'},
            {tag: 'div', cls: 'rack-panel-toolbar'},
            {
                tag: 'div', 
                cls: 'rack-panel-body',
                children: [
                    {tag: 'div', cls: 'rack-panel-body-widget'}
                ]
            },
            {tag: 'div', cls: 'rack-panel-footer'}
        ]
    });
    tpl.compile();
    return tpl;
}();