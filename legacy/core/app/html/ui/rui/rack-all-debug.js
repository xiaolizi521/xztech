/*
 * Rackspace Extensions
 * Copyright(c) 2007 Rackspace Managed Hosting
 * Author: Steve Kollars
 * Version: 1.0
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

var Rack = {
    behavior: {},
    data: {},
    util: {},
    widget: {},
    
    copy: function (o) {
        function F() {}
        F.prototype = o;
        return new F();
    },
    
    
    
    mix: function (base, mixins) {
        var target, p;
        
        if (!mixins) {
            return base;
        }
        
        
        target = function () {
            var args = arguments;
            
            base.apply(this, args);
            
            mixins.forEach(function (e, i, a) {
                e.apply(this, args);
            }.createDelegate(this));
        };
        
        
        Ext.extend(target, base);
        
        
        mixins.forEach(function (e, i, a) {
            for (p in e.prototype) {
                
                if (!target.prototype[p]) {
                    target.prototype[p] = e.prototype[p];
                }
            }
        });
        
        return target;
    },
    
    scope: function (fn) {
        if (Array.si(fn)) {
            return fn[0].createDelegate(fn[1]);
        } else if (Function.si(fn)) {
            return fn;
        } else {
            return function () {};
        }
    },

    sequence: function () {
        var l = arguments.length, fns = arguments;
        
        if (l > 1) {
            return function () {
                var val = Rack.scope(fns[0]).apply(this, arguments);
                var i = 1;
                
                for (; i < l; ++i) {
                    Rack.scope(fns[i]).apply(this, arguments);
                }
                
                return val;
            };
        } else if (l === 1) {
            return this.scope(arguments[0]);
        } else {
            return function () {};
        }
    }
};




Function.prototype.si = function (v) {
    try {
        return v instanceof this;
    } catch (e) {
        return false;
    }
};

Boolean.si = function (v) {
    return typeof v === 'boolean';
};

Number.si = function (v) {
    return typeof v === 'number' && isFinite(v);
};

String.si = function (v) {
    return typeof v === 'string';
};

Array.si = function (v) {
    return v && typeof v === 'object' && typeof v.length === 'number' &&
	          !(v.propertyIsEnumerable('length'));
};

function isEmpty(o) {
    var i, v;
    if (Object.si(o)) {
        for (i in o) {
            v = o[i];
            if (v !== undefined && !Function.si(v)) {
                return false;
            }
        }
    }
    return true;
}
String.prototype.entityify = function () {
    return this.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
};

String.prototype.quote = function () {
    var c, i, l = this.length, o = '"';
    for (i = 0; i < l; i += 1) {
        c = this.charAt(i);
        if (c >= ' ') {
            if (c === '\\' || c === '"') {
                o += '\\';
            }
            o += c;
        } else {
            switch (c) {
            case '\b':
                o += '\\b';
                break;
            case '\f':
                o += '\\f';
                break;
            case '\n':
                o += '\\n';
                break;
            case '\r':
                o += '\\r';
                break;
            case '\t':
                o += '\\t';
                break;
            default:
                c = c.charCodeAt();
                o += '\\u00' + Math.floor(c / 16).toString(16) +
                    (c % 16).toString(16);
            }
        }
    }
    return o + '"';
};

String.prototype.supplant = function (o) {
    var i, j, s = this, v;
    for (;;) {
        i = s.lastIndexOf('{');
        if (i < 0) {
            break;
        }
        j = s.indexOf('}', i);
        if (i + 1 >= j) {
            break;
        }
        v = o[s.substring(i + 1, j)];
        if (!String.si(v) && !Number.si(v)) {
            break;
        }
        s = s.substring(0, i) + v + s.substring(j + 1);
    }
    return s;
};

String.prototype.trim = function () {
    return this.replace(/^\s*(\S*(\s+\S+)*)\s*$/, "$1");
};






if (!Array.prototype.forEach) {
    Array.prototype.forEach = function (fun) {
        var len = this.length, thisp = arguments[1], i = 0;
        if (typeof fun !== "function") {
            throw new TypeError();
        }

        for (; i < len; i++) {
            if (i in this) {
                fun.call(thisp, this[i], i, this);
            }
        }
    };
}

if (!Array.prototype.filter) {
    Array.prototype.filter = function (fun) {
        var len = this.length;
        var res = [];
        var thisp = arguments[1];
        var i = 0, val;
        if (typeof fun !== "function") {
            throw new TypeError();
        }

        for (; i < len; i++) {
            if (i in this) {
                val = this[i]; 
                if (fun.call(thisp, val, i, this)) {
                    res.push(val);
                }
            }
        }

        return res;
    };
}

if (!Array.prototype.map) {
    Array.prototype.map = function (fun)   {
        var len = this.length;
        var res = [];
        var thisp = arguments[1];
        var i = 0;
        if (typeof fun !== "function") {
            throw new TypeError();
        }

        for (; i < len; i++) {
            if (i in this) {
                res[i] = fun.call(thisp, this[i], i, this);
            }
        }

        return res;
    };
}

if (!Array.prototype.some) {
    Array.prototype.some = function (fun) {
        var len = this.length, thisp = arguments[1], i = 0;
        if (typeof fun !== "function") {
            throw new TypeError();
        }

        for (; i < len; i++) {
            if (i in this && fun.call(thisp, this[i], i, this)) {
                return true;
            }
        }

        return false;
    };
}

if (!Array.prototype.every) {
    Array.prototype.every = function (fun) {
        var len = this.length, thisp = arguments[1], i = 0;
        if (typeof fun !== "function") {
            throw new TypeError();
        }

        for (; i < len; i++) {
            if (i in this && !fun.call(thisp, this[i], i, this)) {
                return false;
            }
        }

        return true;
    };
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (elt) {
        var len = this.length,
            from = Number(arguments[1]) || 0;
        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
        if (from < 0) {
            from += len;
        }

        for (; from < len; from++) {
            if (from in this && this[from] === elt) {
                return from;
            }
        }
        return -1;
    };
}

if (!Array.prototype.lastIndexOf) {
    Array.prototype.lastIndexOf = function (elt) {
        var len = this.length,
            from = Number(arguments[1]);
        if (isNaN(from)) {
            from = len - 1;
        } else {
            from = (from < 0) ? Math.ceil(from) : Math.floor(from);
            if (from < 0) {
                from += len;
            } else if (from >= len) {
                from = len - 1;
            }
        }

        for (; from > -1; from--) {
            if (from in this && this[from] === elt) {
                return from;
            }
        }
        return -1;
    };
}



Ext.menu.Menu.prototype.load = function (url, params) {
    var loader = new Ext.menu.Item({text: 'Loading...'});
    this.addItem(loader);
    Ext.lib.Ajax.request((params) ? 'POST' : 'GET', url, {
        success: function (o) {
            this.remove(loader);
            Ext.util.JSON.decode(o.responseText).menu.forEach(function (e, i, a) {
                this.add(e);
            }.createDelegate(this));
        }.createDelegate(this),
        failure: function (o) {
            this.remove(loader);
            this.add({text: 'Failed to load menu items'});
        }.createDelegate(this)
    }, params);
};


Ext.grid.TableGrid = function(table, config) {
    config = config || {};
    var cf = config.fields || [], ch = config.columns || [];
    table = Ext.get(table);

    var ct = table.insertSibling();

    var fields = [], cols = [];
    var headers = table.query('thead[@class="grid-headers"]/tr/th');
	for (var i = 0, h; h = headers[i]; i++) {
		var text = h.innerHTML;
		var name = 'tcol-'+i;

        fields.push(Ext.applyIf(cf[i] || {}, {
            name: name,
            mapping: 'td:nth('+(i+1)+')/@innerHTML'
        }));

		cols.push(Ext.applyIf(ch[i] || {}, {
			'header': text,
			'dataIndex': name,
			'width': h.offsetWidth,
			'tooltip': h.title,
            'sortable': true
        }));
	}

    var ds = new Ext.data.Store({
        reader: new Ext.data.XmlReader({
            record: 'tbody[@class="grid-records"]/tr'
        }, fields)
    });

	ds.loadData(table.dom);

    var cm = new Ext.grid.ColumnModel(cols);

    if(config.width || config.height){
        ct.setSize(config.width || 'auto', config.height || 'auto');
    }
    if(config.remove !== false){
        table.remove();
    }

    Ext.grid.TableGrid.superclass.constructor.call(this, ct,
        Ext.applyIf(config, {
            'ds': ds,
            'cm': cm,
            'sm': new Ext.grid.RowSelectionModel(),
            autoHeight:true,
            autoWidth:true
        }
    ));
};
Ext.extend(Ext.grid.TableGrid, Ext.grid.Grid);

Rack.util.removeContextMenu = function (el) {
    Ext.fly(el).on('contextmenu', function (e) {
        e.stopEvent();
    });
    return this;
};

Rack.util.addContextMenu = function (el, menu) {
    var show = function (e) {
        menu.showAt([e.getPageX(), e.getPageY()]); 
    };
    
    if (Ext.isOpera) { 
        Ext.fly(el).on('mousedown', function (e) {
            if (e.ctrlKey) {
                show(arguments);
            }
        });
    } else {
        Ext.fly(el).on('contextmenu', show);
    }

    return this;
};

(function () { 


var parsers = [];

Rack.util.startParsers = function () {
    this.runParsers(true);
};

Rack.util.runParsers = function (all) {
    parsers.forEach(function (e) {
        e[0].apply(e[1] || window, [(all) ? null : this.dom]);
    });
    return this;
};

Rack.util.registerParser = function (p, s) {
    parsers.push([p, s]);
    return this;
};


Ext.Element.prototype.update = Rack.sequence(
    Ext.Element.prototype.update, 
    Rack.util.runParsers);

Ext.onReady(Rack.util.startParsers, Rack.util);



Rack.util.registerParser(function (el) {
    Ext.DomQuery.select('table.rack-grid', el).
    map(function (e, i, a) {
        
        
        Ext.fly(e).removeClass('rack-grid');
        return e;
    }).
    forEach(function (e, i, a) {
        var g = new Ext.grid.TableGrid(e);
        g.render();
        g.getSelectionModel().lock();
    });
});


Rack.util.registerParser(function (el) {
    Ext.DomQuery.select('a.rack-button', el).
    map(function (e, i, a) {
        
        
        Ext.fly(e).removeClass('rack-button');
        return e;
    }).
    forEach(function (e, i, a) {
        if (e && e.parentNode) {
            var b = new Ext.Button(e.parentNode, {
                text: e.innerHTML,
                handler: e.onclick
            });
            
            b.el.insertBefore(e);
            Ext.fly(e).remove();
        }
    });
});


})();


Rack.util.Essence = function (o) {
    if (o) {
        this.applyParams(o);
    }
};

Rack.util.Essence.prototype = {
    target: null,
    
    setTarget: function (t) {
        if (!this.target) {
            this.target = t;
        }
        return this;
    },
    
    applyParams: function (o) {
        if (!this.params) {
            this.params = {};
            this.defaultParams = {};
        }
        return this.p_applyParams(this.params, o);
    },
    
    p_applyParams: function (c, o) {
        if (o) {
            Ext.apply(c, o);
        }
        return this;
    },
    
    addParams: function (o) {
        if (!this.params) {
            this.params = {};
            this.defaultParams = {};
        }
        this.p_applyParams(this.params, o);
        this.p_applyParams(this.defaultParams, o);
        this.p_createParamSetters(o);
        return this;
    },
    
    clearParams: function () {
        this.params = Rack.copy(this.defaultParams);
        return this;
    },
    
    paramSetterName: function (x) {
        return 'set' + x.substr(0, 1).toUpperCase() + x.substr(1);
    },
    
    paramSetterFunction: function (p) {
        return function (v) {
            this.params[p] = v;
            return this;
        };
    },
    
    p_createParamSetters: function (o) {
        var x, p;
        for (x in o) {
            p = this.paramSetterName(x);
            if (!this[p]) {
                this[p] = this.paramSetterFunction(x);
            }
        }
        return this;
    },
    
    validate: function () {
        return true;
    },
    
    create: function () {
        var Target = this.target;
        
        if (!Target) {
            throw new TypeError('Invalid Essence.  Target has not been defined.');
        }
        
        if (this.validate()) {
            return new Target(this.params);
        }
    }
};



Rack.util.BehavioralEssence = function (o) {
    Rack.util.BehavioralEssence.superclass.constructor.call(this, o);
};

Ext.extend(Rack.util.BehavioralEssence, Rack.util.Essence, {
    using: function (c) {
        if (!this.behaviors) {
            this.behaviors = [];
        }
        this.behaviors.push(c);
        
        this.addParams(c.prototype.getParamDefaults());
        return this;
    },
    
    create: function () {
        var Target = this.target;
        
        if (!Target) {
            throw new TypeError('Invalid Essence.  Target has not been defined.');
        }
        
        
        
        if (this.behaviors) {
            Target = Rack.mix(this.target, this.behaviors);
        }
        
        if (this.validate()) {
            return new Target(this.params);
        }
    }
});

Rack.data.DLL = function () {};
Rack.data.DLL.prototype = {
    firstNode: null,
    lastNode: null,
    
    insertAfter: function (node, oldNode) {
        node.prev = oldNode;
        node.next = oldNode.next;
        
        if (oldNode.next === null) {
            this.lastNode = node;
        } else {
            oldNode.next.prev = node;
        }
        oldNode.next = node;
    },

    insertBefore: function (node, oldNode) {
        node.prev = oldNode.prev;
        node.next = oldNode;

        if (oldNode.prev === null) {
            this.firstNode = node;
        } else {
            oldNode.prev.next = node;
        }
        oldNode.prev = node;
    },

    insertBeginning: function (node) {
        if (this.firstNode === null) {
            this.firstNode = node;
            this.lastNode  = node;
            node.prev = null;
            node.next = null;
        } else {
            this.insertBefore(node, this.firstNode);
        }
    },

    insertEnd: function (node) {
        if (this.lastNode === null) {
            this.insertBeginning(node);
        } else {
            this.insertAfter(node, this.lastNode);
        }
    },
    
    remove: function (node) {
        if (!node) {
            return null;
        }
        
        if (node.prev === null) {
            this.firstNode = node.next;
        } else {
            node.prev.next = node.next;
        }

        if (node.next === null) {
            this.lastNode = node.prev;
        } else {
            node.next.prev = node.prev;
        }

        return node;
    },
    
    swap: function (node1, node2) {
        var t = node1.data;
        node1.data = node2.data;
        node2.data = t;
    },
    
    listOrdered: function () {
        var item = this.firstNode;
        var list = [];
        if (item) {
            do {
                list.push(Rack.copy(item));
                item = item.next;
            } while (item);
        }
        return list;
    }
};


Rack.data.DLLNode = function (data) {
    this.next = null;
    this.prev = null;
    this.data = data;
};

Rack.data.DLLI = function () {
    this.superclass = Rack.data.DLLI.superclass;
};
Ext.extend(Rack.data.DLLI, Rack.data.DLL, {
    index: {},
    
    item: function (id) {
        return this.index[id];
    },
    
    insertAfter: function (node, oldNode) {
        this.index[node.id] = node;
        this.superclass.insertAfter.apply(this, arguments);
    },
    
    insertBefore: function (node, oldNode) {
        this.index[node.id] = node;
        this.superclass.insertBefore.apply(this, arguments);
    },
    
    insertBeginning: function (node) {
        this.index[node.id] = node;
        this.superclass.insertBeginning.apply(this, arguments);
    },
    
    insertEnd: function (node) {
        this.index[node.id] = node;
        this.superclass.insertEnd.apply(this, arguments);
    },
    
    swap: function (node1, node2) {
        var t = node1.id;
        node1.id = node2.id;
        node2.id = t;
        this.index[node1.id] = node1;
        this.index[node2.id] = node2;
        this.superclass.swap.apply(this, arguments);
    }
});

Rack.data.DLLINode = function (id, data) {
    this.id = id;
    Rack.data.DLLINode.superclass.constructor.call(this, data);
};
Ext.extend(Rack.data.DLLINode, Rack.data.DLLNode);
Rack.behavior.ExpandCollapse = function (config) {
    this.p_expanding = false;
    this.p_expandCallback = null;
    this.p_collapsing = false;
    this.p_collapseCallback = null;
    this.collapsed = false;
    
    this.alwaysExpanded = config.alwaysExpanded;
    this.alwaysCollapsed = config.alwaysCollapsed;
    
    this.expandDuration = config.expandDuration;
    this.expandTransition = config.expandTransition;
    this.collapseDuration = config.collapseDuration;
    this.collapseTransition = config.collapseTransition;
    
    this.hideContentOnExpandCollapse = config.hideContentOnExpandCollapse;
    
    this.addEvents({
        
        'beforeexpand': true,
        
        'expand': true,
        
        'beforecollapse': true,
        
        'collapse': true
    });
    
    if (this.alwaysExpanded) {
        this.expand(null, true);
    } else if (this.alwaysCollapsed) {
        this.collapse(null, true);
    } else if (config.collapsed) {
        this.collapse(null, true);
    } else {
        this.expand(null, true);
    }
};

Rack.behavior.ExpandCollapse.prototype = {
    
    getParamDefaults: function () {
        return {
            collapsed: false,
            alwaysExpanded: false,
            alwaysCollapsed: false,
            expandDuration: 0.35,
            expandTransition: 'easeOut',
            collapseDuration: 0.35,
            collapseTransition: 'easeOut',
            hideContentOnExpandCollapse: false
        };
    },
    
    getAlwaysExpanded: function () {
        return this.alwaysExpanded;
    },
    
    getAlwaysCollapsed: function () {
        return this.alwaysCollapsed;
    },
    
    expand: function (cb, now) {
        var bc = this.getBodyContainer();
        
        if (this.p_expanding || !this.collapsed) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforeexpand', this, e);
        if (e.cancel !== true) {
            this.p_expanding = true;
            this.p_expandCallback = cb;
            
            bc.removeClass('rack-hide');
            
            if (now || !this.expandDuration) {
                bc.setHeight(this.getBody().getHeight());
                this.afterExpand();
            } else {
                bc.setHeight(this.bodyHeight || this.getBody().getHeight(), {
                    duration: this.expandDuration, 
                    callback: Rack.scope([this.afterExpand, this]), 
                    method: this.expandTransition
                });
            }
        }
        return this;
    },
    
    afterExpand: function () {
        if (this.hideContentOnExpandCollapse) {
            this.getBody().removeClass('rack-hide');
        }
            
        this.getBodyContainer().removeClass('rack-panel-body-clip');
        this.syncContentHeight();
        
        this.p_expanding = false;
        this.collapsed = false;
        
        if (this.p_expandCallback) {
            this.p_expandCallback();
        }
        
        this.fireEvent('expand', this);
    },
    
    collapse: function (cb, now) {
        var bc = this.getBodyContainer();
        
        if (this.p_collapsing || this.collapsed) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforecollapse', this, e);
        if (e.cancel !== true) {
            this.p_collapsing = true;
            this.p_collapseCallback = cb;
            
            if (this.hideContentOnExpandCollapse) {
                this.getBody().addClass('rack-hide');
            }
            
            bc.addClass('rack-panel-body-clip');
            if (now || !this.collapseDuration) {
                bc.setHeight(1);
                this.afterCollapse();
            } else {
                bc.setHeight(1, {
                    duration: this.collapseDuration,
                    callback: Rack.scope([this.afterCollapse, this]),
                    method: this.collapseTransition
                }); 
            }
        }
    },
    
    afterCollapse: function () {
        this.getBodyContainer().addClass('rack-hide');
        
        this.p_collapsing = false;
        this.collapsed = true;

        if (this.p_collapseCallback) {
            this.p_collapseCallback();
        }
        
        this.fireEvent('collapse', this);
    },
    
    isCollapsed: function () {
        return this.collapsed;
    }
};
Rack.behavior.ShowHide = function (config) {
    this.p_showing = false;
    this.p_showCallback = null;
    this.p_hiding = false;
    this.p_hideCallback = null;
    this.hidden = false;
    
    this.showDuration = config.showDuration;
    this.hideDuration = config.hideDuration;
    
    this.addEvents({
        
        'beforeshow': true,
        
        'show': true,
        
        'beforehide': true,
        
        'hide': true
    });
    
    if (config.hidden) {
        this.hide(null, true);
    } else {
        this.show(null, true);
    }
};

Rack.behavior.ShowHide.prototype = {
    
    getParamDefaults: function () {
        return {
            hidden: false,
            showDuration: 0.30,
            hideDuration: 0.20
        };
    },
    
    show: function (cb, now) {
        if (this.p_showing || !this.hidden) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforeshow', this, e);
        if (e.cancel !== true) {
            this.p_showing = true;
            this.p_showCallback = cb;
            
            if (now || !this.showDuration) {
                this.getContainer().setVisible(true);
                this.afterShow();
            } else {
                this.getContainer().fadeIn({
                    endOpacity: 1,
                    duration: this.showDuration,
                    callback: Rack.scope([this.afterShow, this])
                });
            }
        }
        
        return this;
    },
    
    afterShow: function () {
        this.p_showing = false;
        this.hidden = false;

        if (this.p_showCallback) {
            this.p_showCallback();
        }
        
        this.fireEvent('show', this);
    },
    
    hide: function (cb, now) {
        if (this.p_hiding || this.hidden) {
            return this;
        }
        
        var e = {};
        this.fireEvent('beforehide', this, e);
        if (e.cancel !== true) {
            this.p_hiding = true;
            this.p_hideCallback = cb;
            
            if (now || !this.hideDuration) {
                this.getContainer().setVisible(false);
                this.afterHide();
            } else {
                this.getContainer().fadeOut({
                    endOpacity: 0,
                    duration: this.hideDuration,
                    callback: Rack.scope([this.afterHide, this])
                });
            }
        }
        
        return this;
    },
    
    afterHide: function () {
        this.p_hiding = false;
        this.hidden = true;

        if (this.p_hideCallback) {
            this.p_hideCallback();
        }
        
        this.fireEvent('hide', this);
    },
    
    isHidden: function () {
        return this.hidden;
    }
};
Rack.behavior.Remote = function (config) {
    this.p_loading = false;
    this.p_loaded = false;
    
    if (config.url) {
        this.setUrl(config.url, config.params, config.urlOptions);
    }
};

Rack.behavior.Remote.prototype = {
    
    getParamDefaults: function () {
        return {
            url: null,
            params: {},
            urlOptions: {timeout: 10}
        };
    },
    
    
    getUpdateManager: function () {
        return this.getBody().getUpdateManager();
    },
    
    
    setUrl: function (url, params, urlOptions) {
        
        this.p_currentRefresher = this.p_refresher.createDelegate(this, [url, params, urlOptions]);
        this.refresh();
        return this.getUpdateManager();
    },
    
    
    p_refresher: function (url, params, urlOptions) {
        var update = Rack.copy(urlOptions);
        if (!this.p_loading) {
            this.getBodyContainer().addClass('rack-panel-body-clip');
            
            update.url = url;
            update.params = params;
            update.callback = update.callback ? 
                    Rack.sequence([this.p_contentReady, this], [update.callback, update.scope]) :
                    Rack.scope([this.p_contentReady, this]);
            
            this.p_loading = true;
            this.getUpdateManager().update(update);
        }
    },
    
    
    refresh: function () {
        if (this.p_currentRefresher) {
            this.p_loaded = false;
            this.p_currentRefresher();
        }
        return this;
    },

    
    
    p_contentReady: function (el, s, r) {
        if (!s) {
            this.setContent('Failed to load content from server.');
        }
        
        this.syncContentHeight();
        
        this.getBodyContainer().removeClass('rack-panel-body-clip');
        
        this.p_loading = false;
        this.p_loaded = true;
    }
};

Rack.behavior.AccordionPanelRemote = function (config) {
    this.p_loading = false;
    this.p_loaded = false;
    
    if (config.url) {
        this.setUrl(config.url, config.params, config.loadOnce, config.urlOptions);
    }
};

Ext.extend(Rack.behavior.AccordionPanelRemote, Rack.behavior.Remote, {
    
    getParamDefaults: function () {
        return {
            url: null,
            params: {},
            loadOnce: false,
            urlOptions: {timeout: 10}
        };
    },

    
    setUrl: function (url, params, loadOnce, urlOptions) {
        if (this.p_currentRefresher) {
            
            this.un('beforeexpand', this.p_currentRefresher);
        }
        
        if (loadOnce) {
            this.p_loaded = false;
            this.showReloadButton();
        } else {
            this.hideReloadButton();
        }
        
        
        this.p_currentRefresher = this.p_refresher.createDelegate(this, [url, params, loadOnce, urlOptions]);
        
        
        this.on('beforeexpand', this.p_currentRefresher);
        
        
        if (!this.isCollapsed()) {
            this.p_currentRefresher();
        }
        return this.getUpdateManager();
    },
    
    p_refresher: function (url, params, loadOnce, urlOptions) {
        if (loadOnce && this.p_loaded) {
            return;
        }
        Rack.behavior.AccordionPanelRemote.superclass.p_refresher.call(this, url, params, urlOptions);
    }
});

Rack.DumbComboBox = function (config) {
    config = config || {};
    Rack.DumbComboBox.superclass.constructor.call(this, config);
    this.addEvents({
        'expand': true,
        'collapse': true,
        'beforeselect': true,
        'select': true,
        
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



Rack.widget.Themes = {
    DEFAULT: [],
    AERO: ['/ui/ext/resources/css/ytheme-aero.css', '/ui/rui/resources/css/theme-aero.css'],
    GRAY: ['/ui/ext/resources/css/ytheme-gray.css', '/ui/rui/resources/css/theme-gray.css'],
    VISTA: ['/ui/ext/resources/css/ytheme-vista.css', '/ui/rui/resources/css/theme-vista.css'],
    
    state: {theme: []},
    
    show: function (theme, hide) {
        var addCSS = this.addExternalCSS;
        
        
        Ext.select('link.rack-themes-include').set({disabled: 'true'}).remove();
        
        Ext.each(theme, function (e) {
            addCSS(e);
        });
        this.state.theme = theme;
        this.storeState();
        
        if (!this.dialogHidden) {
            this.hideDialog();
        }
    },
    
    
    addExternalCSS: function (url) {
        var l = document.createElement('link');
        l.rel = 'stylesheet';
        l.type = 'text/css';
        l.href = url;
        l.className = 'rack-themes-include';
        
        document.getElementsByTagName('head')[0].appendChild(l);
    },
    
    dialog: null,
    dialogHidden: true,
    
    showDialog: function (from) {
        var dialog, layout, dh;
        if (!this.dialog) {
            dh = Ext.DomHelper;
            dialog = new Ext.BasicDialog(Ext.id(), { 
                autoCreate: true,
                title: 'Theme Picker',
                modal: true,
                width: 300,
                height: 230,
                shadow: true,
                proxyDrag: true,
                resizeHandles: 'none',
                syncHeightBeforeShow: true,
                autoScroll: false
            });
            
            dialog.addKeyListener(27, this.hideDialog, this);
            
            Ext.fly(dh.append(dialog.body, {tag: 'a', href: '#', cls: 'rack-themes-btn rack-themes-default-btn', html: 'Default'})).
                on('click', function (e) {
                    e.stopEvent();
                    this.show(this.DEFAULT);
                }, this);
            Ext.fly(dh.append(dialog.body, {tag: 'a', href: '#', cls: 'rack-themes-btn rack-themes-aero-btn', html: 'Aero'})).
                on('click', function (e) {
                    e.stopEvent();
                    this.show(this.AERO);
                }, this);
            Ext.fly(dh.append(dialog.body, {tag: 'a', href: '#', cls: 'rack-themes-btn rack-themes-gray-btn', html: 'Gray'})).
                on('click', function (e) {
                    e.stopEvent();
                    this.show(this.GRAY);
                }, this);
            Ext.fly(dh.append(dialog.body, {tag: 'a', href: '#', cls: 'rack-themes-btn rack-themes-vista-btn', html: 'Vista'})).
                on('click', function (e) {
                    e.stopEvent();
                    this.show(this.VISTA);
                }, this);

            this.dialog = dialog;
        }
        this.dialogHidden = false;
        this.dialog.show(from);
    },
    
    hideDialog: function (callback) {
        this.dialog.hide(callback);
        this.dialogHidden = true;
    },
    
    restoreState: function (provider) {
        this.provider = provider || Ext.state.Manager;
        var state = this.provider.get("rack-themes-state");
        if (state && state.theme) {
            this.show(state.theme);
            this.state = state;
        }
        return this;
    },
    
    storeState: function () {
        this.provider.set("rack-themes-state", this.state);
        return this;
    }
};

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
    
    
    if (Array.si(this.headers)) {
        
        
        var fl = this.fields.length,
            hl = this.headers.length;
        this.headers.forEach(function (e, i, a) {
            var cs = hl === 1 ? fl + 2 : hl - 1 === i && fl - hl > 0 ? fl - hl : 1;
            this.headerTemplate.append(this.head, {text: e, colspan: cs});
        }.createDelegate(this));
        
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
            
            proxy: new Ext.data.HttpProxy({url: this.url}),
            
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
    
    
    
    domChange: function () {
        this.setParam(this.el.getValue());
    },
    
    
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

Rack.widget.TitlebarEssence = function (o) {
    this.setTarget(Rack.widget.Titlebar);

    this.addParams({
        
        parent: null,
        
        
        id: Ext.id(),
        
        
        text: '&#160;',
        
        
        buttons: [],
        
        
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




Rack.widget.PanelEssence = function (o) {
    this.setTarget(Rack.widget.Panel);
    
    this.addParams({
        parent: null,
        
        
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



Rack.widget.Panel = function (config) {
    
    this.parent = config.parent;
    
    
    this.container = Ext.get(this.containerTemplate.append(this.parent, {}));
    
    
    this.headerContainer = this.container.child('.rack-panel-header');
    
    
    this.header = null;
    if (config.header) {
        this.header = (new Rack.widget.TitlebarEssence(config.header)).
            setParent(this.headerContainer).
            create();
    }
    
    
    this.toolbarContainer = this.container.child('.rack-panel-toolbar');
    
    
    this.toolbar = null;
    if (Array.si(config.toolbar)) {
        this.toolbar = new Ext.Toolbar(this.toolbarContainer, config.toolbar);
    }
    
    
    this.bodyContainer = this.container.child('.rack-panel-body');
    
    
    this.body = this.bodyContainer.child('.rack-panel-body-widget');
    
    
    
    if (config.body && !config.grid) {
        this.body = Ext.get(config.body).replace(this.body);
    }
    
    
    this.grid = null;
    if (config.grid) {
        this.grid = this.createGrid(config.grid);
        if (this.grid) {
            this.body.appendChild(this.grid.container);
            this.body.removeClass('rack-panel-body-widget');
        }
    }
    
    
    this.bodyHeight = null;
    if (this.bodyHeight) {
        this.setHeight(config.bodyHeight);
    }
    
    
    this.bodyWidth = null;
    if (this.bodyWidth) {
        this.setWidth(config.bodyWidth);
    }
    
    
    this.content = null;
    this.loadScripts = null;
    if (this.content) {
        this.setContent(config.content, config.loadScripts);
    }
    
    
    this.footerContainer = this.container.child('.rack-panel-footer');
    
    
    this.footer = null;
    if (Array.si(config.footer)) {
        this.footer = new Ext.Toolbar(this.footerContainer, config.footer);
    } 

    
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
    
    
    getHeaderContainer: function () {
        return this.headerContainer;
    },
    
    
    getHeader: function () {
        return this.header;
    },
    
    
    getToolbarContainer: function () {
        return this.toolbarContainer;
    },
    
    
    getToolbar: function () {
        return this.toolbar;
    },
    
    
    getBodyContainer: function () {
        return this.bodyContainer;
    },
    
    
    getBody: function () {
        return this.body;
    },
    
    
    getFooterContainer: function () {
        return this.footerContainer;
    },
    
    
    getFooter: function () {
        return this.footer;
    },
    
    
    setContent: function (content, loadScripts) {
        this.content = content;
        this.loadScripts = loadScripts;
        this.body.update(content, loadScripts);
        
        this.syncContentHeight();
        
        return this;
    },
    
    
    syncContentHeight: function () {
        this.bodyContainer.setHeight(this.bodyHeight || this.body.getHeight());
        return this;
    },
    
    
    getHeight: function () {
        return this.bodyHeight;
    },
    
    
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
    
    
    getWidth: function () {
        return this.bodyWidth;
    },
    
    
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

Rack.widget.AccordionPanel = function (config) {
    this.addEvents({
         
        'activate': true,
        
        
        
        
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
 
    
    config.header = config.header || {text: config.title || 'Accordion Panel'};
    
    Rack.widget.AccordionPanel.superclass.constructor.call(this, config);
    
    
    this.header.el.on('click', function () {
        this.activate();
    }, this);
    this.header.el.addClass('rack-accordion-panel-title');

    this.loading = false;
    this.loaded = false;
    
    
    
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
        
        this.elDS = new Rack.widget.AccordionPanelDragSource(this.getContainer(), {
            ddGroup: this.accordion.el.id, 
            dragData: {panel: this},
            scroll: false
        });
        
        this.elDS.setHandleElId(this.header.id);
        
        
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
        
        this.accordion.removePanel(this.id);
    },
    
    
    showReloadButton: function () {
        this.reloadEl.removeClass('rack-display-hide');
    },
    
    
    hideReloadButton: function () {
        this.reloadEl.addClass('rack-display-hide');
    },

    
    activate: function (now) {
        this.accordion.activate(this.id, now);
        this.fireEvent('activate', this);
        return this;
    },
    
    
    
    insertBefore: function (id) {
        
        
        
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


Rack.widget.AccordionPanelEssence = function (o) {
    this.setTarget(Rack.widget.AccordionPanel);

    this.addParams({
        
        accordion: null,
        
        
        stateId: null,

        
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

Rack.widget.AccordionPanelDropTarget = function (panel, config) {
    this.panel = panel;
    this.el = panel.getContainer();
    
    Ext.apply(this, config);
    
    Ext.dd.ScrollManager.register(this.el);
    Ext.dd.DropTarget.superclass.constructor.call(this, this.el.dom, this.ddGroup || this.group, {isTarget: true});
};

Ext.extend(Rack.widget.AccordionPanelDropTarget, Ext.dd.DDTarget, {
    isTarget: true,
    isNotifyTarget: true,
    dropAllowed: "x-dd-drop-ok",
    dropNotAllowed: "x-dd-drop-nodrop",
    
    notifyEnter: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.showOver();
            return this.dropAllowed;
        }
        return this.dropNotAllowed;
    },
    
    notifyOver: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            return this.dropAllowed;
        }
        return this.dropNotAllowed;
    },
    
    notifyOut: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.hideOver();
        }
    },
    
    notifyDrop: function (dd, e, data) {
        if (data.panel.getId() !== this.panel.getId()) {
            this.hideOver();
            data.panel.insertBefore(this.panel.getId());
            return true;
        }
        return false;
    },
    
    overClass: 'rack-accordion-panel-on',
    
    showOver: function () {
        this.el.addClass(this.overClass);
    },
    
    hideOver: function () {
        this.el.removeClass(this.overClass);
    }
});


Rack.widget.AccordionPanelDragSource = function (el, config) {
    this.proxy = new Rack.widget.AccordionPanelStatusProxy();

    Rack.widget.AccordionPanelDragSource.superclass.constructor.call(this, el, config);
};

Ext.extend(Rack.widget.AccordionPanelDragSource, Ext.dd.DragSource, {
    afterRepair: function () {
        this.dragging = false;
    }
});


Rack.widget.AccordionPanelStatusProxy = function (config) {
    Ext.apply(this, config);
    this.id = this.id || Ext.id();
    this.el = new Ext.Layer({
        dh: {
            id: this.id, 
            tag: "div", 
            cls: "x-dd-drag-proxy " + this.dropNotAllowed, 
            children: [
                {tag: "div", cls: "x-dd-drop-icon"},
                {
                    tag: 'div', 
                    cls: 'x-dd-drag-ghost rack-accordion-panel-ghost', 
                    children: [
                        {tag: "div", cls: "rack-accordion-panel-ghost-inner"}
                    ]
                }
            ]
        }, 
        shadow: !config || config.shadow !== false
    });
    this.ghost = Ext.get(this.el.dom.childNodes[1].childNodes[0]);
    this.dropStatus = this.dropNotAllowed;
};

Ext.extend(Rack.widget.AccordionPanelStatusProxy, Ext.dd.StatusProxy);


Rack.widget.Accordion = function (config) {
    this.parent = config.parent;
    this.container = this.el = Ext.DomHelper.append(this.parent, {}, true);
    this.panels = new Rack.data.DLLI();  
    this.stateIdMap = {}; 
    this.active = null;
    this.config = config;
    
    
    this.panelEssence = (new Rack.widget.AccordionPanelEssence()).
        using(Rack.behavior.ShowHide).
        using(Rack.behavior.ExpandCollapse).
        using(Rack.behavior.AccordionPanelRemote);
    
    this.addEvents({
        
        'beforepanelactivate': true,
        
        'panelactivate': true,
        
        'panelexpanded': true,
        
        'panelcollapsed': true,
        
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
        
        if (this.config.multi) {
            return this;
        }
        
        this.el.setHeight(x);
        
        
        var p = this.panels.firstNode;
        while (p) {
            x -= p.data.getHeader().getHeight();
            p = p.next;
        }
        
        
        p = this.panels.firstNode;
        while (p) {
            p.data.setHeight(x);
            p = p.next;
        }
        
        return this;
    },
    
    setWidth: function (x) {
        
        p = this.panels.firstNode;
        while (p) {
            p.data.setWidth(x);
            p = p.next;
        }
    
        this.el.setWidth(x);
        return this;
    },
    
    
    addPanel: function (config) {
        
        var p = this.panelEssence.
            clearParams(). 
            setCollapsed(true). 
            setBodyHeight(this.config.panelHeight).
            setAccordion(this).
            setParent(this.getBody()).
            applyParams(config).
            create();
        
        
        this.panels.insertEnd(new Rack.data.DLLINode(p.getId(), p));
        
        this.p_setPanelStateId(p.getId(), p.getStateId());
        
        return p;
    },

    
    p_setPanelStateId: function (id, stateId) {
        if (id && stateId) {
            this.stateIdMap[stateId] = id;
        }
        
        return this;
    },

    
    p_getPanelId: function (id) {
        return this.stateIdMap[id] || id;
    },
    
    
    getPanel: function (id) {
        return this.p_getPanelNode(id).data;
    },
    
    p_getPanelNode: function (id) {
        return this.panels.item(this.p_getPanelId(id));
    },
    
    
    removePanel: function (id) {
        var p = this.panels.remove(this.p_getPanelId(id));
        if (p) {
            if (p.data === this.active && !this.config.multi) {
                
                this.activate(this.panels.firstNode.id);
            }
            
            p.data.getContainer().remove();
        }
        return this;
    },
    
    
    expandPanel: function (id, now) {
        var p = this.getPanel(id);
        if (p) {
            this.expandPanelObject(p, now);
        }
        return this;
    },
    
    
    
    expandPanelObject: function (p, now) {
        p.expand(null, now);
        this.fireEvent('panelexpanded', this, p);
        return this;
    },
        
    
    collapsePanel: function (id, now) {
        var p = this.getPanel(id);
        if (p) {
            this.collapsePanelObject(p, now);
        }
        return this;
    },
    
    
    
    collapsePanelObject: function (p, now) {
        p.collapse(null, now);
        this.fireEvent('panelcollapsed', this, p);
        return this;
    },
    
    
    activate: function (id, now) {
        var panel = this.getPanel(id);
        var e = {};
        var multi = this.config.multi;
        if (panel === this.active && !multi) {
            return panel;
        } 
        
        
        
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
    
    
    moveUp: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = p1 && p1.prev ? p1.prev : null;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    
    moveDown: function (id) {
        var p2 = this.p_getPanelNode(id),
            p1 = p2 && p2.next ? p2.next : null;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    
    insertPanelBeginning: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = this.panels.firstNode;
        this.p_insertPanelBefore(p1, p2);
        return this;
    },

    
    insertPanelBefore: function (id1, id2) {
        var p1 = this.p_getPanelNode(id1),
            p2 = this.p_getPanelNode(id2);
        this.p_insertPanelBefore(p1, p2);
        return this;
    },
    
    
    
    p_insertPanelBefore: function (p1, p2) {
        if (p1 && p2 && p1 !== p2) {
            
            p1.data.getContainer().insertBefore(p2.data.getContainer());
            
            this.panels.insertBefore(this.panels.remove(p1), p2);
            
            var list = this.panels.listOrdered().map(function (e, a, i) {
                return e.data.getId();
            });
            this.fireEvent('panelorder', this, list);
        }
        return this;
    },

    
    insertPanelEnd: function (id) {
        var p1 = this.p_getPanelNode(id),
            p2 = this.panels.lastNode;
        if (p1 && p2 && p1 !== p2) {
            
            p1.data.getContainer().insertAfter(p2.data.getContainer());
            
            this.panels.insertEnd(this.panels.remove(p1));
            
            var list = this.panels.listOrdered().map(function (e, a, i) {
                return e.data.getId();
            });
            this.fireEvent('panelorder', this, list);
        }
        return this;
    },
    
    
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


Rack.widget.AccordionEssence = function (o) {
    this.setTarget(Rack.widget.Accordion);
    
    this.addParams({
        
        parent: null,
        
        
        multi: false,
        
        
        stateId: null,
        
        
        panelHeight: null,
        
        
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


Rack.widget.AccordionStateManager = function () {
     
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
            
            if (Array.si(state.order)) {
                Ext.each(state.order, function (e, a, i) {
                    accordion.insertPanelEnd(e);
                });
            } else {
                state.order = [];
            }
            
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
