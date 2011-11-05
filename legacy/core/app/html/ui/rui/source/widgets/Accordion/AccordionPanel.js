/**
 * @class Rack.widget.AccordionPanel
 * @extends Rack.widget.Panel
 */
Rack.widget.AccordionPanel = function (config) {
    this.addEvents({
         /**
         * @event activate
         * Fires when this panel becomes the active panel
         * @param {Rack.widget.AccordionPanel} this
         */
        'activate': true,
        
        // Define this here even though it is used by the 
        // ExpandCollapse behavior because some other 
        // features that are NOT tied to the bahavior use it.
        'beforeexpand': true
    });

    this.accordion = config.accordion;
    this.stateId = config.stateId;
    
    this.collapsed = config.collapsed;
    this.loadOnce = config.loadOnce;
    this.moveable = config.moveable;
    this.alwaysOpen = config.alwaysOpen;
    this.alwaysClosed = config.alwaysClosed;
    
    this.expandDuration = config.expandDuration;
    this.expandTransition = config.expandTransition;
    this.collapseDuration = config.collapseDuration;
    this.collapseTransition = config.collapseTransition;
    this.fadeInDuration = config.fadeInDuration;
    this.fadeOutDuration = config.fadeOutDuration;
    
    this.enableDD = this.accordion.config.enableDD;
 
    // We must have a header so we specify a default one
    config.header = config.header || {text: config.title || 'Accordion Panel'};
    
    Rack.widget.AccordionPanel.superclass.constructor.call(this, config);
    
    // Make the header the toggle switch
    this.header.el.on('click', function () {
        this.activate();
    }, this);
    this.header.el.addClass('rack-accordion-panel-title');

    this.loading = false;
    this.loaded = false;
    
    // Show a reload button on the header for when 
    // we are loading remotely
    this.reloadEl = this.header.addButton('Reload', 'rack-tbicon-reload-dlg', function (e) {
        e.stopPropagation();
        this.refresh();
    }, this);
    
    if (config.loadOnce) {
        this.showReloadButton();
    } else {
        this.hideReloadButton();
    }
    
    if (this.enableDD && this.moveable) {
        // The source is the container
        this.elDS = new Rack.widget.AccordionPanelDragSource(this.getContainer(), {
            ddGroup: this.accordion.el.id, 
            dragData: {panel: this},
            scroll: false
        });
        // The drag handler is the header
        this.elDS.setHandleElId(this.header.id);
        
        // The target is the container
        this.elDT = new Rack.widget.AccordionPanelDropTarget(this, {
            ddGroup: this.accordion.el.id
        });
    }
};

Ext.extend(Rack.widget.AccordionPanel, Rack.widget.Panel, {
    getStateId: function () {
        return this.stateId;
    },
    
    remove: function () {
        // Let the accordion remove the panel
        this.accordion.removePanel(this.id);
    },
    
    /** @private */
    showReloadButton: function () {
        this.reloadEl.removeClass('rack-display-hide');
    },
    
    /** @private */
    hideReloadButton: function () {
        this.reloadEl.addClass('rack-display-hide');
    },

    /**
     * Activate this AccordionPanel.
     * @return {Rack.widget.AccordionPanel} 
     */
    activate: function (now) {
        this.accordion.activate(this.id, now);
        this.fireEvent('activate', this);
        return this;
    },
    
    // Insert this panel before a panel
    /** @private */
    insertBefore: function (id) {
        // 1. Hide the panel
        // 2. Let the Accordion move the panel
        // 3. Show the panel
        this.hide(
            this.accordion.insertPanelBefore.
                createDelegate(this.accordion, [this.getId(), id]).
                createSequence(this.show.createDelegate(this, [null]), this));
    },
    
    createGrid: function (grid) {
        if (Function.si(grid)) {
            this.on('beforeexpand', function () {
                if (!this.grid) {
                    this.grid = this.createGrid(grid());
                    this.body.appendChild(this.grid.container);
                    this.body.removeClass('rack-panel-body-widget');
                }
            }, this);
            
            return null;
        }
        
        return grid;
    }
});
