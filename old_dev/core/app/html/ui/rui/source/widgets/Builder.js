/**
 * @class Rack.widget.CriteriaBuilder
 * @extends Ext.util.Observable
 * Creates a Criteria Builder component.
 * @cfg {String/HTMLElement/Element} parent The id, DOM element or Ext.Element container where this CriteriaBuilder is to be rendered.
 * @cfg {[String]} headers Array of column header names.  If only one name is present it is stretched across all of the columns.  If no names are present then no header is used.
 * @cfg {[Object]} fields Array of field definitions.  See Rack.widget.CriteriaBuilderField for field definition options.
 * @cfg {Object} options Configuration object to set any properties for this CriteriaBuilder.
 * @cfg {String} cls Configuration option - Class to add to this CriteriaBuilder. (defaults to null)
 * @cfg {Number} width Configuration option - Width of this CriteriaBuilder. (defaults to null)
 * @cfg {Number} format Configuration option - Format to use when returning the value of this CriteriaBuilder. (defaults to Rack.widget.CriteriaBuilderResultFormat.JSON)
 * @cfg {String} name Configuration option - Name of form variable to store the value of this CriteriaBuilder. (defaults to 'CriteriaBuilder' plus a unique identifier)
 * @constructor
 * Create a new CriteriaBuilder.
 * @param {Object} config Config object to set any properties for this CriteriaBuilder. 
 */
Rack.widget.CriteriaBuilder = function (config) {
    if (!config) {
        throw new TypeError('Rack.widget.CriteriaBuilder must be passed a configuration object.');
    }

    if (!config.parent) {
        throw new TypeError('Rack.widget.CriteriaBuilder must be passed an id, DOM element or Ext.Element container where this CriteriaBuilder is to be rendered.');
    }
    
    if (!Array.si(config.fields)) {
        throw new TypeError('Rack.widget.CriteriaBuilder must be passed an array of field definitions.');
    }
    
    this.headers = config.headers;
    this.fields = config.fields;

    this.config = config.options || {};
    this.cls = this.config.cls || '';
    this.width = this.config.width;
    this.format = this.config.resultFormat || this.resultFormat.JSON;
    this.name = this.config.name || 'CriteriaBuilder' + Ext.id();
    this.results = [];
        
    this.rows = {};
    this.rowCount = 0;
    
    this.parent = Ext.get(config.parent, true);
    this.el = Ext.get(this.tableTemplate.append(this.parent, {cls: this.cls}), true);
    this.head = this.el.child('thead tr');
    this.body = this.el.child('tbody');
    
    // Add the column headers
    if (Array.si(this.headers)) {
        // If there is only one header then it will span the 
        // entire table without the two button column headers.
        var fl = this.fields.length,
            hl = this.headers.length;
        this.headers.forEach(function (e, i, a) {
            var cs = hl === 1 ? fl + 2 : hl - 1 === i && fl - hl > 0 ? fl - hl : 1;
            this.headerTemplate.append(this.head, {text: e, colspan: cs});
        }.createDelegate(this));
        // Add the two button column headers
        if (hl !== 1) {
            this.headerTemplate.append(this.head, {text: '&#160;'});
            this.headerTemplate.append(this.head, {text: '&#160;'});
        }
    }
    
    if (this.width) {
        this.el.setWidth(this.width);
        this.el.child('table').setWidth(this.width);
    }
    
    if (this.format === this.resultFormat.JSON) {
        this.form = Ext.get(this.formTemplate.append(this.el, {name: this.name}), true);
    }
};

Ext.extend(Rack.widget.CriteriaBuilder, Ext.util.Observable, {
    addRow: function () {
        var r = new Rack.widget.CriteriaBuilderRow(this, this.fields, this.config, arguments);
        r.addButton('+', function () {
            this.addRow();
        }.createDelegate(this));
        r.addButton('-', function () {
            this.removeRow(r);
        }.createDelegate(this));
        this.rows[r.id] = r;
        r.on('update', this.updateForm, this);
        this.rowCount += 1;
        this.updateForm();
        return r;
    }, 
    removeRow: function (row, all) {
        if (!all && this.rowCount <= 1) {
            return this;
        }
        Ext.fly(row.id).remove();
        delete this.rows[row.id];
        this.rowCount -= 1;
        return this;
    },
    clearRows: function () {
        var x;
        for (x in this.rows) {
            this.removeRow(this.rows[x], true);
        }
        return this;
    },
    updateForm: function () {
        var r;
        this.results = [];
        for (r in this.rows) {
            this.results.push(this.rows[r].getValue());
        }
        
        if (this.format === this.resultFormat.JSON) {
            this.form.dom.value = Ext.util.JSON.encode(this.results);
        }
    }
});

Rack.widget.CriteriaBuilder.prototype.tableTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({
        tag: 'div',
        cls: 'rack-cbuilder',
        children: [
            {
                tag: 'table', 
                cls: 'rack-cbuilder {cls}', 
                children: [
                    {
                        tag: 'thead',
                        children: [
                            {tag: 'tr'}
                        ]
                    },
                    {tag: 'tbody'}
                ]
            }
        ]
    });
    tpl.compile();
    return tpl;
}();

Rack.widget.CriteriaBuilder.prototype.headerTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({tag: 'th', cls: 'rack-cbuilder', colspan: '{colspan}', html: '{text}'});
    tpl.compile();
    return tpl;
}();

Rack.widget.CriteriaBuilder.prototype.formTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({tag: 'input', type: 'hidden', name: '{name}'});
    tpl.compile();
    return tpl;
}();

Rack.widget.CriteriaBuilderResultFormat = {
    JSON: 1
};
Rack.widget.CriteriaBuilder.prototype.resultFormat = Rack.widget.CriteriaBuilderResultFormat;


/**
 * @class Rack.AccordionPanel
 * @extends Ext.util.Observable
 * Creates a lightweight Accordion component.
 * @cfg {Boolean} multi Allow multiple items to be open at one time (defaults to false)
 * @cfg {String} stateId The id used when state is stored between a group of AccordionPanels (defaults to null)
 * @cfg {Number} panelHeight The height of the panels
 * @constructor
 * Create a new AccordionPanel.
 * @param {String/HTMLElement/Element} container The id, DOM element or Ext.Element container where this AccordionPanel is to be rendered. 
 * @param {Boolean} config Config object to set any properties for this AccordionPanel. 
 */
Rack.widget.CriteriaBuilderRow = function (parent, fields, config, defaults) {
    if (!parent) {
        throw new TypeError('Rack.widget.CriteriaBuilderRow must be passed a Rack.widget.CriteriaBuilder object to add itself to.');
    }

    if (!Array.si(fields)) {
        throw new TypeError('Rack.widget.CriteriaBuilderRow must be passed an array of field definitions.');
    }
    
    this.id = Ext.id();
    this.fields = [];
    this.lastField = null;
    this.config = config || {};
    this.hideEmpty = config.hideEmpty || true;
    this.defaults = defaults || [];
    this.results = {};
    
    this.events = {
        'update': true
    };
    
    this.parent = parent;
    this.el = Ext.get(this.rowTemplate.append(this.parent.body, {id: this.id}), true);
    
    fields.forEach(function (e, i, a) {
        this.addField(e, this.defaults[i]);
    }.createDelegate(this));

    this.fields[0].load();
};

Ext.extend(Rack.widget.CriteriaBuilderRow, Ext.util.Observable, {
    addField: function (definition, def) {
        var f = new Rack.widget.CriteriaBuilderField(this, definition, def),
            lf = this.lastField;
        
        if (lf) {
            lf.on('change', f.load, f);
            lf.on('load', f.clear, f);
            lf.on('clear', f.clear, f);
        }
        
        f.on('update', this.update, this);
        
        this.lastField = f;
        this.fields.push(f);
        
        return f;
    },
    addButton: function (text, fn) {
        return new Ext.Button(Ext.DomHelper.append(this.el, {tag: 'td', cls: 'rack-cbuilder'}), {text: text, handler: fn});
    },
    getValue: function () {
        return this.value;
    },
    update: function () {
        var value = {};
        this.fields.forEach(function (e, i, a) {
            value[e.name] = e.getValue();
        });
        this.value = value;
        this.fireEvent('update');
    }
});

Rack.widget.CriteriaBuilderRow.prototype.rowTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({tag: 'tr', id: '{id}', cls: 'rack-cbuilder'});
    tpl.compile();
    return tpl;
}();


/**
 * @class Rack.AccordionPanel
 * @extends Ext.util.Observable
 * Creates a lightweight Accordion component.
 * @cfg {Boolean} multi Allow multiple items to be open at one time (defaults to false)
 * @cfg {String} stateId The id used when state is stored between a group of AccordionPanels (defaults to null)
 * @cfg {Number} panelHeight The height of the panels
 * @constructor
 * Create a new AccordionPanel.
 * @param {String/HTMLElement/Element} container The id, DOM element or Ext.Element container where this AccordionPanel is to be rendered. 
 * @param {Boolean} config Config object to set any properties for this AccordionPanel. 
 */
Rack.widget.CriteriaBuilderField = function (parent, definition, def) {
    if (!parent) {
        throw new TypeError('Rack.widget.CriteriaBuilderField must be passed a Rack.widget.CriteriaBuilderRow object to add itself to.');
    }
    
    if (!definition) {
        throw new TypeError('Rack.widget.CriteriaBuilderField must be passed a field definition.');
    }
    
    this.parent = parent;
    this.definition = definition;
    this.type = this.definition.type || 'combo';
    this.id = this.definition.id || Ext.id();
    this.name = this.definition.name || this.id;
    this.url = this.definition.url;
    this.def = def || this.definition.def;

    if (this.definition.ds && Function.si(this.definition.ds)) {
        this.ds = this.definition.ds();
    } else if (this.definition.ds) {
        this.ds = this.definition.ds;
    } else {
        this.ds = new Ext.data.Store({
            // Load using HTTP
            proxy: new Ext.data.HttpProxy({url: this.url}),
            // Reader to load the JSON information
            reader: new Ext.data.JsonReader({
                root: 'values',
                totalProperty: 'total_values',
                id: 'value'
            }, ['value', 'text'])
        });
    }
    
    this.ds.on('load', this.finishLoad, this);
    
    this.params = {};
    
    this.events = {
        'change': true,
        'load': true,
        'clear': true,
        'update': true
    };

    this.elp = Ext.get(this.fieldTemplate.append(this.parent.el, {id: this.id}), true);
    this.el = new Rack.DumbComboBox({
        displayField: this.definition.displayField || 'text',
        valueField: this.definition.valueField || 'value',
        typeAhead: true,
        emptyText: this.definition.emptyText || '',
        selectOnFocus: true,
        triggerAction: 'all',
        width: this.definition.width,
        hiddenName: this.name
    });
    this.el.applyTo(this.elp.dom.firstChild);
    this.el.on('select', this.domChange, this);
    this.elp.on('change', this.domChange, this);
};

Ext.extend(Rack.widget.CriteriaBuilderField, Ext.util.Observable, {
    load: function (params) {
        this.params = params || {};
        this.ds.load({params: this.params});
    },
    finishLoad: function () {
        this.el.populateView(this.ds);
        this.set(this.def || '');
        this.fireEvent('load');
        this.fireEvent('update');
    },
    clear: function () {
        this.el.clearView();
        this.set('');
        this.fireEvent('clear');
        this.fireEvent('update');
    },
    set: function (val) {
        this.el.setValue(val);
        this.setParam(val);
    },
    setParam: function (val) {
        this.params[this.name] = val;
        if (val) {
            this.fireEvent('change', this.params);
            this.fireEvent('update');
        }
    },
    
    // When the combobox is updated
    /** @private */
    domChange: function () {
        this.setParam(this.el.getValue());
    },
    
    /**
     * Set a URL to be used to load the content for this AccordianPanelItem.
     * @param {String/Function} url The url to load the content from or a function to call to get the url
     * @param {String/Object} params (optional) The string params for the update call or an object of the params. See {@link Ext.UpdateManager#update} for more details. (defaults to null)
     * @param {Boolean} loadOnce (optional) Whether to only load the content once. If this is false it makes the Ajax call every time this AccordianPanelItem is activated. (defaults to false)
     * @param {Object} config (optional) Configuration options for the update call. See {@link Ext.UpdateManager#update} for more details. (defaults to null)
     * @return {Ext.UpdateManager} The UpdateManager
     */
    getValue: function () {
        return this.el.getValue();
    }
});

Rack.widget.CriteriaBuilderField.prototype.fieldTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({
        tag: 'td', 
        cls: 'rack-cbuilder', 
        children: [
            {tag: 'input', type: 'text', id: '{id}', cls: 'rack-cbuilder'}
        ]
    });
    tpl.compile();
    return tpl;
}();

Rack.widget.CriteriaBuilderField.prototype.optionTemplate = function () {
    var tpl = Ext.DomHelper.createTemplate({tag: 'option', value: '{value}', html: '{name}'});
    tpl.compile();
    return tpl;
}();