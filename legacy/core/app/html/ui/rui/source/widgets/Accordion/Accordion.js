/**
* Creates an Accordion component.
* @namespace Rack.widget
* @class Accordion
* @extends Ext.util.Observable
* @constructor
* @param {Object} config Configuration object to set any properties for this Accordion. 
*/
Rack.widget.Accordion = function (config) {
    this.parent = config.parent;
    this.container = this.el = Ext.DomHelper.append(this.parent, {}, true);
    this.panels = new Rack.data.DLLI();  // Indexed doubly linked list
    this.stateIdMap = {}; // Maps state ids to normal ids.
    this.active = null;
    this.config = config;
    
    // Create the essence that will create the panels
    this.panelEssence = (new Rack.widget.AccordionPanelEssence()).
        using(Rack.behavior.ShowHide).
        using(Rack.behavior.ExpandCollapse).
        using(Rack.behavior.AccordionPanelRemote);
    
    this.addEvents({
        /**
         * @event beforepanelactivate
         * Fires before a panel is activated, set cancel to true on the "e" parameter to cancel the activation
         * @param {Rack.widget.Accordion} this
         * @param {Object} e Set cancel to true on this object to cancel the activation
         * @param {Rack.widget.AccordionPanel} panel The panel being activated
         */
        'beforepanelactivate': true,
        /**
         * @event panelactivate
         * Fires when the active tab changes
         * @param {Rack.widget.Accordion} this
         * @param {Rack.widget.AccordionPanel} panel The activated panel
         */
        'panelactivate': true,
        /**
         * @event panelexpandn
         * Fires when a panel is expandn
         * @param {Rack.widget.Accordion} this
         * @param {Rack.widget.AccordionPanel} panel The expandn panel
         */
        'panelexpanded': true,
        /**
         * @event panelhidden
         * Fires when a panel is hidden
         * @param {Rack.widget.Accordion} this
         * @param {Rack.widget.AccordionPanel} panel The hidden panel
         */
        'panelcollapsed': true,
        /**
         * @event panelorder
         * Fires when the order of the panels changes
         * @param {Rack.widget.Accordion} this
         * @param {Array} panelIDList Ordered list of panel IDs
         */
        'panelorder': true
    });
};


Ext.extend(Rack.widget.Accordion, Ext.util.Observable, {
    getParent: function () {
        return this.el;
    },
    
    getContainer: function () {
        return this.el;
    },
    
    getBody: function () {
        return this.el;
    },
    
    setHeight: function (x) {
        // setHeight = no good with multi
        if (this.config.multi) {
            return this;
        }
        
        this.el.setHeight(x);
        
        // Take off the height of the panel headers
        var p = this.panels.firstNode;
        while (p) {
            x -= p.data.getHeader().getHeight();
            p = p.next;
        }
        
        // Set the result on all of the panels
        p = this.panels.firstNode;
        while (p) {
            p.data.setHeight(x);
            p = p.next;
        }
        
        return this;
    },
    
    setWidth: function (x) {
        // Set the result on all of the panels
        p = this.panels.firstNode;
        while (p) {
            p.data.setWidth(x);
            p = p.next;
        }
    
        this.el.setWidth(x);
        return this;
    },
    
    /**
     * Creates a new AccordionPanel.
     * @param {Object} config Config object to set any properties for the AccordionPanel. 
     * @return {Rack.widget.AccordionPanel} The created AccordionPanel.
     */
    addPanel: function (config) {
        // Create the panel.
        var p = this.panelEssence.
            clearParams(). // restores all default params
            setCollapsed(true). // collapsed by default
            setBodyHeight(this.config.panelHeight).
            setAccordion(this).
            setParent(this.getBody()).
            applyParams(config).
            create();
        
        // Store the panel
        this.panels.insertEnd(new Rack.data.DLLINode(p.getId(), p));
        
        this.p_setPanelStateId(p.getId(), p.getStateId());
        
        return p;
    },

    // Maps the state id to the unique id
    p_setPanelStateId: function (id, stateId) {
        if (id && stateId) {
            this.stateIdMap[stateId] = id;
        }
        
        return this;
    },

    // Returns the unique id of a panel.
    p_getPanelId: function (id) {
        return this.stateIdMap[id] || id;
    },
    
    /**
     * Returns the AccordionPanel with the specified id
     * @param {String} id The id of the AccordionPanel to fetch.
     * @return {Rack.widget.AccordionPanel}
     */
    getPanel: function (id) {
        return this.p_getPanelNode(id).data;
    },
    
    p_getPanelNode: function (id) {
        return this.panels.item(this.p_getPanelId(id));
    },
    
    /**
     * Remove an AccordionPanel.
     * @param {String} id The id of the AccordionPanel to remove.
     * @return {Rack.widget.Accordion}
     */
    removePanel: function (id) {
        var p = this.panels.remove(this.p_getPanelId(id));
        if (p) {
            if (p.data === this.active && !this.config.multi) {
                // Activate the first panel if the removed panel was the active one
                this.activate(this.panels.firstNode.id);
            }
            // Remove the DOM element
            p.data.getContainer().remove();
        }
        return this;
    },
    
    /**
     * expands the AccordionPanel with the specified id
     * @param {String} id The id of the AccordionPanel to expand.
     * @param {Boolean} now (optional) True to expand the AccordionPanel without animation.
     * @return {Rack.widget.Accordion}
     */
    expandPanel: function (id, now) {
        var p = this.getPanel(id);
        if (p) {
            this.expandPanelObject(p, now);
        }
        return this;
    },
    
    // expands a panel object
    /** @private */
    expandPanelObject: function (p, now) {
        p.expand(null, now);
        this.fireEvent('panelexpanded', this, p);
        return this;
    },
        
    /**
     * collapses the AccordionPanel with the specified id
     * @param {String} id The id of the AccordionPanel to collapse.
     * @param {Boolean} now (optional) True to collapse the AccordionPanel without animation.
     * @return {Rack.widget.Accordion}
     */
    collapsePanel: function (id, now) {
        var p = this.getPanel(id);
        if (p) {
            this.collapsePanelObject(p, now);
        }
        return this;
    },
    
    // collapses a panel object
    /** @private */
    collapsePanelObject: function (p, now) {
        p.collapse(null, now);
        this.fireEvent('panelcollapsed', this, p);
        return this;
    },
    
    /**
     * Activate an AccordionPanel.
     * @param {String} id The id of the AccordionPanel to activate.
     * @return {Rack.widget.Accordion} 
     */
    activate: function (id, now) {
        var panel = this.getPanel(id);
        var e = {};
        var multi = this.config.multi;
        if (panel === this.active && !multi) {
            return panel;
        } 
        
        // The beforepanelactivate event gives the user a chance to
        // cancel the activation by setting e.cancel = true
        this.fireEvent("beforepanelactivate", this, e, this.active);
        if (e.cancel !== true) {
            if (this.active && !multi) {
                this.collapsePanelObject(this.active, now);
            }
            this.active = panel;
            if (multi && !this.active.isCollapsed()) {
                this.collapsePanelObject(this.active, now);
            } else {
                this.expandPanelObject(this.active, now);
            }
            this.fireEvent('panelactivate', this, this.active);
        }
        return panel;
    },
    
    /**
     * Moves an AccordionPanel up one in the panel order.
     * @param {String} id The id of the AccordionPanel to move.
     * @return {Rack.widget.Accordion} 
     */
    moveUp: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = p1 && p1.prev ? p1.prev : null;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    /**
     * Moves an AccordionPanel down one in the panel order.
     * @param {String} id The id of the AccordionPanel to move.
     * @return {Rack.widget.Accordion} 
     */
    moveDown: function (id) {
        var p2 = this.p_getPanelNode(id),
            p1 = p2 && p2.next ? p2.next : null;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    /**
     * Moves an AccordionPanel to the top in the panel order.
     * @param {String} id The id of the AccordionPanel to move.
     * @return {Rack.widget.Accordion} 
     */
    insertPanelBeginning: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = this.panels.firstNode;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },

    /**
     * Moves an AccordionPanel before another AccordionPanel.
     * @param {String} id1 The id of the AccordionPanel to be moved.
     * @param {String} id2 The id of the AccordionPanel used as a position reference.
     * @return {Rack.widget.Accordion} 
     */
    insertPanelBefore: function (id1, id2) {
        var p1 = this.p_getPanelNode(id1),
            p2 = this.p_getPanelNode(id2);
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    // Moves panel p1 before panel p2
    /** @private */
    p_insertPanelBefore: function (p1, p2) {
        if (p1 && p2 && p1 !== p2) {
            // Move the DOM objects
            p1.data.getContainer().insertBefore(p2.data.getContainer());
            // Remove then reinsert
            this.panels.insertBefore(this.panels.remove(p1), p2);
            // Get the order list
            var list = this.panels.listOrdered().map(function (e, a, i) {
                return e.data.getId();
            });
            this.fireEvent('panelorder', this, list);
        }
        return this;
    },

    /**
     * Moves an AccordionPanel to the bottom in the panel order.
     * @param {String} id The id of the AccordionPanel to move.
     * @return {Rack.widget.Accordion} 
     */
    insertPanelEnd: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = this.panels.lastNode;
        if (p1 && p2 && p1 !== p2) {
            // Move the DOM objects
            p1.data.getContainer().insertAfter(p2.data.getContainer());
            // Remove then reinsert
            this.panels.insertEnd(this.panels.remove(p1));
            // Get the order list
            var list = this.panels.listOrdered().map(function (e, a, i) {
                return e.data.getId();
            });
            this.fireEvent('panelorder', this, list);
        }
        return this;
    },
    
    /**
     * Restores this Accordion's state using Ext.state.Manager or the state provided by the passed provider.
     * @param {String} groupid The group of Accordions that this Accordion shares its state with.
     * @param {Ext.State.Manager} provider (optional) An alternate state provider.
     * @return {Rack.widget.Accordion} 
     */
    restoreState: function (provider) {
        var sm = new Rack.widget.AccordionStateManager(),
            stateId = this.config.stateId || this.getContainer().id;
        if (!provider) {
            provider = Ext.state.Manager;
        }
        sm.init(this, stateId, provider);
        return this;
    },
    
    restorePanelState: function (pid, state) {
        var p = this.getPanel(pid);
        // We don't want to mess with panel expand/collapse
        // if the panel had one of these set.
        if (!p.getAlwaysExpanded() && !p.getAlwaysCollapsed()) {
            if (this.config.multi) {
                if (state.exp) {
                    this.expandPanel(pid, true);
                } else {
                    this.collapsePanel(pid, true);
                }
            } else {
                if (state.exp) {
                    p.activate();
                }
            }
        }
    },
    
    /**
     * Destroys this Accordion
     * @param {Boolean} removeEl (optional) True to remove the element from the DOM as well
     */
    destroy: function (removeEl) {
        this.purgeListeners();
        YAHOO.util.Event.purgeElement(this.container.dom, true);
        
        var p;
        while (p = this.panels.remove(this.panels.firstNode)) {
            if (p.data && p.data.destroy) {
                p.data.destroy(true);
                p.data = null;
            }
        }

        this.container.remove();
    }

});
