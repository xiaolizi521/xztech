Rack.DumbComboBox = function (config) {
    config = config || {};
    Rack.DumbComboBox.superclass.constructor.call(this, config);
    this.addEvents({
        'expand': true,
        'collapse': true,
        'beforeselect': true,
        'select': true,
        /**
         * @event beforequery
         * Fires before all queries are processed. Return false to cancel the query or set cancel to true.
         * The event object passed has these properties:
         * <ul style="padding:5px;padding-left:16px;">
	     * <li>combo - This combo box</li>
	     * <li>query - The query</li>
	     * <li>forceAll - true to force "all" query</li>
	     * <li>cancel - set to true to cancel the query.</li>
	     * </ul>
	     * @param {Object} e The query event object
	     */
        'beforequery': true
    });
    this.selectedIndex = -1;
    this.mode = 'local';
    if (config.queryDelay === undefined) {
        this.queryDelay = 10;
    }
    if (config.minChars === undefined) {
        this.minChars = 0;
    }
};

Ext.extend(Rack.DumbComboBox, Ext.form.ComboBox, {
    onRender: function (ct) {
        Rack.DumbComboBox.superclass.onRender.call(this, ct);
        if (this.hiddenName) {
            this.hiddenField = this.el.insertSibling({tag: 'input', type: 'hidden',  name: this.hiddenName, id: this.hiddenName}, 
                'before', true);
            this.hiddenField.value =
                this.hiddenValue !== undefined ? this.hiddenValue :
                this.value !== undefined ? this.value : '';

            this.el.dom.name = '';
        }
        
        if (Ext.isGecko) {
            this.el.dom.setAttribute('autocomplete', 'off');
        }
        
        var cls = 'x-combo-list';
        
        this.list = new Ext.Layer({shadow: this.shadow, cls: [cls, this.listClass].join(' '), constrain: false});
        
        this.list.setWidth(this.listWidth || this.wrap.getWidth());
        this.list.swallowEvent('mousewheel');
        this.assetHeight = 0;
        
        if (this.title) {
            this.header = this.list.createChild({cls: cls + '-hd', html: this.title});
            this.assetHeight += this.header.getHeight();
        }
        
        this.innerList = this.list.createChild({cls: cls + '-inner'});
        this.innerList.on('mouseover', this.onViewOver, this);
        this.innerList.on('mousemove', this.onViewMove, this);
        
        if (this.pageSize) {
            this.footer = this.list.createChild({cls: cls + '-ft'});
            this.pageTb = new Ext.PagingToolbar(this.footer, this.store, {pageSize: this.pageSize});
            this.assetHeight += this.footer.getHeight();
        }

        if (!this.tpl) {
            this.tpl = '<div class="' + cls + '-item">{' + this.displayField + '}</div>';
        }
        
        this.emptyStore = new Ext.data.SimpleStore({
            'id': 0,
            fields: [this.valueField, this.displayField],
            data: []
        });
        
        if (this.store) {
            this.storeLoaded = true;
        } else {
            this.store = this.emptyStore;
        }
        
        this.view = new Ext.View(this.innerList, this.tpl, {
            singleSelect: true, 
            store: this.store, 
            selectedClass: this.selectedClass
        });
        
        this.view.on('click', this.onViewClick, this);
        
        if (this.resizable) {
            this.resizer = new Ext.Resizable(this.list,  {
                pinned: true, 
                handles: 'se'
            });
            this.resizer.on('resize', function (r, w, h) {
                this.maxHeight = h - this.handleHeight - this.list.getFrameWidth('tb') - this.assetHeight;
                this.listWidth = w;
                this.restrictHeight();
            }, this);
            this[this.pageSize ? 'footer' : 'innerList'].setStyle('margin-bottom', this.handleHeight + 'px');
        }
        
        if (!this.editable) {
            this.editable = true;
            this.setEditable(false);
        }
    },

    populateView: function (store) {
        this.selectedIndex = -1;
        this.store = store;
        this.view.setStore(store);
        this.storeLoaded = true;
        return this;
    },
    
    clearView: function () {
        this.storeLoaded = false;
        this.clearValue();
        this.selectedIndex = -1;
        this.view.clearSelections();
        this.view.setStore(this.emptyStore);
        return this;
    },

    doQuery: function (q, forceAll) {
        var qe = {
            query: q || '',
            forceAll: forceAll,
            combo: this,
            cancel: false
        };
        if (this.fireEvent('beforequery', qe) === false || qe.cancel) {
            return this;
        }
        if (qe.forceAll || (qe.query.length >= this.minChars)) {
            if (this.lastQuery !== qe.query) {
                this.lastQuery = qe.query;
                this.selectedIndex = -1;
                if (this.storeLoaded) {
                    if (qe.forceAll) {
                        this.store.clearFilter();
                    } else {
                        this.store.filter(this.displayField, qe.query);
                    }
                }
            }
            if (this.storeLoaded) {
                this.onLoad();
            }
        }
    },
    
    onTriggerClick: function () {
        if (this.disabled || !this.storeLoaded) {
            return;
        }
        if (this.isExpanded()) {
            this.collapse();
            this.el.focus();
        } else {
            this.hasFocus = true;
            this.doQuery(this.triggerAction === 'all' ? this.doQuery(this.allQuery, true) : this.doQuery(this.getRawValue()));
            this.el.focus();
        }
    }
});

Rack.DumbComboBox.superclass = Ext.form.ComboBox.superclass;
