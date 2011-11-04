// Accordion Widget State Manager
Rack.widget.AccordionStateManager = function () {
     // empty state
    this.state = {
        order: [],
        panels: {}
    };
};

Rack.widget.AccordionStateManager.prototype = {
    init: function (accordion, stateId, provider) {
        this.provider = provider;
        this.id = stateId + '-accordion-state';
        var state = provider.get(this.id);
        if (state) {
            var pid;
            // Order the panels
            if (Array.si(state.order)) {
                Ext.each(state.order, function (e, a, i) {
                    accordion.insertPanelEnd(e);
                });
            } else {
                state.order = [];
            }
            // expand/collapse the panels
            if (state.panels) {
                for (pid in state.panels) {
                    accordion.restorePanelState(pid, state.panels[pid])
                }
            } else {
                state.panels = {};
            }
            this.state = state; 
        }
        this.accordion = accordion;
        accordion.on('panelexpanded', this.onPanelExpanded, this);
        accordion.on('panelcollapsed', this.onPanelCollapsed, this);
        accordion.on('panelorder', this.onPanelOrder, this);
    },
    
    storeState: function () {
        this.provider.set(this.id, this.state);
    },
    
    onPanelExpanded: function (accordion, panel) {
        var pid = panel.getStateId();
        if (!this.state.panels[pid]) {
            this.state.panels[pid] = {};
        }
        this.state.panels[pid].exp = true;
        this.storeState();
    },
    
    onPanelCollapsed: function (accordion, panel) {
        var pid = panel.getStateId();
        if (!this.state.panels[pid]) {
            this.state.panels[pid] = {};
        }
        this.state.panels[pid].exp = false;
        this.storeState();
    },
    
    onPanelOrder: function (accordion, list) {
        this.state.order = list.map(function (e, a, i) {
            return accordion.getPanel(e).getStateId();
        });
        this.storeState();
    }
};