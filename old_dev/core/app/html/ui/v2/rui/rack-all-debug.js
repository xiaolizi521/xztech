/*extern Ext*/

var Rack = {
    widget: {},
    app: {},
    abs: {},
    util: {}
};

// ------------------------------------------------------------
// Override this so that we don't try to access extjs.com
// ------------------------------------------------------------
Ext.BLANK_IMAGE_URL = '/ui/v2/rui/resources/images/s.gif';

// The following functions and implementations were taken
// from Douglas Crockford's website (javascript.crockford.com).

// These are the old ones.  Just need to ensure that no one is using them.
// DEPRECATED!
Function.prototype.si = function (v) {
    try {
        return v instanceof this;
    } catch (e) {
        return false;
    }
};

// DEPRECATED!
Boolean.si = function (v) {
    return typeof v === 'boolean';
};

// DEPRECATED!
Number.si = function (v) {
    return typeof v === 'number' && isFinite(v);
};

// DEPRECATED!
String.si = function (v) {
    return typeof v === 'string';
};

// DEPRECATED!
Array.si = function (v) {
    return v && typeof v === 'object' && typeof v.length === 'number' &&
	          !(v.propertyIsEnumerable('length'));
};

// These are the new ones.  Use these!
function typeOf(value) {
    var s = typeof value;
    if (s === 'object') {
        if (value) {
            if (typeof value.length === 'number' &&
                    !(value.propertyIsEnumerable('length')) &&
                    typeof value.splice === 'function') {
                s = 'array';
            }
        } else {
            s = 'null';
        }
    }
    return s;
}

function isEmpty(o) {
    var i, v;
    if (typeOf(o) === 'object') {
        for (i in o) {
            v = o[i];
            if (v !== undefined && typeOf(v) !== 'function') {
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
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
};

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}; 


// The following extensions to the Array object are meant
// for browsers that do not yet support these functions 
// natively.  The code for these implementations was taken
// from the Mozilla Developer Center (developer.mozilla.org).
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
                val = this[i]; // in case fun mutates this
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

// System for defining interfaces.
Rack.implement = function (cls) {
    if (arguments.length < 2) {
        return;
    }
    var cp = cls.prototype, inter;
    for (var i = 1; i < arguments.length; i += 1) {
        inter = arguments[i];
        Ext.applyIf(cp, inter);
        if (!cp.CLS_INTERFACES) {
            cp.CLS_INTERFACES = [];
        }
        cls.prototype.CLS_INTERFACES.push(inter);
    }
};

Rack.hasInterface = function (ins, inter) {
    if (ins.CLS_INTERFACES && ins.CLS_INTERFACES.indexOf && (ins.CLS_INTERFACES.indexOf(inter) > -1)) {
        return true;
    }
    
    return false;
};


(function () {

// AOP-ish intercepting system.
var RI = Rack.intercept = function (cls, ics) {
    var cp = (typeof cls === "function") ? cls.prototype : cls;
    var pts = null;
    var prop = null;
    var icsProps = RI.getAllProps(ics, true);
    for (var i = 0; i < icsProps.length; i += 1) {
        prop = icsProps[i];
        pts = ics[prop];
        RI.findPropMatches(cp, prop).forEach(function (p) {
            if (typeof cp[p] === "function") {
                RI.handlePoints(cp, p, pts);
            }
        });
    }
};

RI.handlePoints = function (obj, prop, points) {
    if (points.around) {
        RI.applyAroundPoint(obj, prop, points.around);
    }
    if (points.start) {
        RI.applyStartPoint(obj, prop, points.start);
    }
    if (points.end) {
        RI.applyEndPoint(obj, prop, points.end);
    }
    if (points.exception) {
        RI.applyExceptionPoint(obj, prop, points.exception);
    }
};

RI.applyAroundPoint = function (obj, prop, point) {
    var ofn = obj[prop];
    obj[prop] = function () {
        var t = this;
        return point.call(this, prop, ofn, arguments);
    };
};

RI.applyStartPoint = function (obj, prop, point) {
    var ofn = obj[prop];
    obj[prop] = function () {
        var pargs = point.call(this, prop, arguments);
        return ofn.apply(this, (pargs !== undefined) ? pargs : arguments);
    };
};

RI.applyEndPoint = function (obj, prop, point) {
    var ofn = obj[prop];
    obj[prop] = function () {
        var retval = ofn.apply(this, arguments);
        var pretval = point.call(this, prop, retval);
        return (pretval !== undefined) ? pretval : retval;
    };
};

RI.applyExceptionPoint = function (obj, prop, point) {
    var ofn = obj[prop];
    obj[prop] = function () {
        try {
            return ofn.apply(this, arguments);
        } catch (e) {
            point.call(this, prop, e);
            throw e;
        }
    };
};

RI.findPropMatches = function (obj, prop) {
    var sp = prop.indexOf('*');
    if (sp < 0) {
        return [prop];
    } else if (sp > 0) {
        var pp = prop.substr(0, sp);
        return RI.getAllProps(obj, false).filter(function (p) {
            return (p.substr(0, sp) === pp);
        });
    } else {
        return RI.getAllProps(obj, false);
    }
};

RI.getAllProps = function (obj, own) {
    var props = [];
    for (var p in obj) {
        props.push(p);
    }
    // Don't want to leave these out
    if ((props.indexOf('valueOf') === -1) && (!own || (own && obj.hasOwnProperty('valueOf')))) {
        props.push('valueOf');
    }
    if ((props.indexOf('toString') === -1) && (!own || (own && obj.hasOwnProperty('toString')))) {
        props.push('toString');
    }
    if ((props.indexOf('toLocaleString') === -1) && (!own || (own && obj.hasOwnProperty('toLocaleString')))) {
        props.push('toLocaleString');
    }
    return props;
};

})();

var Property = {
    create: function (reader, writer, options) {
        options = options || {};
        if (!((typeof reader) === "function")) {
            throw new TypeError("The reader must be a function.  Got " + (typeof reader) + " instead.");
        }
        var fn = function () {
            var scope = options.scope || this;
            name = options.name || 'undefined';
            if (arguments.length) {
                if (!((typeof writer) === "function")) {
                    throw new Property.ReadOnlyError("This is a read only property. Object: " + scope + ", Property: " + name);
                }
                return writer.apply(scope, arguments);
            } else {
                return reader.apply(scope);
            }
        };
        if (options.scope) {
            fn.toString = function () {
                var v = fn();
                return (v) ? v.toString() : '';
            };
            fn.valueOf = function () {
                var v = fn();
                return (v) ? v.valueOf() : null;
            };
        }
        return fn;
    },
    factory: function (obj) {
        return function (name, reader, writer) {
            obj[name] = Property.create(reader, writer, {scope: obj, name: name});
        };
    }
};
Property.ReadOnlyError = function (msg) {
    this.name = "ReadOnlyError";
    this.message = msg;
};
Property.ReadOnlyError.prototype = new Error();

// Create a special property factory that will fire
// events and provide add/removeListener functions.
Rack.observableFactory = function (obj) {
    return function (name, reader, writer) {
        var eventWriter = function () {
            var ret = writer.apply(this, arguments);
            this.fireEvent(name + "_updated", reader.call(this));
            return ret;
        };
        obj[name] = Property.create(reader, eventWriter, {scope: obj, name: name});
        obj[name].addListener = function (fn, scope) {
            obj.addListener(name + "_updated", fn, scope);
        };
        obj[name].removeListener = function (fn, scope) {
            obj.removeListener(name + "_updated", fn, scope);
        };
    };
};

// Patching Ext.override
// 
// Reason:
// In IE, the valueOf, toString, and toLocaleString properties of an object 
// are not enumerable.  This prevents us from passing an object, in which we 
// define those properties, to a class with the intention of adding those  
// properties to the class prototype.
// 
// Change:
// Explicitly check to see if the object that was passed in has defined any 
// of those properties and, if so, assign them to the class prototype.
// 
Ext.override = function (oc, or) {
    if (or) {
        var p = oc.prototype;
        for (var m in or) {
            p[m] = or[m];
        }
        if (Ext.isIE) {
            var rp = ['valueOf', 'toString', 'toLocaleString'];
            for (var i = 0; i < rp.length; i++) {
                m = rp[i];
                if (or.hasOwnProperty(m)) {
                    p[m] = or[m];
                }
            }
        }
    }
};


// Modifying Ext.extend
// Add superclass, implement, intercept, and extend functionality
(function () {
    var oldExtend = Ext.extend;
    
    Ext.extend = function () {
        var con = oldExtend.apply(null, arguments);
        con.implement = function () {
            var args = Array.prototype.slice.call(arguments);
            Rack.implement.apply(null, [this].concat(args));
        };
        con.prototype.hasInterface = con.hasInterface = function (i) {return Rack.hasInterface(con, i);};
        con.intercept = function (o) {Rack.intercept(con, o);};
        con.prototype.intercept = function (o) {Rack.intercept(this, o);};
        con.extend = function (o) {return Ext.extend(con, o);};
        return con;
    };
})();


// Patching Ext.grid.GridView.findCell
// 
// Reason:
// Grids capture DOM events only at the top-most DOM element of the grid.  
// Because of this, they then have to determine what the intended target of 
// the event was.
// 
// In the case of the 'cellclick' event, a grid will search for a parent of 
// the event's target that matches the base cell element.  However, a grid 
// will only look back through 3 levels of DOM elements in order to find the 
// appropriate matching element.  If there are other nested DOM elements 
// inside of the cell element, it can easily exceed the 3 level depth and the 
// cell element will not be found.  If no cell element is found, the event 
// will not fire.
// 
// Change:
// Let the code look back through 10 levels of DOM elements instead of 3 when 
// trying to find the cell element.
// 
Ext.grid.GridView.prototype.findCell = function (el) {
    if (!el) {
        return false;
    }
    return this.fly(el).findParent(this.cellSelector, 10); // Use 10 instead of 3
};


// Modifying Ext.util.Observable
Ext.override(Ext.util.Observable, {

    // Modify removeListener/un to accept shortcut parameters like 
    // addListener/on.
    // 
    // Example:
    // Something.un({
    //     event1: myFunction,
    //     event2: myFunction,
    //     scope: this
    // });
    removeListener: function (eventName, fn, scope) {
        if (typeof eventName === "object") {
            var o = eventName;
            for (var e in o) {
                if (this.filterOptRe.test(e)) {
                    continue;
                }
                if (typeof o[e] === "function") {
                    // shared options
                    this.removeListener(e, o[e], o.scope);
                } else {
                    // individual options
                    this.removeListener(e, o[e].fn, o[e].scope);
                }
            }
            return;
        }
        
        var ce = this.events[eventName.toLowerCase()];
        if (typeof ce === "object") {
            ce.removeListener(fn, scope);
        }
    },
    
    // Add the addProp method to support dynamic properties
    addProp: function (name, reader, writer) {
        // This will overwrite the addProp function (the one you're currently in) 
        // with a new function and then call the new function.
        // A bit strange, no?
        this.addProp = Rack.observableFactory(this);
        this.addProp(name, reader, writer);
    }
});
Ext.util.Observable.prototype.un = Ext.util.Observable.prototype.removeListener;
Rack.util.Observable = Ext.util.Observable;


Rack.ExtOverrides = function () {
    Ext.Button.override({
        onRender: function (ct, position) {
            if (!this.template) {
                if (!Ext.Button.buttonTemplate) {
                    Ext.Button.buttonTemplate = new Ext.Template(
                        '<table border="0" cellpadding="0" cellspacing="0" class="x-btn-wrap"><tbody><tr>',
                        '<td class="x-btn-left"><i>&#160;</i></td><td class="x-btn-center"><em unselectable="on"><button name="{0}" class="x-btn-text" type="{1}">{0}</button></em></td><td class="x-btn-right"><i>&#160;</i></td>',
                        "</tr></tbody></table>");
                }
                this.template = Ext.Button.buttonTemplate;
            }
            var btn, targs = [this.text || '&#160;', this.type];

            if (position) {
                btn = this.template.insertBefore(position, targs, true);
            } else {
                btn = this.template.append(ct, targs, true);
            }
            var btnEl = btn.child(this.buttonSelector);
            btnEl.on('focus', this.onFocus, this);
            btnEl.on('blur', this.onBlur, this);

            this.initButtonEl(btn, btnEl);

            if (this.menu) {
                this.el.child(this.menuClassTarget).addClass("x-btn-with-menu");
            }
            Ext.ButtonToggleMgr.register(this);
        }
    });
    
    Ext.form.ComboBox.override({
        onRender: function (ct, position) {
            Ext.form.ComboBox.superclass.onRender.call(this, ct, position);
            if (this.hiddenName) {
                this.hiddenField = this.el.insertSibling({
                    tag:'input', 
                    type:'hidden', 
                    name: this.hiddenName,
                    id: (this.hiddenId || this.hiddenName)
                }, 'before', true);
            }
            this.el.dom.name = this.fieldLabel.replace("'","").replace('"','');
            
            if (Ext.isGecko) {
                this.el.dom.setAttribute('autocomplete', 'off');
            }
            if (!this.lazyInit) {
                this.initList();
            } else {
                this.on('focus', this.initList, this, {single: true});
            }
            if (!this.editable) {
                this.editable = true;
                this.setEditable(false);
            }
        }
    });
};


// Modifying Ext.Panel
// 
// Add the showFooter and hideFooter methods
Ext.Panel.override({
    showFooter: function (anim, size) {
        if (this.footerShown) {
            return;
        }
        this.footerShown = true;
        size = size || 37; // 37 is the typical height of a footer that has buttons added
        this.footer.setHeight(size, true);
        this.body.setHeight(this.body.getHeight() - size, anim);
    },
    
    hideFooter: function (anim) {
        this.footerShown = false;
        var fh = this.footer.getHeight();
        this.body.setHeight(this.body.getHeight() + fh, anim);
        this.footer.setHeight(0, anim);
    }
});


// Modifying Ext.Element
// Override default masking behavior to give masks correct z-indexes.
Ext.override(Ext.Element, {
    mask: function (msg, msgCls, zLayer) {
        if (this.getStyle("position") == "static") {
            this.setStyle("position", "relative");
        }
        
        if (this.getStyle("z-index") == "auto" || this.getStyle("z-index") == 0) {
            this.setStyle("z-index", 1);
        }
        
        if (this._maskMsg) {
            this._maskMsg.remove();
        }
        
        if (this._mask) {
            this._mask.remove();
        }
        
        this._mask = Ext.DomHelper.append(this.dom, {cls: "ext-el-mask"}, true);
        
        // pop mask just above the layer of this element unless zLayer is set.
        var maskLayer = zLayer || parseInt(this.getStyle("z-index"), 10) + 1;
        this._mask.setStyle("z-index", maskLayer.toString());
        
        this.addClass("x-masked");
        this._mask.setDisplayed(true);
        
        if (typeof(msg) === 'string') {
            this._maskMsg = Ext.DomHelper.append(this.dom, {cls: "ext-el-mask-msg", cn: {}}, true);
            var mm = this._maskMsg;
            // pop the mask message just above the mask
            var maskMsgLayer = parseInt(this._mask.getStyle("z-index"), 10) + 1;
            mm.setStyle("z-index", maskMsgLayer.toString());
            mm.dom.className = msgCls ? "ext-el-mask-msg " + msgCls : "ext-el-mask-msg";
            mm.dom.firstChild.innerHTML = msg;
            mm.setDisplayed(true);
            mm.center(this);
        }
        
        // IE will not expand full height automatically
        if (Ext.isIE && !(Ext.isIE7 && Ext.isStrict) && this.getStyle('height') == 'auto') { 
            this._mask.setSize(this.dom.clientWidth, this.getHeight());
        }
        
        return this._mask;
    }
}); 
/*

Cookie functions from PPK
http://www.quirksmode.org/js/cookies.html

*/
Rack.createCookie = function (name, value, expires, path, encode) {
    if (expires) {
        if (expires instanceof Date) {
            expires = "; expires=" + expires.toGMTString();
        } else {
            var date = new Date();
            date.setTime(date.getTime() + (expires * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
    } else {
        expires = "";
    }

    if (!path) {
        path = "/";
    }
    
    if (encode) {
        value = encodeURIComponent(value);
    }
    
    document.cookie = name + "=" + value + expires + "; path=" + path;
};

Rack.readCookie = function (name, encoded) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    var c = null;
    
    for (var i = 0; i < ca.length; i++) {
        if (encoded) {
            c = decodeURIComponent(ca[i]);
        } else {
            c = ca[i];
        }
        
        while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length);
        }
        
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
};

Rack.eraseCookie = function (name) {
	Rack.createCookie(name, "" , -1);
};

Rack.msg = function (title, body) {
    var msgCt = Rack.msg.msgCt;
    var msgTpl = Rack.msg.msgTpl;
    
    if (!msgCt) {
        msgCt = Ext.DomHelper.insertFirst(document.body, {
            id: 'msg-div', 
            style: 'width:300px; z-index:20000; position:absolute; padding-top:30px;'
        }, true);
        Rack.msg.msgCt = msgCt;
    }
    msgCt.alignTo(document, 't-t');
    var m = Ext.DomHelper.append(msgCt, {
        html: msgTpl.apply({'t': title, 'b': body})
    }, true);
    m.slideIn('t', {duration: 0.5}).fadeIn({
        duration: 0.5, 
        endOpacity: 0.80, 
        concurrent: true
    }).pause(2).ghost('t', {remove: true});
};

Rack.msg.msgCt = null;
Rack.msg.msgTpl = new Ext.Template(
    '<div style="background-color:#eee; padding:15px; border:1px solid #ccc; font-size:110%; margin-bottom:5px;">',
    '<b>{t}</b><br>{b}',
    '</div>');
Rack.msg.msgTpl.compile();

Rack.error = function (title, body) {
    Ext.Msg.show({
        title: title, 
        msg: body,
        icon: Ext.Msg.ERROR,
        buttons: Ext.Msg.OK,
        width: 400
    });
};

Rack.warn = function (title, body) {
    Ext.Msg.show({
        title: title, 
        msg: body,
        icon: Ext.Msg.WARNING,
        buttons: Ext.Msg.OK,
        width: 400
    });
};


Ext.namespace('Rack.localStore');
(function () {
var ns = Rack.localStore;

ns.set = function (key, value, life, overwrite) {
    if (!window.globalStorage || !window.globalStorage[window.location.hostname]) {
        return;
    }
    var store = window.globalStorage[window.location.hostname];
    var now = new Date();
    var entry = Ext.encode({
        expires: now.setTime(now.getTime() + life),
        value: value
    });
    if (overwrite || (!overwrite && !ns.get(key))) {
        store[key] = entry;
    }
};

ns.get = function (key) {
    if (!window.globalStorage || !window.globalStorage[window.location.hostname]) {
        return;
    }
    var store = window.globalStorage[window.location.hostname];
    var entry = store[key];
    if (entry) {
        entry = Ext.decode(entry);
        if (entry.expires > (new Date()).getTime()) {
            return entry.value;
        }
    }
    return null;
};

})();


Ext.namespace('Rack.abs');
(function () {
var supercon = Ext.util.Observable;
var superproto = supercon.prototype;
var con = Rack.abs.CommandableDocument = Ext.extend(supercon, {
    constructor: function () {
        this.commands = [];
        this.commandPtr = 0;
        
        this.addEvents(
            'cmd_executed', 
            'cmd_undone', 
            'cmd_redone');
    },
    
    execute: function (cmd) {
        if (cmd.undo) {
            this.commands[this.commandPtr] = cmd;
            this.commandPtr += 1;
            this.commands = this.commands.slice(0, this.commandPtr);
        }
        
        cmd.execute(this);
        this.fireEvent('cmd_executed', this, cmd);
        
        return this;
    },
    
    undo: function (level) {
        var i = 0;
        var cmd = null;
        
        level = (level) ? 
            (typeof level === 'object') ? 
                this.commandPtr - this.commands.indexOf(level) : 
                level :
            1;
        
        for (i = 0; i < level; i++) {
            if (this.commandPtr > 0) {
                this.commandPtr -= 1;
                cmd = this.commands[this.commandPtr];
                cmd.undo(this);
                this.fireEvent('cmd_undone', this, cmd);
            }
        }
        
        return this;
    },
    
    redo: function (level) {
        var i = 0;
        var cmd = null;
        
        level = (level) ? 
            (typeof level === 'object') ? 
                this.commands.indexOf(level) + 1 - this.commandPtr : 
                level :
            1;
        
        for (i = 0; i < level; i++) {
            if (this.commandPtr < this.commands.length) {
                cmd = this.commands[this.commandPtr];
                this.commandPtr += 1;
                cmd.execute(this);
                this.fireEvent('cmd_redone', this, cmd);
            }
        }
        
        return this;
    },
    
    getCommandHistory: function () {
        return this.commands;
    }
});
})();


Ext.grid.CheckColumn = function (config) {
    Ext.apply(this, config);
    if (!this.id) {
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

Ext.grid.CheckColumn.prototype = {
    init: function (grid) {
        this.grid = grid;
        this.grid.on('render', function () {
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },
    
    onMouseDown: function (e, t) {
        if (t.className && t.className.indexOf('x-grid3-cc-' + this.id) != -1) {
            e.stopEvent();
            var index = this.grid.getView().findRowIndex(t);
            var record = this.grid.store.getAt(index);
            record.set(this.dataIndex, !record.data[this.dataIndex]);
        }
    },
    
    renderer: function (v, p, record) {
        p.css += ' x-grid3-check-col-td'; 
        return '<div class="x-grid3-check-col' + (v ? '-on' : '') + ' x-grid3-cc-' + this.id + '">&#160;</div>';
    }
};


/*
 * Ext JS Library 2.0 Beta 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */
Ext.grid.RowExpander = function (config) {
    Ext.apply(this, config);
    Ext.grid.RowExpander.superclass.constructor.call(this);
    if (this.tpl) {
        if (typeof this.tpl == 'string') {
            this.tpl = new Ext.Template(this.tpl);
        }
        this.tpl.compile();
    }
    this.state = {};
    this.bodyContent = {};
    this.addEvents({
        beforeexpand: true,
        expand: true,
        beforecollapse: true,
        collapse: true
    });
};

Ext.extend(Ext.grid.RowExpander, Ext.util.Observable, {
    header: "",
    width: 20,
    sortable: false,
    fixed: true,
    dataIndex: '',
    id: 'expander',
    lazyRender: true,
    enableCaching: true,
    getRowClass: function (record, rowIndex, p, ds) {
        p.cols = p.cols - 1;
        var content = this.bodyContent[record.id];
        if (!content && !this.lazyRender) {
            content = this.getBodyContent(record, rowIndex);
        }
        if (content) {
            p.body = content;
        }
        return this.state[record.id] ? 'x-grid3-row-expanded' : 'x-grid3-row-collapsed';
    },
    init: function (grid) {
        this.grid = grid;
        var view = grid.getView();
        view.getRowClass = this.getRowClass.createDelegate(this);
        view.enableRowBody = true;
        grid.on('render', function () {
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },
    getBodyContent: function (record, index) {
        if (!this.enableCaching) {
            return this.tpl.apply(record.data);
        }
        var content = this.bodyContent[record.id];
        if (!content) {
            content = this.tpl.apply(record.data);
            this.bodyContent[record.id] = content;
        }
        return content;
    },
    onMouseDown: function(e, t) {
        if (t.className == 'x-grid3-row-expander') {
            e.stopEvent();
            var row = e.getTarget('.x-grid3-row');
            this.toggleRow(row);
        }
    },
    renderer: function (v, p, record) {
        p.cellAttr = 'rowspan="2"';
        return '<div class="x-grid3-row-expander">&#160;</div>';
    },
    beforeExpand: function (record, body, rowIndex) {
        if (this.fireEvent('beforexpand', this, record, body, rowIndex) !== false) {
            if (this.tpl && this.lazyRender) {
                body.innerHTML = this.getBodyContent(record, rowIndex);
            }
            return true;
        } else {
            return false;
        }
    },
    toggleRow: function (row) {
        if (typeof row == 'number') {
            row = this.grid.view.getRow(row);
        }
        this[Ext.fly(row).hasClass('x-grid3-row-collapsed') ? 'expandRow' : 'collapseRow'](row);
    },
    expandRow: function (row) {
        if (typeof row == 'number') {
            row = this.grid.view.getRow(row);
        }
        var record = this.grid.store.getAt(row.rowIndex);
        var body = Ext.DomQuery.selectNode('tr:nth(2) div.x-grid3-row-body', row);
        if (this.beforeExpand(record, body, row.rowIndex)) {
            this.state[record.id] = true;
            Ext.fly(row).replaceClass('x-grid3-row-collapsed', 'x-grid3-row-expanded');
            this.fireEvent('expand', this, record, body, row.rowIndex);
        }
    },
    collapseRow: function (row) {
        if (typeof row == 'number') {
            row = this.grid.view.getRow(row);
        }
        var record = this.grid.store.getAt(row.rowIndex);
        var body = Ext.fly(row).child('tr:nth(1) div.x-grid3-row-body', true);
        if (this.fireEvent('beforcollapse', this, record, body, row.rowIndex) !== false) {
            this.state[record.id] = false;
            Ext.fly(row).replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');
            this.fireEvent('collapse', this, record, body, row.rowIndex);
        }
    }
});
/*
 * @class Ext.ux.ManagedIFrame
 * Version:  1.1
 * Author: Doug Hendricks. doug[always-At]theactivegroup.com
 * Copyright 2007-2008, Active Group, Inc.  All rights reserved.
 *
 ************************************************************************************
 *   This file is distributed on an AS IS BASIS WITHOUT ANY WARRANTY;
 *   without even the implied warranty of MERCHANTABILITY or
 *   FITNESS FOR A PARTICULAR PURPOSE.
 ************************************************************************************

 License: ux.ManagedIFrame and ux.ManagedIFramePanel are licensed under the terms of
 the Open Source LGPL 3.0 license.  Commercial use is permitted to the extent
 that the code/component(s) do NOT become part of another Open Source or Commercially
 licensed development library or toolkit without explicit permission.

 Donations are welcomed: http://donate.theactivegroup.com

 License details: http://www.gnu.org/licenses/lgpl.html

 * <p> An Ext harness for iframe elements.

  Adds Ext.UpdateManager(Updater) support and a compatible 'update' method for
  writing content directly into an iFrames' document structure.

  Signals various DOM/document states as the frames content changes with 'domready',
  'documentloaded', and 'exception' events.  The domready event is only raised when
  a proper security context exists for the frame's DOM to permit modification.
  (ie, Updates via Updater or documents retrieved from same-domain servers).

  Frame sand-box permits eval/script-tag writes of javascript source.
  (See execScript, writeScript, and loadFunction methods for more info.)

  * Usage:<br>
   * <pre><code>
   * // Harnessed from an existing Iframe from markup:
   * var i = new Ext.ux.ManagedIFrame("myIframe");
   * // Replace the iFrames document structure with the response from the requested URL.
   * i.load("http://myserver.com/index.php", "param1=1&amp;param2=2");

   * // Notes:  this is not the same as setting the Iframes src property !
   * // Content loaded in this fashion does not share the same document namespaces as it's parent --
   * // meaning, there (by default) will be no Ext namespace defined in it since the document is
   * // overwritten after each call to the update method, and no styleSheets.
  * </code></pre>
  * <br>
   * @cfg {Boolean/Object} autoCreate True to auto generate the IFRAME element, or a {@link Ext.DomHelper} config of the IFRAME to create
   * @cfg {String} html Any markup to be applied to the IFRAME's document content when rendered.
   * @cfg {Object} loadMask An {@link Ext.LoadMask} config or true to mask the iframe while using the update or setSrc methods (defaults to false).
   * @cfg {Object} src  The src attribute to be assigned to the Iframe after initialization (overrides the autoCreate config src attribute)
   * @constructor
  * @param {Mixed} el, Config object The iframe element or it's id to harness or a valid config object.

 * Release:  1.1  (4/13/2008) Adds Ext.Element, CSS Selectors (query,select) fly, and CSS
                                    interface support (same-domain only)
                              Adds blur,focus,unload events (same-domain only)

  */

(function(){

var EV = Ext.lib.Event;

Ext.ux.ManagedIFrame = function(){
    var args=Array.prototype.slice.call(arguments, 0)
        ,el = Ext.get(args[0])
        ,config = args[0];

    if(el && el.dom && el.dom.tagName == 'IFRAME'){
            config = args[1] || {};
    }else{
            config = args[0] || args[1] || {};

            el = config.autoCreate?
            Ext.get(Ext.DomHelper.append(config.autoCreate.parent||document.body,
                Ext.apply({tag:'iframe', src:(Ext.isIE&&Ext.isSecure)?Ext.SSL_SECURE_URL:''},config.autoCreate))):null;
    }

    if(!el || el.dom.tagName != 'IFRAME') return el;

    el.dom.name || (el.dom.name = el.dom.id); //make sure there is a valid frame name

    this.addEvents({
        /**
         * @event focus
         * Fires when the frame gets focus.
         * @param {Ext.ux.ManagedIFrame} this
         * @param {Ext.Event}
         * Note: This event is only available when overwriting the iframe document using the update method and to pages
         * retrieved from a "same domain".
         * Returning false from the eventHandler [MAY] NOT cancel the event, as this event is NOT ALWAYS cancellable in all browsers.
         */
        "focus"         : true,

        /**
         * @event blur
         * * Fires when the frame is blurred (loses focus).
         * @param {Ext.ux.ManagedIFrame} this
         * @param {Ext.Event}
         * Note: This event is only available when overwriting the iframe document using the update method and to pages
         * retrieved from a "same domain".
         * Returning false from the eventHandler [MAY] NOT cancel the event, as this event is NOT ALWAYS cancellable in all browsers.
         */
        "blur"          : true,

        /**
         * @event unload
         * * Fires when(if) the frames window object raises the unload event
         * @param {Ext.ux.ManagedIFrame} this
         * @param {Ext.Event}
         * Note: This event is only available when overwriting the iframe document using the update method and to pages
         * retrieved from a "same domain".
         *Note: Opera does not raise this event.
         */
        "unload"        : true,

       /**
         * @event domready
         * Fires ONLY when an iFrame's Document(DOM) has reach a state where the DOM may be manipulated (ie same domain policy)
         * @param {Ext.ux.ManagedIFrame} this
         * Note: This event is only available when overwriting the iframe document using the update method and to pages
         * retrieved from a "same domain".
         * Returning false from the eventHandler stops further event (documentloaded) processing.
         */
        "domready"       : true,

       /**
         * @event documentloaded
         * Fires when the iFrame has reached a loaded/complete state.
         * @param {Ext.ux.ManagedIFrame} this
         */
        "documentloaded" : true,

        /**
         * @event exception
         * Fires when the iFrame raises an error
         * @param {Ext.ux.ManagedIFrame} this
         * @param {Object/string} exception
         */
        "exception" : true,
        /**
         * @event message
         * Fires upon receipt of a message generated by window.sendMessage method of the embedded Iframe.window object
         * @param {Ext.ux.ManagedIFrame} this
         * @param {object} message (members:  type: {string} literal "message",
         *                                    data {Mixed} [the message payload],
         *                                    domain [the document domain from which the message originated ],
         *                                    uri {string} the document URI of the message sender
         *                                    source (Object) the window context of the message sender
         *                                    tag {string} optional reference tag sent by the message sender
         */
        "message" : true
        /**
         * Alternate event handler syntax for message:tag filtering
         * @event message:tag
         * Fires upon receipt of a message generated by window.sendMessage method
         * which includes a specific tag value of the embedded Iframe.window object
         * @param {Ext.ux.ManagedIFrame} this
         * @param {object} message (members:  type: {string} literal "message",
         *                                    data {Mixed} [the message payload],
         *                                    domain [the document domain from which the message originated ],
         *                                    uri {string} the document URI of the message sender
         *                                    source (Object) the window context of the message sender
         *                                    tag {string} optional reference tag sent by the message sender
         */
        //"message:tagName"  is supported for X-frame messaging
    });

    if(config.listeners){
        this.listeners=config.listeners;
        Ext.ux.ManagedIFrame.superclass.constructor.call(this);
    }

    Ext.apply(el,this);  // apply this class interface ( pseudo Decorator )

    el.addClass('x-managed-iframe');
    if(config.style){
        el.applyStyles(config.style);
    }

    el._maskEl = el.parent('.x-managed-iframe-mask')||el.parent().addClass('x-managed-iframe-mask');

    Ext.apply(el,{
      disableMessaging : config.disableMessaging===true
     ,loadMask         : Ext.apply({msg:'Loading..'
                            ,msgCls:'x-mask-loading'
                            ,maskEl: el._maskEl
                            ,hideOnReady:true
                            ,disabled:!config.loadMask},config.loadMask)
    //Hook the Iframes loaded state handler
     ,_eventName       : Ext.isIE?'onreadystatechange':'onload'
     ,_windowContext   : null
     ,eventsFollowFrameLinks  : typeof config.eventsFollowFrameLinks=='undefined'?
                                true  :  config.eventsFollowFrameLinks
    });

    el.dom[el._eventName] = el.loadHandler.createDelegate(el);

    if(document.addEventListener){  //for Gecko and Opera and any who might support it later
       Ext.EventManager.on(window,"DOMFrameContentLoaded", el.dom[el._eventName]);
    }

    var um = el.updateManager=new Ext.UpdateManager(el,true);
    um.showLoadIndicator= config.showLoadIndicator || false;

    if(config.src){
        el.setSrc(config.src);
    }else{

        var content = config.html || config.content || false;

        if(content){
            el.update.defer(10,el,[content]); //allow frame to quiesce
        }
    }

    return Ext.ux.ManagedIFrame.Manager.register(el);

};

var MIM = Ext.ux.ManagedIFrame.Manager = function(){
  var frames = {};
  return {
    shimCls      : 'x-frame-shim',
    register     :function(frame){
        frame.manager = this;
        frames[frame.id] = frames[frame.dom.name] = {ref:frame, elCache:{}};
        return frame;
    },

    deRegister     :function(frame){

        frame._unHook();
        delete frames[frame.id];
        delete frames[frame.dom.name];

    },
    hideShims : function(){

        if(!this.shimApplied)return;
        Ext.select('.'+this.shimCls,true).removeClass(this.shimCls+'-on');
        this.shimApplied = false;
    },

    /* Mask ALL ManagedIframes (eg. when a region-layout.splitter is on the move.)*/
    showShims : function(){
       if(!this.shimApplied){
          this.shimApplied = true;
          //Activate the shimCls globally
          Ext.select('.'+this.shimCls,true).addClass(this.shimCls+'-on');
       }

    },
    getFrameById  : function(id){

       return typeof id == 'string'?(frames[id]?frames[id].ref||null:null):null;

    },

    getFrameByName : function(name){
        return this.getFrameById(name);
    },

    //retrieve the internal frameCache object
    getFrameHash  : function(frame){
       return frame.id?frames[frame.id]:null;

    },
    //to be called under the scope of the managing MIF
    eventProxy     : function(e){
        e = Ext.lib.Event.getEvent(e);
        if(!e)return;
        var be=e.browserEvent||e;

        //same-domain unloads should clear ElCache for use with the next document rendering
        if(e.type == 'unload'){ this._unHook(); }

        if(!be['eventPhase'] || (be['eventPhase'] == (be['AT_TARGET']||2))){
            return this.fireEvent(e.type, e);
        }
    },

    _flyweights : {},

    //safe removal of embedded frame element
    removeNode : Ext.isIE ?
               function(frame, n){
                    frame = MIM.getFrameHash(frame);
                    if(frame && n && n.tagName != 'BODY'){
                        d = frame.scratchDiv || (frame.scratchDiv = frame.getDocument().createElement('div'));
                        d.appendChild(n);
                        d.innerHTML = '';
                    }
                }
              : function(frame, n){
                    if(n && n.parentNode && n.tagName != 'BODY'){
                        n.parentNode.removeChild(n);
                    }
                }


   }
 }();

 MIM.showDragMask = MIM.showShims;
 MIM.hideDragMask = MIM.hideShims;

 //Provide an Ext.Element interface to frame document elements
 MIM.El =function(frame, el, forceNew){

     var frameObj;
     frame = (frameObj = MIM.getFrameHash(frame))?frameObj.ref:null ;

     if(!frame ){ return null; }
     var elCache = frameObj.elCache || (frameObj.elCache = {});

     var dom = frame.getDom(el);

     if(!dom){ // invalid id/element
         return null;
     }
     var id = dom.id;
     if(forceNew !== true && id && elCache[id]){ // element object already exists
         return elCache[id];
     }

     /**
      * The DOM element
      * @type HTMLElement
      */
     this.dom = dom;

     /**
      * The DOM element ID
      * @type String
      */
    this.id = id || Ext.id(dom);
 };

 MIM.El.get =function(frame, el){
     var ex, elm, id, doc;
     if(!frame || !el ){ return null; }

     var frameObj;
     frame = (frameObj = MIM.getFrameHash(frame))?frameObj.ref:null ;

     if(!frame ){ return null;}

     var elCache = frameObj.elCache || (frameObj.elCache = {} );

     if(!(doc = frame.getDocument())){ return null; }
     if(typeof el == "string"){ // element id
         if(!(elm = frame.getDom(el))){
             return null;
         }
         if(ex = elCache[el]){
             ex.dom = elm;
         }else{
             ex = elCache[el] = new MIM.El(frame, elm);
         }
         return ex;
     }else if(el.tagName){ // dom element
         if(!(id = el.id)){
             id = Ext.id(el);
         }
         if(ex = elCache[id]){
             ex.dom = el;
         }else{
             ex = elCache[id] = new MIM.El(frame, el);
         }
         return ex;
     }else if(el instanceof MIM.El){
         if(el != frameObj.docEl){
             el.dom = frame.getDom(el.id) || el.dom; // refresh dom element in case no longer valid,
                                                     // catch case where it hasn't been appended
             elCache[el.id] = el; // in case it was created directly with Element(), let's cache it
         }
         return el;
     }else if(el.isComposite){
         return el;
     }else if(Ext.isArray(el)){
         return frame.select(el);
     }else if(el == doc){
         // create a bogus element object representing the document object
         if(!frameObj.docEl){
             var f = function(){};
             f.prototype = MIM.El.prototype;
             frameObj.docEl = new f();
             frameObj.docEl.dom = doc;
         }
         return frameObj.docEl;
     }
    return null;

 };

 Ext.apply(MIM.El.prototype,Ext.Element.prototype);



 Ext.extend(Ext.ux.ManagedIFrame , Ext.util.Observable,
    {

    src : null ,
      /**
      * Sets the embedded Iframe src property.

      * @param {String/Function} url (Optional) A string or reference to a Function that returns a URI string when called
      * @param {Boolean} discardUrl (Optional) If not passed as <tt>false</tt> the URL of this action becomes the default SRC attribute for
      * this iframe, and will be subsequently used in future setSrc calls (emulates autoRefresh by calling setSrc without params).
      * Note:  invoke the function with no arguments to refresh the iframe based on the current src value.
     */
    setSrc : function(url, discardUrl, callback){
          var reset = Ext.isIE&&Ext.isSecure?Ext.SSL_SECURE_URL:'';
          var src = url || this.src || reset;

          if(Ext.isOpera){
              this.dom.src = reset;
           }
          this._windowContext = null;
          this._unHook();

          this._callBack = callback || false;

          this.showMask();

          (function(){
                var s = typeof src == 'function'?src()||'':src;
                try{
                    this._frameAction = true; //signal listening now
                    this.dom.src = s;
                    this.frameInit= true; //control initial event chatter
                    this.checkDOM();
                }catch(ex){ this.fireEvent('exception', this, ex); }

          }).defer(10,this);

          if(discardUrl !== true){ this.src = src; }

          return this;

    },
    reset     : function(src, callback){
          this.setSrc(src || (Ext.isIE&&Ext.isSecure?Ext.SSL_SECURE_URL:''),true,callback);

    },
    //Private: script removal RegeXp
    scriptRE  : /(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)/gi
    ,
    /*
     * Write(replacing) string content into the IFrames document structure
     * @param {String} content The new content
     * @param {Boolean} loadScripts (optional) true to also render and process embedded scripts
     * @param {Function} callback (optional) Callback when update is complete.
     */
    update : function(content,loadScripts,callback){

        loadScripts = loadScripts || this.getUpdateManager().loadScripts || false;

        content = Ext.DomHelper.markup(content||'');
        content = loadScripts===true ? content:content.replace(this.scriptRE , "");

        var doc;

        if(doc = this.getDocument()){

            this._frameAction = !!content.length;
            this._windowContext = this.src = null;
            this._callBack = callback || false;
            this._unHook();
            this.showMask();
            doc.open();
            doc.write(content);
            doc.close();
            this.frameInit= true; //control initial event chatter
            if(this._frameAction){
                this.checkDOM();
            } else {
                this.hideMask(true);
                if(this._callBack)this._callBack();
            }

        }else{
            this.hideMask(true);
            if(this._callBack)this._callBack();
        }
        return this;
    },

    /* Enables/disables x-frame messaging interface */
    disableMessaging :  true,

    //Private, frame messaging interface (for same-domain-policy frames only)
    _XFrameMessaging  :  function(){
        //each tag gets a hash queue ($ = no tag ).
        var tagStack = {'$' : [] };
        var isEmpty = function(v, allowBlank){
             return v === null || v === undefined || (!allowBlank ? v === '' : false);
        };
        window.sendMessage = function(message, tag, origin ){
            var MIF;
            if(MIF = arguments.callee.manager){
                if(message._fromHost){
                    var fn, result;
                    //only raise matching-tag handlers
                    var compTag= message.tag || tag || null;
                    var mstack = !isEmpty(compTag)? tagStack[compTag.toLowerCase()]||[] : tagStack["$"];

                    for(var i=0,l=mstack.length;i<l;i++){
                        if(fn = mstack[i]){
                            result = fn.apply(fn.__scope,arguments)===false?false:result;
                            if(fn.__single){mstack[i] = null;}
                            if(result === false){break;}
                        }
                    }

                    return result;
                }else{

                    message =
                        {type   :isEmpty(tag)?'message':'message:'+tag.toLowerCase().replace(/^\s+|\s+$/g,'')
                        ,data   :message
                        ,domain :origin || document.domain
                        ,uri    :document.documentURI
                        ,source :window
                        ,tag    :isEmpty(tag)?null:tag.toLowerCase()
                        };

                    try{
                       return MIF.disableMessaging !== true
                        ? MIF.fireEvent.call(MIF,message.type,MIF, message)
                        : null;
                    }catch(ex){} //trap for message:tag handlers not yet defined

                    return null;
                }

            }
        };
        window.onhostmessage = function(fn,scope,single,tag){

            if(typeof fn == 'function' ){
                if(!isEmpty(fn.__index)){
                    throw "onhostmessage: duplicate handler definition" + (tag?" for tag:"+tag:'');
                }

                var k = isEmpty(tag)? "$":tag.toLowerCase();
                tagStack[k] || ( tagStack[k] = [] );
                Ext.apply(fn,{
                   __tag    : k
                  ,__single : single || false
                  ,__scope  : scope || window
                  ,__index  : tagStack[k].length
                });
                tagStack[k].push(fn);

            } else
               {throw "onhostmessage: function required";}


        };
        window.unhostmessage = function(fn){
            if(typeof fn == 'function' && typeof fn.__index != 'undefined'){
                var k = fn.__tag || "$";
                tagStack[k][fn.__index]=null;
            }
        };


    }
    ,get   :function(el){
             return  MIM.El.get(this, el);
         }

    ,fly : function(el, named){
        named = named || '_global';
        el = this.getDom(el);
        if(!el){
            return null;
        }
        if(!MIM._flyweights[named]){
            MIM._flyweights[named] = new Ext.Element.Flyweight();
        }
        MIM._flyweights[named].dom = el;
        return MIM._flyweights[named];
    }

    ,getDom  : function(el){
         var d;
         if(!el || !(d = this.getDocument())){
            return null;
         }
         return el.dom ? el.dom : (typeof el == 'string' ? d.getElementById(el) : el);

    }
    /**
     * Creates a {@link Ext.CompositeElement} for child nodes based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @param {Boolean} unique (optional) True to create a unique Ext.Element for each child (defaults to false, which creates a single shared flyweight object)
     * @return {CompositeElement/CompositeElementLite} The composite element
     */
    ,select : function(selector, unique){
        var d;
        return (d = this.getDocument())?Ext.Element.select(selector, unique, d):null;
     }

    /**
     * Selects frame document child nodes based on the passed CSS selector (the selector should not contain an id).
     * @param {String} selector The CSS selector
     * @return {Array} An array of the matched nodes
     */
    ,query : function(selector){
        var d;
        return (d = this.getDocument())?Ext.DomQuery.select(selector, d):null;
     }


    /**
     * Returns the current HTML document object as an {@link Ext.Element}.
     * @return Ext.Element The document
    */
    ,getDoc  : function(){

        return this.get(this.getDocument());

    }

    /**
     * Removes a DOM Element from the embedded documents
     * @param {Element, String} node The node id or node Element to remove

     */
    ,removeNode  : function(node){
        MIM.removeNode(this,this.getDom(node));
    }

    //Private : clear all event listeners and Element cache
    ,_unHook     : function(){

        var elcache, h = MIM.getFrameHash(this)||{};

        if( this._hooked && h && (elcache = h.elCache)){

            for (var id in elcache){
                var el = elcache[id];

                delete elcache[id];
                if(el.removeAllListeners)el.removeAllListeners();
            }
            if(h.docEl){
                h.docEl.removeAllListeners();
                h.docEl=null;
                delete h.docEl;
            }
        }
        this._hooked = this._domReady = this._domFired = false;

    }
    //Private execScript sandbox and messaging interface
    ,_renderHook : function(){

        this._windowContext = null;
        this._hooked = false;
        try{
           if(this.writeScript('(function(){(window.hostMIF = parent.Ext.get("'+
                                this.dom.id+
                                '"))._windowContext='+
                                (Ext.isIE?'window':'{eval:function(s){return eval(s);}}')+
                                ';})();')
                ){
                this._frameProxy || (this._frameProxy = MIM.eventProxy.createDelegate(this));
                EV.doAdd(this.getWindow(), 'focus', this._frameProxy);
                EV.doAdd(this.getWindow(), 'blur',  this._frameProxy);
                EV.doAdd(this.getWindow(), 'unload', this._frameProxy );

                if(this.disableMessaging !== true){
                   this.loadFunction({name:'XMessage',fn:this._XFrameMessaging},false,true);
                   var sm;
                   if(sm=this.getWindow().sendMessage){
                       sm.manager = this;
                   }
                }
                this.CSS = new CSSInterface(this.getDocument());
           }
           return this.domWritable();
          }catch(ex){}

        return false;

    },
    /* dispatch a message to the embedded frame-window context */
    sendMessage : function (message,tag,origin){
         var win;
         if(this.disableMessaging !== true && (win = this.getWindow())){
              //support frame-to-frame messaging relay
              tag || (tag= message.tag || '');
              tag = tag.toLowerCase();
              message = Ext.applyIf(message.data?message:{data:message},
                                 {type   :Ext.isEmpty(tag)?'message':'message:'+tag
                                 ,domain :origin || document.domain
                                 ,uri    : document.documentURI
                                 ,source : window
                                 ,tag    :tag || null
                                 ,_fromHost: this
                    });
             return win.sendMessage?win.sendMessage.call(null,message,tag,origin): null;
         }
         return null;

    },
    _windowContext : null,
    /*
      Return the Iframes document object
    */
    getDocument:function(){
        var win;
        return (win = this.getWindow())?win.document:null;
    },

    //Attempt to retrieve the frames current document.body
    getBody : function(){
        var d;
        return (d = this.getDocument())?d.body:null;

    },

    //Attempt to retrieve the frames current URI
    getDocumentURI : function(){
        var URI;
        try{
           URI = this.src?this.getDocument().location.href:null;
        }catch(ex){} //will fail on NON-same-origin domains

        return URI || this.src;
    },
    /*
     Return the Iframes window object
    */
    getWindow:function(){
        var dom= this.dom;
        return dom?dom.contentWindow||window.frames[dom.name]:null;
    },

    /*
     Print the contents of the Iframes (if we own the document)
    */
    print:function(){
        try{
            var win = this.getWindow();
            if(Ext.isIE){win.focus();}
            win.print();
        } catch(ex){
            throw 'print exception: ' + (ex.description || ex.message || ex);
        }
    },
    //private
    destroy:function(){
        this.removeAllListeners();

        if(this.dom){
             //unHook the Iframes loaded state handlers
             if(document.addEventListener){ //Gecko/Opera
                Ext.EventManager.un(window,"DOMFrameContentLoaded", this.dom[this._eventName]);
               }
             this.dom[this._eventName]=null;
             Ext.ux.ManagedIFrame.Manager.deRegister(this);
             this._windowContext = null;
             //IE Iframe cleanup
             if(Ext.isIE && this.dom.src){
                this.dom.src = 'javascript:false';
             }
             this._maskEl = null;
             Ext.removeNode(this.dom);

        }

        Ext.apply(this.loadMask,{masker :null ,maskEl : null});

    }
    /* Returns the general DOM modification capability of the frame. */
    ,domWritable  : function(){
        return !!this._windowContext;
    }
    /*
     *  eval a javascript code block(string) within the context of the Iframes window object.
     * @param {String} block A valid ('eval'able) script source block.
     * @param {Boolean} useDOM - if true inserts the fn into a dynamic script tag,
     *                           false does a simple eval on the function definition. (useful for debugging)
     * <p> Note: will only work after a successful iframe.(Updater) update
     *      or after same-domain document has been hooked, otherwise an exception is raised.
     */
    ,execScript: function(block, useDOM){
      try{
        if(this.domWritable()){
            if(useDOM){
               this.writeScript(block);
            }else{
                return this._windowContext.eval(block);
            }

        }else{ throw 'execScript:non-secure context' }
       }catch(ex){
            this.fireEvent('exception', this, ex);
            return false;
        }
        return true;

    }
    /*
     *  write a <script> block into the iframe's document
     * @param {String} block A valid (executable) script source block.
     * @param {object} attributes Additional Script tag attributes to apply to the script Element (for other language specs [vbscript, Javascript] etc.)
     * <p> Note: writeScript will only work after a successful iframe.(Updater) update
     *      or after same-domain document has been hooked, otherwise an exception is raised.
     */
    ,writeScript  : function(block, attributes) {
        attributes = Ext.apply({},attributes||{},{type :"text/javascript",text:block});

         try{
            var head,script, doc= this.getDocument();
            if(doc && doc.getElementsByTagName){
                if(!(head = doc.getElementsByTagName("head")[0] )){
                    //some browsers (Webkit, Safari) do not auto-create
                    //head elements during document.write
                    head =doc.createElement("head");
                    doc.getElementsByTagName("html")[0].appendChild(head);
                }
                if(head && (script = doc.createElement("script"))){
                    for(var attrib in attributes){
                          if(attributes.hasOwnProperty(attrib) && attrib in script){
                              script[attrib] = attributes[attrib];
                          }
                    }
                    return !!head.appendChild(script);
                }
            }
         }catch(ex){ this.fireEvent('exception', this, ex);}
         return false;
    }
    /*
     * Eval a function definition into the iframe window context.
     * args:
     * @param {String/Object} name of the function or
                              function map object: {name:'encodeHTML',fn:Ext.util.Format.htmlEncode}
     * @param {Boolean} useDOM - if true inserts the fn into a dynamic script tag,
                                    false does a simple eval on the function definition,
     * examples:
     * var trim = function(s){
     *     return s.replace( /^\s+|\s+$/g,'');
     *     };
     * iframe.loadFunction('trim');
     * iframe.loadFunction({name:'myTrim',fn:String.prototype.trim || trim});
     */
    ,loadFunction : function(fn, useDOM, invokeIt){

       var name  =  fn.name || fn;
       var    fn =  fn.fn   || window[fn];
       this.execScript(name + '=' + fn, useDOM); //fn.toString coercion
       if(invokeIt){
           this.execScript(name+'()') ; //no args only
        }
    }

    //Private
    ,showMask: function(msg,msgCls,forced){
          var lmask;
          if((lmask = this.loadMask) && (!lmask.disabled|| forced)){
               if(lmask._vis)return;
               lmask.masker || (lmask.masker = Ext.get(lmask.maskEl||this.dom.parentNode||this.wrap({tag:'div',style:{position:'relative'}})));
               lmask._vis = true;
               lmask.masker.mask.defer(lmask.delay||5,lmask.masker,[msg||lmask.msg , msgCls||lmask.msgCls] );
           }
       }
    //Private
    ,hideMask: function(forced){
           var tlm;
           if((tlm = this.loadMask) && !tlm.disabled && tlm.masker ){
               if(!forced && (tlm.hideOnReady!==true && this._domReady)){return;}
               tlm._vis = false;
               tlm.masker.unmask.defer(tlm.delay||5,tlm.masker);
           }
    }

    /* Private
      Evaluate the Iframes readyState/load event to determine its 'load' state,
      and raise the 'domready/documentloaded' event when applicable.
    */
    ,loadHandler : function(e){

        if(!this.frameInit || (!this._frameAction && !this.eventsFollowFrameLinks)){return;}

        var rstatus = (e && typeof e.type !== 'undefined'?e.type:this.dom.readyState );
        switch(rstatus){
            case 'loading':  //IE
            case 'interactive': //IE

              break;
            case 'DOMFrameContentLoaded': //Gecko, Opera

              if(this._domFired || (e && e.target !== this.dom)){ return;} //not this frame.

            case 'domready': //MIF
              if(this._domFired)return;
              if(this._domFired = this._hooked = this._renderHook() ){

                 this._frameAction = (this.fireEvent("domready",this) === false?false:this._frameAction);  //Only raise if sandBox injection succeeded (same domain)
              }
            case 'domfail': //MIF

              this._domReady = true;
              this.hideMask();
              break;
            case 'load': //Gecko, Opera
            case 'complete': //IE
              if(!this._domFired ){  // one last try for slow DOMS.
                  this.loadHandler({type:'domready'});
              }
              this.hideMask(true);
              if(this._frameAction || this.eventsFollowFrameLinks ){
                //not going to wait for the event chain, as its not cancellable anyhow.
                this.fireEvent.defer(50,this,["documentloaded",this]);
              }
              this._frameAction = false;
              if(this.eventsFollowFrameLinks){  //reset for link tracking
                  this._domFired = this._domReady = false;
              }
              if(this._callBack){
                   this._callBack(this);
              }

              break;
            default:
        }

    }
    /* Private
      Poll the Iframes document structure to determine DOM ready state,
      and raise the 'domready' event when applicable.
    */
    ,checkDOM : function(win){
        if(Ext.isOpera)return;
        //initialise the counter
        var n = 0
            ,win = win||this.getWindow()
            ,manager = this
            ,domReady = false
            ,max = 100;

            var poll =  function(){  //DOM polling for IE and others
               try{
                 domReady  =false;
                 var doc = win.document,body;
                 if(!manager._domReady){
                    domReady = (doc && doc.getElementsByTagName);
                    domReady = domReady && (body = doc.getElementsByTagName('body')[0]) && !!body.innerHTML.length;
                 }

               }catch(ex){
                     n = max; //likely same-domain policy violation
               }

                //if the timer has reached 100 (timeout after 3 seconds)
                //in practice, shouldn't take longer than 7 iterations [in kde 3
                //in second place was IE6, which takes 2 or 3 iterations roughly 5% of the time]

                if(!manager._frameAction || manager._domReady)return;

                if(n++ < max && !domReady )
                {
                    //try again
                    setTimeout(arguments.callee, 10);
                    return;
                }
                manager.loadHandler ({type:domReady?'domready':'domfail'});

            };
            setTimeout(poll,50);
         }
 });

/* Stylesheet Frame interface object */
var styleCamelRe = /(-[a-z])/gi;
var styleCamelFn = function(m, a){ return a.charAt(1).toUpperCase(); };
var CSSInterface = function(hostDocument){
    var doc;
    if(hostDocument){

        doc = hostDocument;

    return {
        rules : null,
       /**
        * Creates a stylesheet from a text blob of rules.
        * These rules will be wrapped in a STYLE tag and appended to the HEAD of the document.
        * @param {String} cssText The text containing the css rules
        * @param {String} id An id to add to the stylesheet for later removal
        * @return {StyleSheet}
        */
       createStyleSheet : function(cssText, id){
           var ss;

           if(!doc)return;
           var head = doc.getElementsByTagName("head")[0];
           var rules = doc.createElement("style");
           rules.setAttribute("type", "text/css");
           if(id){
               rules.setAttribute("id", id);
           }
           if(Ext.isIE){
               head.appendChild(rules);
               ss = rules.styleSheet;
               ss.cssText = cssText;
           }else{
               try{
                    rules.appendChild(doc.createTextNode(cssText));
               }catch(e){
                   rules.cssText = cssText;
               }
               head.appendChild(rules);
               ss = rules.styleSheet ? rules.styleSheet : (rules.sheet || doc.styleSheets[doc.styleSheets.length-1]);
           }
           this.cacheStyleSheet(ss);
           return ss;
       },

       /**
        * Removes a style or link tag by id
        * @param {String} id The id of the tag
        */
       removeStyleSheet : function(id){

           if(!doc)return;
           var existing = doc.getElementById(id);
           if(existing){
               existing.parentNode.removeChild(existing);
           }
       },

       /**
        * Dynamically swaps an existing stylesheet reference for a new one
        * @param {String} id The id of an existing link tag to remove
        * @param {String} url The href of the new stylesheet to include
        */
       swapStyleSheet : function(id, url){
           this.removeStyleSheet(id);

           if(!doc)return;
           var ss = doc.createElement("link");
           ss.setAttribute("rel", "stylesheet");
           ss.setAttribute("type", "text/css");
           ss.setAttribute("id", id);
           ss.setAttribute("href", url);
           doc.getElementsByTagName("head")[0].appendChild(ss);
       },

       /**
        * Refresh the rule cache if you have dynamically added stylesheets
        * @return {Object} An object (hash) of rules indexed by selector
        */
       refreshCache : function(){
           return this.getRules(true);
       },

       // private
       cacheStyleSheet : function(ss){
           if(this.rules){
               this.rules = {};
           }
           try{// try catch for cross domain access issue
               var ssRules = ss.cssRules || ss.rules;
               for(var j = ssRules.length-1; j >= 0; --j){
                   this.rules[ssRules[j].selectorText] = ssRules[j];
               }
           }catch(e){}
       },

       /**
        * Gets all css rules for the document
        * @param {Boolean} refreshCache true to refresh the internal cache
        * @return {Object} An object (hash) of rules indexed by selector
        */
       getRules : function(refreshCache){
            if(this.rules == null || refreshCache){
                this.rules = {};

                if(doc){
                    var ds = doc.styleSheets;
                    for(var i =0, len = ds.length; i < len; i++){
                        try{
                            this.cacheStyleSheet(ds[i]);
                        }catch(e){}
                    }
                }
            }
            return this.rules;
        },

        /**
        * Gets an an individual CSS rule by selector(s)
        * @param {String/Array} selector The CSS selector or an array of selectors to try. The first selector that is found is returned.
        * @param {Boolean} refreshCache true to refresh the internal cache if you have recently updated any rules or added styles dynamically
        * @return {CSSRule} The CSS rule or null if one is not found
        */
       getRule : function(selector, refreshCache){
            var rs = this.getRules(refreshCache);
            if(!Ext.isArray(selector)){
                return rs[selector];
            }
            for(var i = 0; i < selector.length; i++){
                if(rs[selector[i]]){
                    return rs[selector[i]];
                }
            }
            return null;
        },

        /**
        * Updates a rule property
        * @param {String/Array} selector If it's an array it tries each selector until it finds one. Stops immediately once one is found.
        * @param {String} property The css property
        * @param {String} value The new value for the property
        * @return {Boolean} true If a rule was found and updated
        */
       updateRule : function(selector, property, value){
            if(!Ext.isArray(selector)){
                var rule = this.getRule(selector);
                if(rule){
                    rule.style[property.replace(styleCamelRe, styleCamelFn)] = value;
                    return true;
                }
            }else{
                for(var i = 0; i < selector.length; i++){
                    if(this.updateRule(selector[i], property, value)){
                        return true;
                    }
                }
            }
            return false;
        }
    };}
};

 /*
  * @class Ext.ux.ManagedIFramePanel
  * Version:  1.1  (4/13/2008)


  * Author: Doug Hendricks 12/2007 doug[always-At]theactivegroup.com
  *
  *
 */
 Ext.ux.ManagedIframePanel = Ext.extend(Ext.Panel, {

    /**
    * Cached Iframe.src url to use for refreshes. Overwritten every time setSrc() is called unless "discardUrl" param is set to true.
    * @type String/Function (which will return a string URL when invoked)
     */
    defaultSrc  :null,
    bodyStyle   :{height:'100%',width:'100%', position:'relative'},

    /**
    * @cfg {String/Object} frameStyle
    * Custom CSS styles to be applied to the ux.ManagedIframe element in the format expected by {@link Ext.Element#applyStyles}
    * (defaults to CSS Rule {overflow:'auto'}).
    */
    frameStyle  : {overflow:'auto'},
    frameConfig : null,
    hideMode    : !Ext.isIE?'nosize':'display',
    shimCls     : Ext.ux.ManagedIFrame.Manager.shimCls,
    shimUrl     : null,
    loadMask    : false,
    animCollapse: Ext.isIE,
    autoScroll  : false,
    closable    : true, /* set True by default in the event a site times-out while loadMasked */
    ctype       : "Ext.ux.ManagedIframePanel",
    showLoadIndicator : false,

    /**
    *@cfg {String/Object} unsupportedText Text (or Ext.DOMHelper config) to display within the rendered iframe tag to indicate the frame is not supported
    */
    unsupportedText : 'Inline frames are NOT enabled\/supported by your browser.'

   ,initComponent : function(){

        var unsup =this.unsupportedText?{html:this.unsupportedText}:false;
        //this.frameConfig || (this.frameConfig = {autoCreate:{}});
        this.bodyCfg ||
           (this.bodyCfg =
               {tag:'div'
               ,cls:'x-panel-body'
               ,children:[
                  {  cls    :'x-managed-iframe-mask' //shared masking DIV for hosting loadMask/dragMask
                    ,children:[
                        Ext.apply(
                          Ext.apply({
                              tag          :'iframe',
                              frameborder  : 0,
                              cls          : 'x-managed-iframe',
                              style        : this.frameStyle || null
                            },this.frameConfig)
                            ,unsup , Ext.isIE&&Ext.isSecure?{src:Ext.SSL_SECURE_URL}:false )
                            //the shimming agent
                            ,{tag:'img', src:this.shimUrl||Ext.BLANK_IMAGE_URL , cls: this.shimCls }
                         ]
                    }
                  ]
           });

         this.autoScroll = false; //Force off as the Iframe manages this
         this.items = null;

         //setup stateful events if not defined
         if(this.stateful !== false){
             this.stateEvents || (this.stateEvents = ['documentloaded']);
         }

         Ext.ux.ManagedIframePanel.superclass.initComponent.call(this);

         this.monitorResize || (this.monitorResize = this.fitToParent);

         this.addEvents({documentloaded:true, domready:true,message:true,exception:true});

         //apply the addListener patch for 'message:tagging'
         this.addListener = this.on;

    },

    doLayout   :  function(){
        //only resize (to Parent) if the panel is NOT in a layout.
        //parentNode should have {style:overflow:hidden;} applied.
        if(this.fitToParent && !this.ownerCt){
            var pos = this.getPosition(), size = (Ext.get(this.fitToParent)|| this.getEl().parent()).getViewSize();
            this.setSize(size.width - pos[0], size.height - pos[1]);
        }
        Ext.ux.ManagedIframePanel.superclass.doLayout.apply(this,arguments);

    },

      // private
    beforeDestroy : function(){

        if(this.rendered){

             if(this.tools){
                for(var k in this.tools){
                      Ext.destroy(this.tools[k]);
                }
             }

             if(this.header && this.headerAsText){
                var s;
                if( s=this.header.child('span')) s.remove();
                this.header.update('');
             }

             Ext.each(['iframe','shim','header','topToolbar','bottomToolbar','footer','loadMask','body','bwrap'],
                function(elName){
                  if(this[elName]){
                    if(typeof this[elName].destroy == 'function'){
                         this[elName].destroy();
                    } else { Ext.destroy(this[elName]); }

                    this[elName] = null;
                    delete this[elName];
                  }
             },this);
        }

        Ext.ux.ManagedIframePanel.superclass.beforeDestroy.call(this);
    },
    onDestroy : function(){
        //Yes, Panel.super (Component), since we're doing Panel cleanup beforeDestroy instead.
        Ext.Panel.superclass.onDestroy.call(this);
    },
    // private
    onRender : function(ct, position){
        Ext.ux.ManagedIframePanel.superclass.onRender.call(this, ct, position);

        if(this.iframe = this.body.child('iframe.x-managed-iframe')){

            // Set the Visibility Mode for el, bwrap for collapse/expands/hide/show
            var El = Ext.Element;
            var mode = El[this.hideMode.toUpperCase()] || 'x-hide-nosize';
            Ext.each(
                [this[this.collapseEl],this.floating? null: this.getActionEl(),this.iframe]
                ,function(el){
                     if(el)el.setVisibilityMode(mode);
            },this);

            if(this.loadMask){
                this.loadMask = Ext.apply({disabled     :false
                                          ,maskEl       :this.body
                                          ,hideOnReady  :true}
                                          ,this.loadMask);
             }

            if(this.iframe = new Ext.ux.ManagedIFrame(this.iframe, {
                    loadMask           :this.loadMask
                   ,showLoadIndicator  :this.showLoadIndicator
                   ,disableMessaging   :this.disableMessaging
                   ,style              :this.frameStyle
                   })){

                this.loadMask = this.iframe.loadMask;
                this.iframe.ownerCt = this;
                this.relayEvents(this.iframe, ["blur", "focus", "unload", "documentloaded","domready","exception","message"].concat(this._msgTagHandlers ||[]));
                delete this._msgTagHandlers;
            }

            this.getUpdater().showLoadIndicator = this.showLoadIndicator || false;

            // Enable auto-dragMask if the panel participates in (nested?) border layout.
            // Setup event handlers on the SplitBars to enable the frame dragMask when needed
            var ownerCt = this.ownerCt;
            while(ownerCt){

                ownerCt.on('afterlayout',function(container,layout){
                        var MIM = Ext.ux.ManagedIFrame.Manager,st=false;
                        Ext.each(['north','south','east','west'],function(region){
                            var reg;
                            if((reg = layout[region]) && reg.splitEl){
                                st = true;
                                if(!reg.split._splitTrapped){
                                    reg.split.on('beforeresize',MIM.showShims,MIM);
                                    reg.split._splitTrapped = true;
                                }
                            }
                        },this);
                        if(st && !this._splitTrapped ){
                            this.on('resize',MIM.hideShims,MIM);
                            this._splitTrapped = true;

                        }

                },this,{single:true}); //and discard

                ownerCt = ownerCt.ownerCt; //nested layouts?
             }


        }
        this.shim = Ext.get(this.body.child('.'+this.shimCls));
    },

    /* Toggles the built-in MIF shim */
    toggleShim   : function(){

        if(this.shim && this.shimCls)this.shim.toggleClass(this.shimCls+'-on');
    },
        // private
    afterRender : function(container){
        var html = this.html;
        delete this.html;
        Ext.ux.ManagedIframePanel.superclass.afterRender.call(this);
        if(this.iframe){
            if(this.defaultSrc){
                this.setSrc();
            }
            else if(html){
                this.iframe.update(typeof html == 'object' ? Ext.DomHelper.markup(html) : html);
            }
        }

    }
    ,sendMessage :function (){
        if(this.iframe){
            this.iframe.sendMessage.apply(this.iframe,arguments);
        }

    }
    //relay all defined 'message:tag' event handlers
    ,on : function(name){
           var tagRE=/^message\:/i, n = null;
           if(typeof name == 'object'){
               for (var na in name){
                   if(!this.filterOptRe.test(na) && tagRE.test(na)){
                      n || (n=[]);
                      n.push(na.toLowerCase());
                   }
               }
           } else if(tagRE.test(name)){
                  n=[name.toLowerCase()];
           }

           if(this.getFrame() && n){
               this.relayEvents(this.iframe,n);
           }else{
               this._msgTagHandlers || (this._msgTagHandlers =[]);
               if(n)this._msgTagHandlers = this._msgTagHandlers.concat(n); //queued for onRender when iframe is available
           }
           Ext.ux.ManagedIframePanel.superclass.on.apply(this, arguments);

    },

    /**
    * Sets the embedded Iframe src property.
    * @param {String/Function} url (Optional) A string or reference to a Function that returns a URI string when called
    * @param {Boolean} discardUrl (Optional) If not passed as <tt>false</tt> the URL of this action becomes the default URL for
    * this panel, and will be subsequently used in future setSrc calls.
    * Note:  invoke the function with no arguments to refresh the iframe based on the current defaultSrc value.
    */
    setSrc : function(url, discardUrl,callback){
         url = url || this.defaultSrc || false;

         if(!url)return this;

         if(url.url){
            callback = url.callback || false;
            discardUrl = url.discardUrl || false;
            url = url.url || false;

         }
         var src = url || (Ext.isIE&&Ext.isSecure?Ext.SSL_SECURE_URL:'');

         if(this.rendered && this.iframe){
              this.iframe.setSrc(src,discardUrl,callback);
           }

         return this;
    },

    //Make it state-aware
    getState: function(){

         var URI = this.iframe?this.iframe.getDocumentURI()||null:null;
         return Ext.apply(Ext.ux.ManagedIframePanel.superclass.getState.call(this) || {},
             URI?{defaultSrc  : typeof URI == 'function'?URI():URI}:null );

    },
    /**
     * Get the {@link Ext.Updater} for this panel's iframe/or body. Enables you to perform Ajax-based document replacement of this panel's iframe document.
     * @return {Ext.Updater} The Updater
     */
    getUpdater : function(){
        return this.rendered?(this.iframe||this.body).getUpdater():null;
    },
    /**
     * Get the embedded iframe Ext.Element for this panel
     * @return {Ext.Element} The Panels ux.ManagedIFrame instance.
     */
    getFrame : function(){
        return this.rendered?this.iframe:null
    },
    /**
     * Get the embedded iframe's window object
     * @return {Object} or Null if unavailable
     */
    getFrameWindow : function(){
        return this.rendered && this.iframe?this.iframe.getWindow():null
    },
    /**
     * Get the embedded iframe's document object
     * @return {Element} or null if unavailable
     */
    getFrameDocument : function(){
        return this.rendered && this.iframe?this.iframe.getDocument():null
    },

    /**
     * Get the embedded iframe's document as an Ext.Element.
     * @return {Ext.Element object} or null if unavailable
     */
    getFrameDoc : function(){
        return this.rendered && this.iframe?this.iframe.getDoc():null
    },

    /**
     * Get the embedded iframe's document.body Element.
     * @return {Element object} or null if unavailable
     */
    getFrameBody : function(){
        return this.rendered && this.iframe?this.iframe.getBody():null
    },
     /**
      * Loads this panel's iframe immediately with content returned from an XHR call.
      * @param {Object/String/Function} config A config object containing any of the following options:
    <pre><code>
    panel.load({
        url: "your-url.php",
        params: {param1: "foo", param2: "bar"}, // or a URL encoded string
        callback: yourFunction,
        scope: yourObject, // optional scope for the callback
        discardUrl: false,
        nocache: false,
        text: "Loading...",
        timeout: 30,
        scripts: false,
        renderer:{render:function(el, response, updater, callback){....}}  //optional custom renderer
    });
    </code></pre>
         * The only required property is url. The optional properties nocache, text and scripts
         * are shorthand for disableCaching, indicatorText and loadScripts and are used to set their
         * associated property on this panel Updater instance.
         * @return {Ext.Panel} this
         */
    load : function(loadCfg){
         var um;
         if(um = this.getUpdater()){
            if (loadCfg && loadCfg.renderer) {
                 um.setRenderer(loadCfg.renderer);
                 delete loadCfg.renderer;
            }
            um.update.apply(um, arguments);
         }
         return this;
    }
     // private
    ,doAutoLoad : function(){
        this.load(
            typeof this.autoLoad == 'object' ?
                this.autoLoad : {url: this.autoLoad});
    }

});

Ext.reg('iframepanel', Ext.ux.ManagedIframePanel);

Ext.ux.ManagedIframePortlet = Ext.extend(Ext.ux.ManagedIframePanel, {
     anchor: '100%',
     frame:true,
     collapseEl:'bwrap',
     collapsible:true,
     draggable:true,
     cls:'x-portlet'
 });
Ext.reg('iframeportlet', Ext.ux.ManagedIframePortlet);

/* override adds a third visibility feature to Ext.Element:
* Now an elements' visibility may be handled by application of a custom (hiding) CSS className.
* The class is removed to make the Element visible again
*/

Ext.apply(Ext.Element.prototype, {
  setVisible : function(visible, animate){
        if(!animate || !Ext.lib.Anim){
            if(this.visibilityMode == Ext.Element.DISPLAY){
                this.setDisplayed(visible);
            }else if(this.visibilityMode == Ext.Element.VISIBILITY){
                this.fixDisplay();
                this.dom.style.visibility = visible ? "visible" : "hidden";
            }else {
                this[visible?'removeClass':'addClass'](String(this.visibilityMode));
            }

        }else{
            // closure for composites
            var dom = this.dom;
            var visMode = this.visibilityMode;

            if(visible){
                this.setOpacity(.01);
                this.setVisible(true);
            }
            this.anim({opacity: { to: (visible?1:0) }},
                  this.preanim(arguments, 1),
                  null, .35, 'easeIn', function(){

                     if(!visible){
                         if(visMode == Ext.Element.DISPLAY){
                             dom.style.display = "none";
                         }else if(visMode == Ext.Element.VISIBILITY){
                             dom.style.visibility = "hidden";
                         }else {
                             Ext.get(dom).addClass(String(visMode));
                         }
                         Ext.get(dom).setOpacity(1);
                     }
                 });
        }
        return this;
    },
    /**
     * Checks whether the element is currently visible using both visibility and display properties.
     * @param {Boolean} deep (optional) True to walk the dom and see if parent elements are hidden (defaults to false)
     * @return {Boolean} True if the element is currently visible, else false
     */
    isVisible : function(deep) {
        var vis = !(this.getStyle("visibility") == "hidden" || this.getStyle("display") == "none" || this.hasClass(this.visibilityMode));
        if(deep !== true || !vis){
            return vis;
        }
        var p = this.dom.parentNode;
        while(p && p.tagName.toLowerCase() != "body"){
            if(!Ext.fly(p, '_isVisible').isVisible()){
                return false;
            }
            p = p.parentNode;
        }
        return true;
    }
});

Ext.onReady( function(){
  //Generate CSS Rules but allow for overrides.
    var CSS = Ext.util.CSS, rules=[];

    CSS.getRule('.x-managed-iframe') || ( rules.push('.x-managed-iframe {height:100%;width:100%;overflow:auto;}'));
    CSS.getRule('.x-managed-iframe-mask')||(rules.push('.x-managed-iframe-mask{width:100%;height:100%;position:relative;}'));
    if(!CSS.getRule('.x-frame-shim')){
        rules.push('.x-frame-shim {z-index:9000;position:absolute;top:0px;left:0px;background:transparent!important;overflow:hidden;display:none;}');
        rules.push('.x-frame-shim-on{width:100%;height:100%;display:block;zoom:1;}');
        rules.push('.ext-ie6 .x-frame-shim{margin-left:5px;margin-top:3px;}');
    }
    CSS.getRule('.x-hide-nosize') || (rules.push('.x-hide-nosize,.x-hide-nosize object,.x-hide-nosize iframe{height:0px!important;width:0px!important;border:none;}'));

    if(!!rules.length){
        CSS.createStyleSheet(rules.join(' '));
    }
});
})();

/**
 * Ext.ux.grid.livegrid.DragZone
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.DragZone is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.DragZone
 * @extends Ext.dd.DragZone
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.DragZone = function(grid, config){

    Ext.ux.grid.livegrid.DragZone.superclass.constructor.call(this, grid, config);

    this.view.ds.on('beforeselectionsload', this._onBeforeSelectionsLoad, this);
    this.view.ds.on('selectionsload',       this._onSelectionsLoad,       this);
};

Ext.extend(Ext.ux.grid.livegrid.DragZone, Ext.grid.GridDragZone, {

    /**
     * Tells whether a drop is valid. Used inetrnally to determine if pending
     * selections need to be loaded/ have been loaded.
     * @type {Boolean}
     */
    isDropValid : true,

    /**
     * Overriden for loading pending selections if needed.
     */
    onInitDrag : function(e)
    {
        this.view.ds.loadSelections(this.grid.selModel.getPendingSelections(true));

        Ext.ux.grid.livegrid.DragZone.superclass.onInitDrag.call(this, e);
    },

    /**
     * Gets called before pending selections are loaded. Any drop
     * operations are invalid/get paused if the component needs to
     * wait for selections to load from the server.
     *
     */
    _onBeforeSelectionsLoad : function()
    {
        this.isDropValid = false;
        Ext.fly(this.proxy.el.dom.firstChild).addClass('ext-ux-livegrid-drop-waiting');
    },

    /**
     * Gets called after pending selections have been loaded.
     * Any paused drop operation will be resumed.
     *
     */
    _onSelectionsLoad : function()
    {
        this.isDropValid = true;
        this.ddel.innerHTML = this.grid.getDragDropText();
        Ext.fly(this.proxy.el.dom.firstChild).removeClass('ext-ux-livegrid-drop-waiting');
    }
});
/**
 * Ext.ux.grid.livegrid.EditorGridPanel
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.EditorGridPanel is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.EditorGridPanel
 * @extends Ext.grid.EditorGridPanel
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.EditorGridPanel = Ext.extend(Ext.grid.EditorGridPanel, {

    /**
     * Overriden so the panel listens to the "cursormove" event for
     * cancelling any edit that is in progress.
     *
     * @private
     */
    initEvents : function()
    {
        Ext.ux.grid.livegrid.EditorGridPanel.superclass.initEvents.call(this);

        this.view.on("cursormove", this.stopEditing, this, [true]);
    },

    /**
     * Starts editing the specified for the specified row/column
     * Will be cancelled if the requested row index to edit is not
     * represented by data due to out of range regarding the view's
     * store buffer.
     *
     * @param {Number} rowIndex
     * @param {Number} colIndex
     */
    startEditing : function(row, col)
    {
        this.stopEditing();
        if(this.colModel.isCellEditable(col, row)){
            this.view.ensureVisible(row, col, true);
            if (!this.store.getAt(row)) {
                return;
            }
        }

        return Ext.ux.grid.livegrid.EditorGridPanel.superclass.startEditing.call(this, row, col);
    },

    /**
     * Since we do not have multiple inheritance, we need to override the
     * same methods in this class we have overriden for
     * Ext.ux.grid.livegrid.GridPanel
     *
     */
    walkCells : function(row, col, step, fn, scope)
    {
        return Ext.ux.grid.livegrid.GridPanel.prototype.walkCells.call(this, row, col, step, fn, scope);
    }

});
/**
 * Ext.ux.grid.livegrid.GridPanel
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.GridPanel is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.GridPanel
 * @extends Ext.grid.GridPanel
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.GridPanel = Ext.extend(Ext.grid.GridPanel, {

    /**
     * Overriden since the original implementation checks for
     * getCount() of the store, not getTotalCount().
     *
     */
    walkCells : function(row, col, step, fn, scope)
    {
        var ds  = this.store;
        var _oF = ds.getCount;

        ds.getCount = ds.getTotalCount;

        var ret = Ext.ux.grid.livegrid.GridPanel.superclass.walkCells.call(this, row, col, step, fn, scope);

        ds.getCount = _oF;

        return ret;
    }

});
/**
 * Ext.ux.grid.livegrid.GridView
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.GridView is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.GridView
 * @extends Ext.grid.GridView
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.GridView = function(config) {

    this.addEvents({
        /**
         * @event beforebuffer
         * Fires when the store is about to buffer new data.
         * @param {Ext.ux.BufferedGridView} this
         * @param {Ext.data.Store} store The store
         * @param {Number} rowIndex
         * @param {Number} visibleRows
         * @param {Number} totalCount
         * @param {Number} options The options with which the buffer request was called
         */
        'beforebuffer' : true,
        /**
         * @event buffer
         * Fires when the store is finsihed buffering new data.
         * @param {Ext.ux.BufferedGridView} this
         * @param {Ext.data.Store} store The store
         * @param {Number} rowIndex
         * @param {Number} visibleRows
         * @param {Number} totalCount
         * @param {Object} options
         */
        'buffer' : true,
        /**
         * @event bufferfailure
         * Fires when buffering failed.
         * @param {Ext.ux.BufferedGridView} this
         * @param {Ext.data.Store} store The store
         * @param {Object} options The options the buffer-request was initiated with
         */
        'bufferfailure' : true,
        /**
         * @event cursormove
         * Fires when the the user scrolls through the data.
         * @param {Ext.ux.BufferedGridView} this
         * @param {Number} rowIndex The index of the first visible row in the
         *                          grid absolute to it's position in the model.
         * @param {Number} visibleRows The number of rows visible in the grid.
         * @param {Number} totalCount
         */
        'cursormove' : true

    });

    /**
     * @cfg {Number} scrollDelay The number of microseconds a call to the
     * onLiveScroll-lisener should be delayed when the scroll event fires
     */

    /**
     * @cfg {Number} bufferSize The number of records that will at least always
     * be available in the store for rendering. This value will be send to the
     * server as the <tt>limit</tt> parameter and should not change during the
     * lifetime of a grid component. Note: In a paging grid, this number would
     * indicate the page size.
     * The value should be set high enough to make a userfirendly scrolling
     * possible and should be greater than the sum of {nearLimit} and
     * {visibleRows}. Usually, a value in between 150 and 200 is good enough.
     * A lesser value will more often make the store re-request new data, while
     * a larger number will make loading times higher.
     */

    /**
     * @cfg {Number} nearLimit This value represents a near value that is responsible
     * for deciding if a request for new data is needed. The lesser the number, the
     * more often new data will be requested. The number should be set to a value
     * that lies in between 1/4 to 1/2 of the {bufferSize}.
     */

    /**
     * @cfg {Number} horizontalScrollOffset The height of a horizontal aligned
     * scrollbar.  The scrollbar is shown if the total width of all visible
     * columns exceeds the width of the grid component.
     * On Windows XP (IE7, FF2), this value defaults to 17.
     */
    this.horizontalScrollOffset = 17;

    /**
     * @cfg {Object} loadMaskConfig The config of the load mask that will be shown
     * by the view if a request for new data is underway.
     */
    this.loadMask = false;

    Ext.apply(this, config);

    this.templates = {};
    /**
     * The master template adds an addiiotnal scrollbar to make cursoring in the
     * data possible.
     */
    this.templates.master = new Ext.Template(
        '<div class="x-grid3" hidefocus="true"><div class="ext-ux-livegrid-liveScroller"><div></div></div>',
            '<div class="x-grid3-viewport"">',
                '<div class="x-grid3-header"><div class="x-grid3-header-inner"><div class="x-grid3-header-offset">{header}</div></div><div class="x-clear"></div></div>',
                '<div class="x-grid3-scroller" style="overflow-y:hidden !important;"><div class="x-grid3-body">{body}</div><a href="#" class="x-grid3-focus" tabIndex="-1"></a></div>',
            "</div>",
            '<div class="x-grid3-resize-marker">&#160;</div>',
            '<div class="x-grid3-resize-proxy">&#160;</div>',
        "</div>"
    );

    Ext.ux.grid.livegrid.GridView.superclass.constructor.call(this);
};


Ext.extend(Ext.ux.grid.livegrid.GridView, Ext.grid.GridView, {

// {{{ --------------------------properties-------------------------------------

    /**
     * Used to store the z-index of the mask that is used to show while buffering,
     * so the scrollbar can be displayed above of it.
     * @type {Number} _maskIndex
     */
    _maskIndex : 20001,

    /**
     * Stores the height of the header. Needed for recalculating scroller inset height.
     * @param {Number}
     */
    hdHeight : 0,

    /**
     * Indicates wether the last row in the grid is clipped and thus not fully display.
     * 1 if clipped, otherwise 0.
     * @param {Number}
     */
    rowClipped : 0,


    /**
     * This is the actual y-scroller that does control sending request to the server
     * based upon the position of the scrolling cursor.
     * @param {Ext.Element}
     */
    liveScroller : null,

    /**
     * This is the panel that represents the amount of data in a given repository.
     * The height gets computed via the total amount of records multiplied with
     * the fixed(!) row height
     * @param {native HTMLObject}
     */
    liveScrollerInset : null,

    /**
     * The <b>fixed</b> row height for <b>every</b> row in the grid. The value is
     * computed once the store has been loaded for the first time and used for
     * various calculations during the lifetime of the grid component, such as
     * the height of the scroller and the number of visible rows.
     * @param {Number}
     */
    rowHeight : -1,

    /**
     * Stores the number of visible rows that have to be rendered.
     * @param {Number}
     */
    visibleRows : 1,

    /**
     * Stores the last offset relative to a previously scroll action. This is
     * needed for deciding wether the user scrolls up or down.
     * @param {Number}
     */
    lastIndex : -1,

    /**
     * Stores the last visible row at position "0" in the table view before
     * a new scroll event was created and fired.
     * @param {Number}
     */
    lastRowIndex : 0,

    /**
     * Stores the value of the <tt>liveScroller</tt>'s <tt>scrollTop</tt> DOM
     * property.
     * @param {Number}
     */
    lastScrollPos : 0,

    /**
     * The current index of the row in the model that is displayed as the first
     * visible row in the view.
     * @param {Number}
     */
    rowIndex : 0,

    /**
    * Set to <tt>true</tt> if the store is busy with loading new data.
    * @param {Boolean}
    */
    isBuffering : false,

	/**
	 * If a request for new data was made and the user scrolls to a new position
	 * that lays not within the requested range of the new data, the queue will
	 * hold the latest requested position. If the buffering succeeds and the value
	 * of requestQueue is not within the range of the current buffer, data may be
	 * re-requested.
	 *
	 * @param {Number}
	 */
    requestQueue : -1,

    /**
     * The view's own load mask that will be shown when a request to data was made
     * and there are no rows in the buffer left to render.
     * @see {loadMaskConfig}
     * @param {Ext.LoadMask}
     */
    loadMask : null,

    /**
     * Set to <tt>true</tt> if a request for new data has been made while there
     * are still rows in the buffer that can be rendered before the request
     * finishes.
     * @param {Boolean}
     */
    isPrebuffering : false,
// }}}

// {{{ --------------------------public API methods-----------------------------

    /**
     * Resets the view to display the first row in the data model. This will
     * change the scrollTop property of the scroller and may trigger a request
     * to buffer new data, if the row index "0" is not within the buffer range and
     * forceReload is set to true.
     *
     * @param {Boolean} forceReload <tt>true</tt> to reload the buffers contents,
     *                              othwerwise <tt>false</tt>
     *
     * @return {Boolean} Whether the store loads after reset(true); returns false
     * if any of the attached beforeload listeners cancels the load-event
     */
    reset : function(forceReload)
    {
        if (forceReload === false) {
            this.ds.modified = [];
            this.grid.selModel.clearSelections(true);
            this.rowIndex      = 0;
            this.lastScrollPos = 0;
            this.lastRowIndex = 0;
            this.lastIndex    = 0;
            this.adjustVisibleRows();
            this.adjustScrollerPos(-this.liveScroller.dom.scrollTop, true);
            this.showLoadMask(false);
            this.refresh(true);
            //this.replaceLiveRows(0, true);
            this.fireEvent('cursormove', this, 0,
                           Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
                           this.ds.totalLength);
            return false;
        } else {

            var params = {};
            var sInfo = this.ds.sortInfo;

            if (sInfo) {
                params = {
                    dir  : sInfo.direction,
                    sort : sInfo.field
                };
            }

            return this.ds.load({params : params});
        }

    },

// {{{ ------------adjusted methods for applying custom behavior----------------
    /**
     * Overwritten so the {@link Ext.ux.grid.livegrid.DragZone} can be used
     * with this view implementation.
     *
     * Since detaching a previously created DragZone from a grid panel seems to
     * be impossible, a little workaround will tell the parent implementation
     * that drad/drop is not enabled for this view's grid, and right after that
     * the custom DragZone will be created, if neccessary.
     */
    renderUI : function()
    {
        var g = this.grid;
        var dEnabled = g.enableDragDrop || g.enableDrag;

        g.enableDragDrop = false;
        g.enableDrag     = false;

        Ext.ux.grid.livegrid.GridView.superclass.renderUI.call(this);

        var g = this.grid;

        g.enableDragDrop = dEnabled;
        g.enableDrag     = dEnabled;

        if(dEnabled){
            var dd = new Ext.ux.grid.livegrid.DragZone(g, {
                ddGroup : g.ddGroup || 'GridDD'
            });
        }


	    if (this.loadMask) {

            this.loadMask = new Ext.LoadMask(
                                this.mainBody.dom.parentNode.parentNode,
                                this.loadMask
                            );

        }
    },

    /**
     * The extended implementation attaches an listener to the beforeload
     * event of the store of the grid. It is guaranteed that the listener will
     * only be executed upon reloading of the store, sorting and initial loading
     * of data. When the store does "buffer", all events are suspended and the
     * beforeload event will not be triggered.
     *
     * @param {Ext.grid.GridPanel} grid The grid panel this view is attached to
     */
    init: function(grid)
    {
        Ext.ux.grid.livegrid.GridView.superclass.init.call(this, grid);

        grid.on('expand', this._onExpand, this);
    },

    initData : function(ds, cm)
    {
        if(this.ds){
            this.ds.un('bulkremove', this.onBulkRemove, this);
            this.ds.un('beforeload', this.onBeforeLoad, this);
        }
        if(ds){
            ds.on('bulkremove', this.onBulkRemove, this);
            ds.on('beforeload', this.onBeforeLoad, this);
        }

        Ext.ux.grid.livegrid.GridView.superclass.initData.call(this, ds, cm);
    },

    /**
     * Only render the viewable rect of the table. The number of rows visible to
     * the user is defined in <tt>visibleRows</tt>.
     * This implementation does completely overwrite the parent's implementation.
     */
    // private
    renderBody : function()
    {
        var markup = this.renderRows(0, this.visibleRows-1);
        return this.templates.body.apply({rows: markup});
    },

    /**
     * Inits the DOM native elements for this component.
     * The properties <tt>liveScroller</tt> and <tt>liveScrollerInset</tt> will
     * be respected as provided by the master template.
     * The <tt>scroll</tt> listener for the <tt>liverScroller</tt> will also be
     * added here as the <tt>mousewheel</tt> listener.
     * This method overwrites the parents implementation.
     */
    // private
    initElements : function()
    {
        var E = Ext.Element;

        var el = this.grid.getGridEl().dom.firstChild;
	    var cs = el.childNodes;

	    this.el = new E(el);

        this.mainWrap = new E(cs[1]);

        // liveScroller and liveScrollerInset
        this.liveScroller       = new E(cs[0]);
        this.liveScroller.setStyle('zIndex', 2);
        this.liveScrollerInset  = this.liveScroller.dom.firstChild;
        this.liveScroller.on('scroll', this.onLiveScroll,  this, {buffer : this.scrollDelay});

        var thd = this.mainWrap.dom.firstChild;
	    this.mainHd = new E(thd);

	    this.hdHeight = thd.offsetHeight;

	    this.innerHd = this.mainHd.dom.firstChild;
        this.scroller = new E(this.mainWrap.dom.childNodes[1]);
        if(this.forceFit){
            this.scroller.setStyle('overflow-x', 'hidden');
        }
        this.mainBody = new E(this.scroller.dom.firstChild);

        // addd the mousewheel event to the table's body
        this.mainBody.on('mousewheel', this.handleWheel,  this);

	    this.focusEl = new E(this.scroller.dom.childNodes[1]);
        this.focusEl.swallowEvent("click", true);

        this.resizeMarker = new E(cs[2]);
        this.resizeProxy = new E(cs[3]);

    },

	/**
	 * Layouts the grid's view taking the scroller into account. The height
	 * of the scroller gets adjusted depending on the total width of the columns.
	 * The width of the grid view will be adjusted so the header and the rows do
	 * not overlap the scroller.
	 * This method will also compute the row-height based on the first row this
	 * grid displays and will adjust the number of visible rows if a resize
	 * of the grid component happened.
	 * This method overwrites the parents implementation.
	 */
	//private
    layout : function()
    {
        if(!this.mainBody){
            return; // not rendered
        }
        var g = this.grid;
        var c = g.getGridEl(), cm = this.cm,
                expandCol = g.autoExpandColumn,
                gv = this;

        var csize = c.getSize(true);

        // set vw to 19 to take scrollbar width into account!
        var vw = csize.width;

        if(vw < 20 || csize.height < 20){ // display: none?
            return;
        }

        if(g.autoHeight){
            this.scroller.dom.style.overflow = 'visible';
        }else{
            this.el.setSize(csize.width, csize.height);

            var hdHeight = this.mainHd.getHeight();
            var vh = csize.height - (hdHeight);

            this.scroller.setSize(vw, vh);
            if(this.innerHd){
                this.innerHd.style.width = (vw)+'px';
            }
        }

        this.liveScroller.dom.style.top = this.hdHeight+"px";

        if(this.forceFit){
            if(this.lastViewWidth != vw){
                this.fitColumns(false, false);
                this.lastViewWidth = vw;
            }
        }else {
            this.autoExpand();
        }

        // adjust the number of visible rows and the height of the scroller.
        this.adjustVisibleRows();
        this.adjustBufferInset();

        this.onLayout(vw, vh);
    },

    /**
     * Overriden for Ext 2.2 to prevent call to focus Row.
     *
     */
    removeRow : function(row)
    {
        Ext.removeNode(this.getRow(row));
    },

    /**
     * Overriden for Ext 2.2 to prevent call to focus Row.
     * This method i s here for dom operations only - the passed arguments are the
     * index of the nodes in the dom, not in the model.
     *
     */
    removeRows : function(firstRow, lastRow)
    {
        var bd = this.mainBody.dom;
        for(var rowIndex = firstRow; rowIndex <= lastRow; rowIndex++){
            Ext.removeNode(bd.childNodes[firstRow]);
        }
    },

// {{{ ----------------------dom/mouse listeners--------------------------------

    /**
     * Tells the view to recalculate the number of rows displayable
     * and the buffer inset, when it gets expanded after it has been
     * collapsed.
     *
     */
    _onExpand : function(panel)
    {
        this.adjustVisibleRows();
        this.adjustBufferInset();
        this.adjustScrollerPos(this.rowHeight*this.rowIndex, true);
    },

    // private
    onColumnMove : function(cm, oldIndex, newIndex)
    {
        this.indexMap = null;
        this.replaceLiveRows(this.rowIndex, true);
        this.updateHeaders();
        this.updateHeaderSortState();
        this.afterMove(newIndex);
    },


    /**
     * Called when a column width has been updated. Adjusts the scroller height
     * and the number of visible rows wether the horizontal scrollbar is shown
     * or not.
     */
    onColumnWidthUpdated : function(col, w, tw)
    {
        this.adjustVisibleRows();
        this.adjustBufferInset();
    },

    /**
     * Called when the width of all columns has been updated. Adjusts the scroller
     * height and the number of visible rows wether the horizontal scrollbar is shown
     * or not.
     */
    onAllColumnWidthsUpdated : function(ws, tw)
    {
        this.adjustVisibleRows();
        this.adjustBufferInset();
    },

    /**
     * Callback for selecting a row. The index of the row is the absolute index
     * in the datamodel. If the row is not rendered, this method will do nothing.
     */
    // private
    onRowSelect : function(row)
    {
        if (row < this.rowIndex || row > this.rowIndex+this.visibleRows) {
            return;
        }

        this.addRowClass(row, "x-grid3-row-selected");
    },

    /**
     * Callback for deselecting a row. The index of the row is the absolute index
     * in the datamodel. If the row is not currently rendered in the view, this method
     * will do nothing.
     */
    // private
    onRowDeselect : function(row)
    {
        if (row < this.rowIndex || row > this.rowIndex+this.visibleRows) {
            return;
        }

        this.removeRowClass(row, "x-grid3-row-selected");
    },


// {{{ ----------------------data listeners-------------------------------------
    /**
     * Called when the buffer gets cleared. Simply calls the updateLiveRows method
     * with the adjusted index and should force the store to reload
     */
    // private
    onClear : function()
    {
        this.reset(false);
    },

    /**
     * Callback for the "bulkremove" event of the attached datastore.
     *
     * @param {Ext.ux.grid.livegrid.Store} store
     * @param {Array} removedData
     *
     */
    onBulkRemove : function(store, removedData)
    {
        var record    = null;
        var index     = 0;
        var viewIndex = 0;
        var len       = removedData.length;

        var removedInView    = false;
        var removedAfterView = false;
        var scrollerAdjust   = 0;

        if (len == 0) {
            return;
        }

        var tmpRowIndex   = this.rowIndex;
        var removedBefore = 0;
        var removedAfter  = 0;
        var removedIn     = 0;

        for (var i = 0; i < len; i++) {
            record = removedData[i][0];
            index  = removedData[i][1];

            viewIndex = (index != Number.MIN_VALUE && index != Number.MAX_VALUE)
                      ? index + this.ds.bufferRange[0]
                      : index;

            if (viewIndex < this.rowIndex) {
                removedBefore++;
            } else if (viewIndex >= this.rowIndex && viewIndex <= this.rowIndex+(this.visibleRows-1)) {
                removedIn++;
            } else if (viewIndex >= this.rowIndex+this.visibleRows) {
                removedAfter++;
            }

            this.fireEvent("beforerowremoved", this, viewIndex, record);
            this.fireEvent("rowremoved",       this, viewIndex, record);
        }

        var totalLength = this.ds.totalLength;
        this.rowIndex   = Math.max(0, Math.min(this.rowIndex - removedBefore, totalLength-(this.visibleRows-1)));

        this.lastRowIndex = this.rowIndex;

        this.adjustScrollerPos(-(removedBefore*this.rowHeight), true);
        this.updateLiveRows(this.rowIndex, true);
        this.adjustBufferInset();
        this.processRows(0, undefined, false);

    },


    /**
     * Callback for the underlying store's remove method. The current
     * implementation does only remove the selected row which record is in the
     * current store.
     *
     * @see onBulkRemove()
     */
    // private
    onRemove : function(ds, record, index)
    {
        this.onBulkRemove(ds, [[record, index]]);
    },

    /**
     * The callback for the underlying data store when new data was added.
     * If <tt>index</tt> equals to <tt>Number.MIN_VALUE</tt> or <tt>Number.MAX_VALUE</tt>, the
     * method can't tell at which position in the underlying data model the
     * records where added. However, if <tt>index</tt> equals to <tt>Number.MIN_VALUE</tt>,
     * the <tt>rowIndex</tt> property will be adjusted to <tt>rowIndex+records.length</tt>,
     * and the <tt>liveScroller</tt>'s properties get adjusted so it matches the
     * new total number of records of the underlying data model.
     * The same will happen to any records that get added at the store index which
     * is currently represented by the first visible row in the view.
     * Any other value will cause the method to compute the number of rows that
     * have to be (re-)painted and calling the <tt>insertRows</tt> method, if
     * neccessary.
     *
     * This method triggers the <tt>beforerowsinserted</tt> and <tt>rowsinserted</tt>
     * event, passing the indexes of the records as they may default to the
     * positions in the underlying data model. However, due to the fact that
     * any sort algorithm may have computed the indexes of the records, it is
     * not guaranteed that the computed indexes equal to the indexes of the
     * underlying data model.
     *
     * @param {Ext.ux.grid.livegrid.Store} ds The datastore that buffers records
     *                                       from the underlying data model
     * @param {Array} records An array containing the newly added
     *                        {@link Ext.data.Record}s
     * @param {Number} index The index of the position in the underlying
     *                       {@link Ext.ux.grid.livegrid.Store} where the rows
     *                       were added.
     */
    // private
    onAdd : function(ds, records, index)
    {
        var recordLen = records.length;

        // values of index which equal to Number.MIN_VALUE or Number.MAX_VALUE
        // indicate that the records were not added to the store. The component
        // does not know which index those records do have in the underlying
        // data model
        if (index == Number.MAX_VALUE || index == Number.MIN_VALUE) {
            this.fireEvent("beforerowsinserted", this, index, index);

            // if index equals to Number.MIN_VALUE, shift rows!
            if (index == Number.MIN_VALUE) {

                this.rowIndex     = this.rowIndex + recordLen;
                this.lastRowIndex = this.rowIndex;

                this.adjustBufferInset();
                this.adjustScrollerPos(this.rowHeight*recordLen, true);

                this.fireEvent("rowsinserted", this, index, index, recordLen);
                this.processRows(0, undefined, false);
                // the cursor did virtually move
                this.fireEvent('cursormove', this, this.rowIndex,
                               Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
                               this.ds.totalLength);

                return;
            }

            this.adjustBufferInset();
            this.fireEvent("rowsinserted", this, index, index, recordLen);
            return;
        }

        // only insert the rows which affect the current view.
        var start = index+this.ds.bufferRange[0];
        var end   = start + (recordLen-1);
        var len   = this.getRows().length;

        var firstRow = 0;
        var lastRow  = 0;

        // rows would be added at the end of the rows which are currently
        // displayed, so fire the event, resize buffer and adjust visible
        // rows and return
        if (start > this.rowIndex+(this.visibleRows-1)) {
            this.fireEvent("beforerowsinserted", this, start, end);
            this.fireEvent("rowsinserted",       this, start, end, recordLen);

            this.adjustVisibleRows();
            this.adjustBufferInset();

        }

        // rows get added somewhere in the current view.
        else if (start >= this.rowIndex && start <= this.rowIndex+(this.visibleRows-1)) {
            firstRow = index;
            // compute the last row that would be affected of an insert operation
            lastRow  = index+(recordLen-1);
            this.lastRowIndex  = this.rowIndex;
            this.rowIndex      = (start > this.rowIndex) ? this.rowIndex : start;

            this.insertRows(ds, firstRow, lastRow);

            if (this.lastRowIndex != this.rowIndex) {
                this.fireEvent('cursormove', this, this.rowIndex,
                               Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
                               this.ds.totalLength);
            }

            this.adjustVisibleRows();
            this.adjustBufferInset();
        }

        // rows get added before the first visible row, which would not affect any
        // rows to be re-rendered
        else if (start < this.rowIndex) {
            this.fireEvent("beforerowsinserted", this, start, end);

            this.rowIndex     = this.rowIndex+recordLen;
            this.lastRowIndex = this.rowIndex;

            this.adjustVisibleRows();
            this.adjustBufferInset();

            this.adjustScrollerPos(this.rowHeight*recordLen, true);

            this.fireEvent("rowsinserted", this, start, end, recordLen);
            this.processRows(0, undefined, true);

            this.fireEvent('cursormove', this, this.rowIndex,
                           Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
                           this.ds.totalLength);
        }




    },

// {{{ ----------------------store listeners------------------------------------
    /**
     * This callback for the store's "beforeload" event will adjust the start
     * position and the limit of the data in the model to fetch. It is guaranteed
     * that this method will only be called when the store initially loads,
     * remeote-sorts or reloads.
     * All other load events will be suspended when the view requests buffer data.
     * See {updateLiveRows}.
     *
     * @param {Ext.data.Store} store The store the Grid Panel uses
     * @param {Object} options The configuration object for the proxy that loads
     *                         data from the server
     */
    onBeforeLoad : function(store, options)
    {
        options.params = options.params || {};

        var apply = Ext.apply;

        apply(options, {
            scope    : this,
            callback : function(){
                this.reset(false);
            }
        });

        apply(options.params, {
            start    : 0,
            limit    : this.ds.bufferSize
        });

        return true;
    },

    /**
     * Method is used as a callback for the load-event of the attached data store.
     * Adjusts the buffer inset based upon the <tt>totalCount</tt> property
     * returned by the response.
     * Overwrites the parent's implementation.
     */
    onLoad : function(o1, o2, options)
    {
        this.adjustBufferInset();
    },

    /**
     * This will be called when the data in the store has changed, i.e. a
     * re-buffer has occured. If the table was not rendered yet, a call to
     * <tt>refresh</tt> will initially render the table, which DOM elements will
     * then be used to re-render the table upon scrolling.
     *
     */
    // private
    onDataChange : function(store)
    {
        this.updateHeaderSortState();
    },

    /**
     * A callback for the store when new data has been buffered successfully.
     * If the current row index is not within the range of the newly created
     * data buffer or another request to new data has been made while the store
     * was loading, new data will be re-requested.
     *
     * Additionally, if there are any rows that have been selected which were not
     * in the data store, the method will request the pending selections from
     * the grid's selection model and add them to the selections if available.
     * This is because the component assumes that a user who scrolls through the
     * rows and updates the view's buffer during scrolling, can check the selected
     * rows which come into the view for integrity. It is up to the user to
     * deselect those rows not matchuing the selection.
     * Additionally, if the version of the store changes during various requests
     * and selections are still pending, the versionchange event of the store
     * can delete the pending selections after a re-bufer happened and before this
     * method was called.
     *
     */
    // private
    liveBufferUpdate : function(records, options, success)
    {
        if (success === true) {
            this.fireEvent('buffer', this, this.ds, this.rowIndex,
                Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
                this.ds.totalLength,
                options
            );

            this.isBuffering    = false;
            this.isPrebuffering = false;
            this.showLoadMask(false);

            // this is needed since references to records which have been unloaded
            // get lost when the store gets loaded with new data.
            // from the store
            this.grid.selModel.replaceSelections(records);


            if (this.isInRange(this.rowIndex)) {
                this.replaceLiveRows(this.rowIndex, options.forceRepaint);
            } else {
                this.updateLiveRows(this.rowIndex);
            }

            if (this.requestQueue >= 0) {
                var offset = this.requestQueue;
                this.requestQueue = -1;
                this.updateLiveRows(offset);
            }

            return;
        } else {
            this.fireEvent('bufferfailure', this, this.ds, options);
        }

        this.requestQueue   = -1;
        this.isBuffering    = false;
        this.isPrebuffering = false;
        this.showLoadMask(false);
    },


// {{{ ----------------------scroll listeners------------------------------------
    /**
     * Handles mousewheel event on the table's body. This is neccessary since the
     * <tt>liveScroller</tt> element is completely detached from the table's body.
     *
     * @param {Ext.EventObject} e The event object
     */
    handleWheel : function(e)
    {
        if (this.rowHeight == -1) {
            e.stopEvent();
            return;
        }
        var d = e.getWheelDelta();

        this.adjustScrollerPos(-(d*this.rowHeight));

        e.stopEvent();
    },

    /**
     * Handles scrolling through the grid. Since the grid is fixed and rows get
     * removed/ added subsequently, the only way to determine the actual row in
     * view is to measure the <tt>scrollTop</tt> property of the <tt>liveScroller</tt>'s
     * DOM element.
     *
     */
    onLiveScroll : function()
    {
        var scrollTop = this.liveScroller.dom.scrollTop;

        var cursor = Math.floor((scrollTop)/this.rowHeight);

        this.rowIndex = cursor;
        // the lastRowIndex will be set when refreshing the view has finished
        if (cursor == this.lastRowIndex) {
            return;
        }

        this.updateLiveRows(cursor);

        this.lastScrollPos = this.liveScroller.dom.scrollTop;
    },



// {{{ --------------------------helpers----------------------------------------

    // private
    refreshRow : function(record)
    {
        var ds = this.ds, index;
        if(typeof record == 'number'){
            index = record;
            record = ds.getAt(index);
        }else{
            index = ds.indexOf(record);
        }

        var viewIndex = index + this.ds.bufferRange[0];

        if (viewIndex < this.rowIndex || viewIndex >= this.rowIndex + this.visibleRows) {
            this.fireEvent("rowupdated", this, viewIndex, record);
            return;
        }

        this.insertRows(ds, index, index, true);
        this.fireEvent("rowupdated", this, viewIndex, record);
    },

    /**
     * Overwritten so the rowIndex can be changed to the absolute index.
     *
     * If the third parameter equals to <tt>true</tt>, the method will also
     * repaint the selections.
     */
    // private
    processRows : function(startRow, skipStripe, paintSelections)
    {
        skipStripe = skipStripe || !this.grid.stripeRows;
        // we will always process all rows in the view
        startRow = 0;
        var rows = this.getRows();
        var cls = ' x-grid3-row-alt ';
        var cursor = this.rowIndex;

        var index      = 0;
        var selections = this.grid.selModel.selections;
        var ds         = this.ds;
        var row        = null;
        for(var i = startRow, len = rows.length; i < len; i++){
            index = i+cursor;
            row   = rows[i];
            // changed!
            row.rowIndex = index;

            if (paintSelections !== false) {
                if (this.grid.selModel.isSelected(this.ds.getAt(index)) === true) {
                    this.addRowClass(index, "x-grid3-row-selected");
                } else {
                    this.removeRowClass(index, "x-grid3-row-selected");
                }
                this.fly(row).removeClass("x-grid3-row-over");
            }

            if(!skipStripe){
                var isAlt = ((index+1) % 2 == 0);
                var hasAlt = (' '+row.className + ' ').indexOf(cls) != -1;
                if(isAlt == hasAlt){
                    continue;
                }
                if(isAlt){
                    row.className += " x-grid3-row-alt";
                }else{
                    row.className = row.className.replace("x-grid3-row-alt", "");
                }
            }
        }
    },

    /**
     * API only, since the passed arguments are the indexes in the buffer store.
     * However, the method will try to compute the indexes so they might match
     * the indexes of the records in the underlying data model.
     *
     */
    // private
    insertRows : function(dm, firstRow, lastRow, isUpdate)
    {
        var viewIndexFirst = firstRow + this.ds.bufferRange[0];
        var viewIndexLast  = lastRow  + this.ds.bufferRange[0];

        if (!isUpdate) {
            this.fireEvent("beforerowsinserted", this, viewIndexFirst, viewIndexLast);
        }

        // first off, remove the rows at the bottom of the view to match the
        // visibleRows value and to not cause any spill in the DOM
        if (isUpdate !== true && (this.getRows().length + (lastRow-firstRow)) >= this.visibleRows) {
            this.removeRows((this.visibleRows-1)-(lastRow-firstRow), this.visibleRows-1);
        } else if (isUpdate) {
            this.removeRows(viewIndexFirst-this.rowIndex, viewIndexLast-this.rowIndex);
        }

        // compute the range of possible records which could be drawn into the view without
        // causing any spill
        var lastRenderRow = (firstRow == lastRow)
                          ? lastRow
                          : Math.min(lastRow,  (this.rowIndex-this.ds.bufferRange[0])+(this.visibleRows-1));

        var html = this.renderRows(firstRow, lastRenderRow);

        var before = this.getRow(viewIndexFirst);

        if (before) {
            Ext.DomHelper.insertHtml('beforeBegin', before, html);
        } else {
            Ext.DomHelper.insertHtml('beforeEnd', this.mainBody.dom, html);
        }

        // if a row is replaced, we need to set the row index for this
        // row
        if (isUpdate === true) {
            var rows   = this.getRows();
            var cursor = this.rowIndex;
            for (var i = 0, max_i = rows.length; i < max_i; i++) {
                rows[i].rowIndex = cursor+i;
            }
        }

        if (!isUpdate) {
            this.fireEvent("rowsinserted", this, viewIndexFirst, viewIndexLast, (viewIndexLast-viewIndexFirst)+1);
            this.processRows(0, undefined, true);
        }
    },

    /**
     * Return the <TR> HtmlElement which represents a Grid row for the specified index.
     * The passed argument is assumed to be the absolute index and will get translated
     * to the index of the row that represents the data in the view.
     *
     * @param {Number} index The row index
     *
     * @return {null|HtmlElement} The <TR> element, or null if the row is not rendered
     * in the view.
     */
    getRow : function(row)
    {
        if (row-this.rowIndex < 0) {
            return null;
        }

        return this.getRows()[row-this.rowIndex];
    },

    /**
     * Returns the grid's <TD> HtmlElement at the specified coordinates.
     * Returns null if the specified row is not currently rendered.
     *
     * @param {Number} row The row index in which to find the cell.
     * @param {Number} col The column index of the cell.
     * @return {HtmlElement} The &lt;TD> at the specified coordinates.
     */
    getCell : function(row, col)
    {
        var row = this.getRow(row);

        return row
               ? row.getElementsByTagName('td')[col]
               : null;
    },

    /**
     * Focuses the specified cell.
     * @param {Number} row The row index
     * @param {Number} col The column index
     */
    focusCell : function(row, col, hscroll)
    {
        var xy = this.ensureVisible(row, col, hscroll);

        if (!xy) {
        	return;
		}

		this.focusEl.setXY(xy);

        if(Ext.isGecko){
            this.focusEl.focus();
        }else{
            this.focusEl.focus.defer(1, this.focusEl);
        }

    },

    /**
     * Makes sure that the requested /row/col is visible in the viewport.
     * The method may invoke a request for new buffer data and triggers the
     * scroll-event of the <tt>liveScroller</tt> element.
     *
     */
    // private
    ensureVisible : function(row, col, hscroll)
    {
        if(typeof row != "number"){
            row = row.rowIndex;
        }

        if(row < 0 || row >= this.ds.totalLength){
            return;
        }

        col = (col !== undefined ? col : 0);

        var rowInd = row-this.rowIndex;

        if (this.rowClipped && row == this.rowIndex+this.visibleRows-1) {
            this.adjustScrollerPos(this.rowHeight );
        } else if (row >= this.rowIndex+this.visibleRows) {
            this.adjustScrollerPos(((row-(this.rowIndex+this.visibleRows))+1)*this.rowHeight);
        } else if (row <= this.rowIndex) {
            this.adjustScrollerPos((rowInd)*this.rowHeight);
        }

        var rowEl = this.getRow(row), cellEl;

        if(!rowEl){
            return;
        }

        if(!(hscroll === false && col === 0)){
            while(this.cm.isHidden(col)){
                col++;
            }
            cellEl = this.getCell(row, col);
        }

        var c = this.scroller.dom;

        if(hscroll !== false){
            var cleft = parseInt(cellEl.offsetLeft, 10);
            var cright = cleft + cellEl.offsetWidth;

            var sleft = parseInt(c.scrollLeft, 10);
            var sright = sleft + c.clientWidth;
            if(cleft < sleft){
                c.scrollLeft = cleft;
            }else if(cright > sright){
                c.scrollLeft = cright-c.clientWidth;
            }
        }


        return cellEl ?
            Ext.fly(cellEl).getXY() :
            [c.scrollLeft+this.el.getX(), Ext.fly(rowEl).getY()];
    },

    /**
     * Return strue if the passed record is in the visible rect of this view.
     *
     * @param {Ext.data.Record} record
     *
     * @return {Boolean} true if the record is rendered in the view, otherwise false.
     */
    isRecordRendered : function(record)
    {
        var ind = this.ds.indexOf(record);

        if (ind >= this.rowIndex && ind < this.rowIndex+this.visibleRows) {
            return true;
        }

        return false;
    },

    /**
     * Checks if the passed argument <tt>cursor</tt> lays within a renderable
     * area. The area is renderable, if the sum of cursor and the visibleRows
     * property does not exceed the current upper buffer limit.
     *
     * If this method returns <tt>true</tt>, it's basically save to re-render
     * the view with <tt>cursor</tt> as the absolute position in the model
     * as the first visible row.
     *
     * @param {Number} cursor The absolute position of the row in the data model.
     *
     * @return {Boolean} <tt>true</tt>, if the row can be rendered, otherwise
     *                   <tt>false</tt>
     *
     */
    isInRange : function(rowIndex)
    {
        var lastRowIndex = Math.min(this.ds.totalLength-1,
                                    rowIndex + this.visibleRows);

        return (rowIndex     >= this.ds.bufferRange[0]) &&
               (lastRowIndex <= this.ds.bufferRange[1]);
    },

    /**
     * Calculates the bufferRange start index for a buffer request
     *
     * @param {Boolean} inRange If the index is within the current buffer range
     * @param {Number} index The index to use as a reference for the calculations
     * @param {Boolean} down Wether the calculation was requested when the user scrolls down
     */
    getPredictedBufferIndex : function(index, inRange, down)
    {
        if (!inRange) {
            var dNear = 2*this.nearLimit;
            return Math.max(0, index-((dNear >= this.ds.bufferSize ? this.nearLimit : dNear)));
        }
        if (!down) {
            return Math.max(0, (index-this.ds.bufferSize)+this.visibleRows);
        }

        if (down) {
            return Math.max(0, Math.min(index, this.ds.totalLength-this.ds.bufferSize));
        }
    },


    /**
     * Updates the table view. Removes/appends rows as needed and fetches the
     * cells content out of the available store. If the needed rows are not within
     * the buffer, the method will advise the store to update it's contents.
     *
     * The method puts the requested cursor into the queue if a previously called
     * buffering is in process.
     *
     * @param {Number} cursor The row's position, absolute to it's position in the
     *                        data model
     *
     */
    updateLiveRows: function(index, forceRepaint, forceReload)
    {
        var inRange = this.isInRange(index);

        if (this.isBuffering) {
            if (this.isPrebuffering) {
                if (inRange) {
                    this.replaceLiveRows(index);
                } else {
                    this.showLoadMask(true);
                }
            }

            this.fireEvent('cursormove', this, index,
                           Math.min(this.ds.totalLength,
                           this.visibleRows-this.rowClipped),
                           this.ds.totalLength);

            this.requestQueue = index;
            return;
        }

        var lastIndex  = this.lastIndex;
        this.lastIndex = index;
        var inRange    = this.isInRange(index);

        var down = false;

        if (inRange && forceReload !== true) {

            // repaint the table's view
            this.replaceLiveRows(index, forceRepaint);
            // has to be called AFTER the rowIndex was recalculated
            this.fireEvent('cursormove', this, index,
                       Math.min(this.ds.totalLength,
                       this.visibleRows-this.rowClipped),
                       this.ds.totalLength);
            // lets decide if we can void this method or stay in here for
            // requesting a buffer update
            if (index > lastIndex) { // scrolling down

                down = true;
                var totalCount = this.ds.totalLength;

                // while scrolling, we have not yet reached the row index
                // that would trigger a re-buffer
                if (index+this.visibleRows+this.nearLimit <= this.ds.bufferRange[1]) {
                    return;
                }

                // If we have already buffered the last range we can ever get
                // by the queried data repository, we don't need to buffer again.
                // This basically means that a re-buffer would only occur again
                // if we are scrolling up.
                if (this.ds.bufferRange[1]+1 >= totalCount) {
                    return;
                }
            } else if (index < lastIndex) { // scrolling up

                down = false;
                // We are scrolling up in the first buffer range we can ever get
                // Re-buffering would only occur upon scrolling down.
                if (this.ds.bufferRange[0] <= 0) {
                    return;
                }

                // if we are scrolling up and we are moving in an acceptable
                // buffer range, lets return.
                if (index - this.nearLimit > this.ds.bufferRange[0]) {
                    return;
                }
            } else {
                return;
            }

            this.isPrebuffering = true;
        }

        // prepare for rebuffering
        this.isBuffering = true;

        var bufferOffset = this.getPredictedBufferIndex(index, inRange, down);

        if (!inRange) {
            this.showLoadMask(true);
        }

        this.ds.suspendEvents();
        var sInfo  = this.ds.sortInfo;

        var params = {};
        if (this.ds.lastOptions) {
            Ext.apply(params, this.ds.lastOptions.params);
        }

        params.start = bufferOffset;
        params.limit = this.ds.bufferSize;

        if (sInfo) {
            params.dir  = sInfo.direction;
            params.sort = sInfo.field;
        }

        var opts = {
            forceRepaint : forceRepaint,
            callback     : this.liveBufferUpdate,
            scope        : this,
            params       : params
        };

        this.fireEvent('beforebuffer', this, this.ds, index,
            Math.min(this.ds.totalLength, this.visibleRows-this.rowClipped),
            this.ds.totalLength, opts
        );

        this.ds.load(opts);
        this.ds.resumeEvents();
    },

    /**
     * Shows this' view own load mask to indicate that a large amount of buffer
     * data was requested by the store.
     * @param {Boolean} show <tt>true</tt> to show the load mask, otherwise
     *                       <tt>false</tt>
     */
    showLoadMask : function(show)
    {
        if (this.loadMask == null) {
            if (show) {
                this.loadMask = new Ext.LoadMask(
                    this.mainBody.dom.parentNode.parentNode,
                    this.loadMaskConfig
                );
            } else {
                return;
            }
        }

        if (show) {
            this.loadMask.show();
            this.liveScroller.setStyle('zIndex', this._maskIndex);
        } else {
            this.loadMask.hide();
            this.liveScroller.setStyle('zIndex', 2);
        }
    },

    /**
     * Renders the table body with the contents of the model. The method will
     * prepend/ append rows after removing from either the end or the beginning
     * of the table DOM to reduce expensive DOM calls.
     * It will also take care of rendering the rows selected, taking the property
     * <tt>bufferedSelections</tt> of the {@link BufferedRowSelectionModel} into
     * account.
     * Instead of calling this method directly, the <tt>updateLiveRows</tt> method
     * should be called which takes care of rebuffering if needed, since this method
     * will behave erroneous if data of the buffer is requested which may not be
     * available.
     *
     * @param {Number} cursor The position of the data in the model to start
     *                        rendering.
     *
     * @param {Boolean} forceReplace <tt>true</tt> for recomputing the DOM in the
     *                               view, otherwise <tt>false</tt>.
     */
    // private
    replaceLiveRows : function(cursor, forceReplace, processRows)
    {
        var spill = cursor-this.lastRowIndex;

        if (spill == 0 && forceReplace !== true) {
            return;
        }

        // decide wether to prepend or append rows
        // if spill is negative, we are scrolling up. Thus we have to prepend
        // rows. If spill is positive, we have to append the buffers data.
        var append = spill > 0;

        // abs spill for simplyfiying append/prepend calculations
        spill = Math.abs(spill);

        // adjust cursor to the buffered model index
        var bufferRange = this.ds.bufferRange;
        var cursorBuffer = cursor-bufferRange[0];

        // compute the last possible renderindex
        var lpIndex = Math.min(cursorBuffer+this.visibleRows-1, bufferRange[1]-bufferRange[0]);
        // we can skip checking for append or prepend if the spill is larger than
        // visibleRows. We can paint the whole rows new then-
        if (spill >= this.visibleRows || spill == 0) {
            this.mainBody.update(this.renderRows(cursorBuffer, lpIndex));
        } else {
            if (append) {

                this.removeRows(0, spill-1);

                if (cursorBuffer+this.visibleRows-spill <= bufferRange[1]-bufferRange[0]) {
                    var html = this.renderRows(
                        cursorBuffer+this.visibleRows-spill,
                        lpIndex
                    );
                    Ext.DomHelper.insertHtml('beforeEnd', this.mainBody.dom, html);

                }

            } else {
                this.removeRows(this.visibleRows-spill, this.visibleRows-1);
                var html = this.renderRows(cursorBuffer, cursorBuffer+spill-1);
                Ext.DomHelper.insertHtml('beforeBegin', this.mainBody.dom.firstChild, html);

            }
        }

        if (processRows !== false) {
            this.processRows(0, undefined, true);
        }
        this.lastRowIndex = cursor;
    },



    /**
    * Adjusts the scroller height to make sure each row in the dataset will be
    * can be displayed, no matter which value the current height of the grid
    * component equals to.
    */
    // protected
    adjustBufferInset : function()
    {
        var liveScrollerDom = this.liveScroller.dom;
        var g = this.grid, ds = g.store;
        var c  = g.getGridEl();
        var elWidth = c.getSize().width;

        // hidden rows is the number of rows which cannot be
        // displayed and for which a scrollbar needs to be
        // rendered. This does also take clipped rows into account
        var hiddenRows = (ds.totalLength == this.visibleRows-this.rowClipped)
                       ? 0
                       : Math.max(0, ds.totalLength-(this.visibleRows-this.rowClipped));

        if (hiddenRows == 0) {
            this.scroller.setWidth(elWidth);
            liveScrollerDom.style.display = 'none';
            return;
        } else {
            this.scroller.setWidth(elWidth-this.scrollOffset);
            liveScrollerDom.style.display = '';
        }

        var scrollbar = this.cm.getTotalWidth()+this.scrollOffset > elWidth;

        // adjust the height of the scrollbar
        var contHeight = liveScrollerDom.parentNode.offsetHeight +
                         ((ds.totalLength > 0 && scrollbar)
                         ? - this.horizontalScrollOffset
                         : 0)
                         - this.hdHeight;

        liveScrollerDom.style.height = Math.max(contHeight, this.horizontalScrollOffset*2)+"px";

        if (this.rowHeight == -1) {
            return;
        }

        this.liveScrollerInset.style.height = (hiddenRows == 0 ? 0 : contHeight+(hiddenRows*this.rowHeight))+"px";
    },

    /**
     * Recomputes the number of visible rows in the table based upon the height
     * of the component. The method adjusts the <tt>rowIndex</tt> property as
     * needed, if the sum of visible rows and the current row index exceeds the
     * number of total data available.
     */
    // protected
    adjustVisibleRows : function()
    {
        if (this.rowHeight == -1) {
            if (this.getRows()[0]) {
                this.rowHeight = this.getRows()[0].offsetHeight;

                if (this.rowHeight <= 0) {
                    this.rowHeight = -1;
                    return;
                }

            } else {
                return;
            }
        }


        var g = this.grid, ds = g.store;

        var c     = g.getGridEl();
        var cm    = this.cm;
        var size  = c.getSize();
        var width = size.width;
        var vh    = size.height;

        var vw = width-this.scrollOffset;
        // horizontal scrollbar shown?
        if (cm.getTotalWidth() > vw) {
            // yes!
            vh -= this.horizontalScrollOffset;
        }

        vh -= this.mainHd.getHeight();

        var totalLength = ds.totalLength || 0;

        var visibleRows = Math.max(1, Math.floor(vh/this.rowHeight));

        this.rowClipped = 0;
        // only compute the clipped row if the total length of records
        // exceeds the number of visible rows displayable
        if (totalLength > visibleRows && this.rowHeight / 3 < (vh - (visibleRows*this.rowHeight))) {
            visibleRows = Math.min(visibleRows+1, totalLength);
            this.rowClipped = 1;
        }

        // if visibleRows   didn't change, simply void and return.
        if (this.visibleRows == visibleRows) {
            return;
        }

        this.visibleRows = visibleRows;

        // skip recalculating the row index if we are currently buffering.
        if (this.isBuffering) {
            return;
        }

        // when re-rendering, doe not take the clipped row into account
        if (this.rowIndex + (visibleRows-this.rowClipped) > totalLength) {
            this.rowIndex     = Math.max(0, totalLength-(visibleRows-this.rowClipped));
            this.lastRowIndex = this.rowIndex;
        }

        this.updateLiveRows(this.rowIndex, true);
    },


    adjustScrollerPos : function(pixels, suspendEvent)
    {
        if (pixels == 0) {
            return;
        }
        var liveScroller = this.liveScroller;
        var scrollDom    = liveScroller.dom;

        if (suspendEvent === true) {
            liveScroller.un('scroll', this.onLiveScroll, this);
        }
        this.lastScrollPos   = scrollDom.scrollTop;
        scrollDom.scrollTop += pixels;

        if (suspendEvent === true) {
            scrollDom.scrollTop = scrollDom.scrollTop;
            liveScroller.on('scroll', this.onLiveScroll, this, {buffer : this.scrollDelay});
        }

    }



});
/**
 * Ext.ux.grid.livegrid.JsonReader
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.JsonReader is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.JsonReader
 * @extends Ext.data.JsonReader
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.JsonReader = function(meta, recordType){

    Ext.ux.grid.livegrid.JsonReader.superclass.constructor.call(this, meta, recordType);
};


Ext.extend(Ext.ux.grid.livegrid.JsonReader, Ext.data.JsonReader, {

    /**
     * @cfg {String} versionProperty Name of the property from which to retrieve the
     *                               version of the data repository this reader parses
     *                               the reponse from
     */



    /**
     * Create a data block containing Ext.data.Records from a JSON object.
     * @param {Object} o An object which contains an Array of row objects in the property specified
     * in the config as 'root, and optionally a property, specified in the config as 'totalProperty'
     * which contains the total size of the dataset.
     * @return {Object} data A data block which is used by an Ext.data.Store object as
     * a cache of Ext.data.Records.
     */
    readRecords : function(o)
    {
        var s = this.meta;

        if(!this.ef && s.versionProperty) {
            this.getVersion = this.getJsonAccessor(s.versionProperty);
        }

        // shorten for future calls
        if (!this.__readRecords) {
            this.__readRecords = Ext.ux.grid.livegrid.JsonReader.superclass.readRecords;
        }

        var intercept = this.__readRecords.call(this, o);


        if (s.versionProperty) {
            var v = this.getVersion(o);
            intercept.version = (v === undefined || v === "") ? null : v;
        }


        return intercept;
    }

});
/**
 * Ext.ux.grid.livegrid.RowSelectionModel
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.RowSelectionModel is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.RowSelectionModel
 * @extends Ext.grid.RowSelectionModel
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.RowSelectionModel = function(config) {


    this.addEvents({
        /**
         * The selection dirty event will be triggered in case records were
         * inserted/ removed at view indexes that may affect the current
         * selection ranges which are only represented by view indexes, but not
         * current record-ids
         */
        'selectiondirty' : true
    });

    Ext.apply(this, config);

    this.pendingSelections = {};

    Ext.ux.grid.livegrid.RowSelectionModel.superclass.constructor.call(this);

};

Ext.extend(Ext.ux.grid.livegrid.RowSelectionModel, Ext.grid.RowSelectionModel, {


 // private
    initEvents : function()
    {
        Ext.ux.grid.livegrid.RowSelectionModel.superclass.initEvents.call(this);

        this.grid.view.on('rowsinserted',    this.onAdd,            this);
        this.grid.store.on('selectionsload', this.onSelectionsLoad, this);
    },



    // private
    onRefresh : function()
    {
        this.clearSelections(true);
    },



    /**
     * Callback is called when a row gets removed in the view. The process to
     * invoke this method is as follows:
     *
     * <ul>
     *  <li>1. store.remove(record);</li>
     *  <li>2. view.onRemove(store, record, indexInStore, isUpdate)<br />
     *   [view triggers rowremoved event]</li>
     *  <li>3. this.onRemove(view, indexInStore, record)</li>
     * </ul>
     *
     * If r defaults to <tt>null</tt> and index is within the pending selections
     * range, the selectionchange event will be called, too.
     * Additionally, the method will shift all selections and trigger the
     * selectiondirty event if any selections are pending.
     *
     */
    onRemove : function(v, index, r)
    {
        var ranges           = this.getPendingSelections();
        var rangesLength     = ranges.length;
        var selectionChanged = false;

        // if index equals to Number.MIN_VALUE or Number.MAX_VALUE, mark current
        // pending selections as dirty
        if (index == Number.MIN_VALUE || index == Number.MAX_VALUE) {

            if (r) {
                // if the record is part of the current selection, shift the selection down by 1
                // if the index equals to Number.MIN_VALUE
                if (this.isIdSelected(r.id) && index == Number.MIN_VALUE) {
                    // bufferRange already counted down when this method gets
                    // called
                    this.shiftSelections(this.grid.store.bufferRange[1], -1);
                }
                this.selections.remove(r);
                selectionChanged = true;
            }

            // clear all pending selections that are behind the first
            // bufferrange, and shift all pending Selections that lay in front
            // front of the second bufferRange down by 1!
            if (index == Number.MIN_VALUE) {
                this.clearPendingSelections(0, this.grid.store.bufferRange[0]);
            } else {
                // clear pending selections that are in front of bufferRange[1]
                this.clearPendingSelections(this.grid.store.bufferRange[1]);
            }

            // only fire the selectiondirty event if there were pendning ranges
            if (rangesLength != 0) {
                this.fireEvent('selectiondirty', this, index, 1);
            }

        } else {

            selectionChanged = this.isIdSelected(r.id);

            // if the record was not part of the selection, return
            if (!selectionChanged) {
                return;
            }

            this.selections.remove(r);
            //this.last = false;
            // if there are currently pending selections, look up the interval
            // to tell whether removing the record would mark the selection dirty
            if (rangesLength != 0) {

                var startRange = ranges[0];
                var endRange   = ranges[rangesLength-1];
                if (index <= endRange || index <= startRange) {
                    this.shiftSelections(index, -1);
                    this.fireEvent('selectiondirty', this, index, 1);
                }
             }

        }

        if (selectionChanged) {
            this.fireEvent('selectionchange', this);
        }
    },


    /**
     * If records where added to the store, this method will work as a callback,
     * called by the views' rowsinserted event.
     * Selections will be shifted down if, and only if, the listeners for the
     * selectiondirty event will return <tt>true</tt>.
     *
     */
    onAdd : function(store, index, endIndex, recordLength)
    {
        var ranges       = this.getPendingSelections();
        var rangesLength = ranges.length;

        // if index equals to Number.MIN_VALUE or Number.MAX_VALUE, mark current
        // pending selections as dirty
        if ((index == Number.MIN_VALUE || index == Number.MAX_VALUE)) {

            if (index == Number.MIN_VALUE) {
                // bufferRange already counted down when this method gets
                // called
                this.clearPendingSelections(0, this.grid.store.bufferRange[0]);
                this.shiftSelections(this.grid.store.bufferRange[1], recordLength);
            } else {
                this.clearPendingSelections(this.grid.store.bufferRange[1]);
            }

            // only fire the selectiondirty event if there were pendning ranges
            if (rangesLength != 0) {
                this.fireEvent('selectiondirty', this, index, r);
            }

            return;
        }

        // it is safe to say that the selection is dirty when the inserted index
        // is less or equal to the first selection range index or less or equal
        // to the last selection range index
        var startRange = ranges[0];
        var endRange   = ranges[rangesLength-1];
        var viewIndex  = index;
        if (viewIndex <= endRange || viewIndex <= startRange) {
            this.fireEvent('selectiondirty', this, viewIndex, recordLength);
            this.shiftSelections(viewIndex, recordLength);
        }
    },



    /**
     * Shifts current/pending selections. This method can be used when rows where
     * inserted/removed and the selection model has to synchronize itself.
     */
    shiftSelections : function(startRow, length)
    {
        var index         = 0;
        var newIndex      = 0;
        var newRequests   = {};

        var ds            = this.grid.store;
        var storeIndex    = startRow-ds.bufferRange[0];
        var newStoreIndex = 0;
        var totalLength   = this.grid.store.totalLength;
        var rec           = null;

        //this.last = false;

        var ranges       = this.getPendingSelections();
        var rangesLength = ranges.length;

        if (rangesLength == 0) {
            return;
        }

        for (var i = 0; i < rangesLength; i++) {
            index = ranges[i];

            if (index < startRow) {
                continue;
            }

            newIndex      = index+length;
            newStoreIndex = storeIndex+length;
            if (newIndex >= totalLength) {
                break;
            }

            rec = ds.getAt(newStoreIndex);
            if (rec) {
                this.selections.add(rec);
            } else {
                newRequests[newIndex] = true;
            }
        }

        this.pendingSelections = newRequests;
    },

    /**
     *
     * @param {Array} records The records that have been loaded
     * @param {Array} ranges  An array representing the model index ranges the
     *                        reords have been loaded for.
     */
    onSelectionsLoad : function(store, records, ranges)
    {
        this.replaceSelections(records);
    },

    /**
     * Returns true if there is a next record to select
     * @return {Boolean}
     */
    hasNext : function()
    {
        return this.last !== false && (this.last+1) < this.grid.store.getTotalCount();
    },

    /**
     * Gets the number of selected rows.
     * @return {Number}
     */
    getCount : function()
    {
        return this.selections.length + this.getPendingSelections().length;
    },

    /**
     * Returns True if the specified row is selected.
     *
     * @param {Number/Record} record The record or index of the record to check
     * @return {Boolean}
     */
    isSelected : function(index)
    {
        if (typeof index == "number") {
            var orgInd = index;
            index = this.grid.store.getAt(orgInd);
            if (!index) {
                var ind = this.getPendingSelections().indexOf(orgInd);
                if (ind != -1) {
                    return true;
                }

                return false;
            }
        }

        var r = index;
        return (r && this.selections.key(r.id) ? true : false);
    },


    /**
     * Deselects a record.
     * The emthod assumes that the record is physically available, i.e.
     * pendingSelections will not be taken into account
     */
    deselectRecord : function(record, preventViewNotify)
    {
        if(this.locked) {
            return;
        }

        var isSelected = this.selections.key(record.id);

        if (!isSelected) {
            return;
        }

        var store = this.grid.store;
        var index = store.indexOfId(record.id);

        if (index == -1) {
            index = store.findInsertIndex(record);
            if (index != Number.MIN_VALUE && index != Number.MAX_VALUE) {
                index += store.bufferRange[0];
            }
        } else {
            // just to make sure, though this should not be
            // set if the record was availablein the selections
            delete this.pendingSelections[index];
        }

        if (this.last == index) {
            this.last = false;
        }

        if (this.lastActive == index) {
            this.lastActive = false;
        }

        this.selections.remove(record);

        if(!preventViewNotify){
            this.grid.getView().onRowDeselect(index);
        }

        this.fireEvent("rowdeselect", this, index, record);
        this.fireEvent("selectionchange", this);
    },

    /**
     * Deselects a row.
     * @param {Number} row The index of the row to deselect
     */
    deselectRow : function(index, preventViewNotify)
    {
        if(this.locked) return;
        if(this.last == index){
            this.last = false;
        }

        if(this.lastActive == index){
            this.lastActive = false;
        }
        var r = this.grid.store.getAt(index);

        delete this.pendingSelections[index];

        if (r) {
            this.selections.remove(r);
        }
        if(!preventViewNotify){
            this.grid.getView().onRowDeselect(index);
        }
        this.fireEvent("rowdeselect", this, index, r);
        this.fireEvent("selectionchange", this);
    },


    /**
     * Selects a row.
     * @param {Number} row The index of the row to select
     * @param {Boolean} keepExisting (optional) True to keep existing selections
     */
    selectRow : function(index, keepExisting, preventViewNotify)
    {
        if(//this.last === index
           //||
           this.locked
           || index < 0
           || index >= this.grid.store.getTotalCount()) {
            return;
        }

        var r = this.grid.store.getAt(index);

        if(this.fireEvent("beforerowselect", this, index, keepExisting, r) !== false){
            if(!keepExisting || this.singleSelect){
                this.clearSelections();
            }

            if (r) {
                this.selections.add(r);
                delete this.pendingSelections[index];
            } else {
                this.pendingSelections[index] = true;
            }

            this.last = this.lastActive = index;

            if(!preventViewNotify){
                this.grid.getView().onRowSelect(index);
            }

            this.fireEvent("rowselect", this, index, r);
            this.fireEvent("selectionchange", this);
        }
    },

    clearPendingSelections : function(startIndex, endIndex)
    {
        if (endIndex == undefined) {
            endIndex = Number.MAX_VALUE;
        }

        var newSelections = {};

        var ranges       = this.getPendingSelections();
        var rangesLength = ranges.length;

        var index = 0;

        for (var i = 0; i < rangesLength; i++) {
            index = ranges[i];
            if (index <= endIndex && index >= startIndex) {
                continue;
            }

            newSelections[index] = true;
        }

        this.pendingSelections = newSelections;
    },

    /**
     * Replaces already set data with new data from the store if those
     * records can be found within this.selections or this.pendingSelections
     *
     * @param {Array} An array with records buffered by the store
     */
    replaceSelections : function(records)
    {
        if (!records || records.length == 0) {
            return;
        }

        var ds  = this.grid.store;
        var rec = null;

        var assigned     = [];
        var ranges       = this.getPendingSelections();
        var rangesLength = ranges.length

        var selections = this.selections;
        var index      = 0;

        for (var i = 0; i < rangesLength; i++) {
            index = ranges[i];
            rec   = ds.getAt(index);
            if (rec) {
                selections.add(rec);
                assigned.push(rec.id);
                delete this.pendingSelections[index];
            }
        }

        var id  = null;
        for (i = 0, len = records.length; i < len; i++) {
            rec = records[i];
            id  = rec.id;
            if (assigned.indexOf(id) == -1 && selections.containsKey(id)) {
                selections.add(rec);
            }
        }

    },

    getPendingSelections : function(asRange)
    {
        var index         = 1;
        var ranges        = [];
        var currentRange  = 0;
        var tmpArray      = [];

        for (var i in this.pendingSelections) {
            tmpArray.push(parseInt(i));
        }

        tmpArray.sort(function(o1,o2){
            if (o1 > o2) {
                return 1;
            } else if (o1 < o2) {
                return -1;
            } else {
                return 0;
            }
        });

        if (!asRange) {
            return tmpArray;
        }

        var max_i = tmpArray.length;

        if (max_i == 0) {
            return [];
        }

        ranges[currentRange] = [tmpArray[0], tmpArray[0]];
        for (var i = 0, max_i = max_i-1; i < max_i; i++) {
            if (tmpArray[i+1] - tmpArray[i] == 1) {
                ranges[currentRange][1] = tmpArray[i+1];
            } else {
                currentRange++;
                ranges[currentRange] = [tmpArray[i+1], tmpArray[i+1]];
            }
        }

        return ranges;
    },

    /**
     * Clears all selections.
     */
    clearSelections : function(fast)
    {
        if(this.locked) return;
        if(fast !== true){
            var ds  = this.grid.store;
            var s   = this.selections;
            var ind = -1;
            s.each(function(r){
                ind = ds.indexOfId(r.id);
                if (ind != -1) {
                    this.deselectRow(ind+ds.bufferRange[0]);
                }
            }, this);
            s.clear();

            this.pendingSelections = {};

        }else{
            this.selections.clear();
            this.pendingSelections    = {};
        }
        this.last = false;
    },


    /**
     * Selects a range of rows. All rows in between startRow and endRow are also
     * selected.
     *
     * @param {Number} startRow The index of the first row in the range
     * @param {Number} endRow The index of the last row in the range
     * @param {Boolean} keepExisting (optional) True to retain existing selections
     */
    selectRange : function(startRow, endRow, keepExisting)
    {
        if(this.locked) {
            return;
        }

        if(!keepExisting) {
            this.clearSelections();
        }

        if (startRow <= endRow) {
            for(var i = startRow; i <= endRow; i++) {
                this.selectRow(i, true);
            }
        } else {
            for(var i = startRow; i >= endRow; i--) {
                this.selectRow(i, true);
            }
        }

    }

});



/**
 * Ext.ux.grid.livegrid.Store
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.Store is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.Store
 * @extends Ext.data.Store
 *
 * The BufferedGridSore is a special implementation of a Ext.data.Store. It is used
 * for loading chunks of data from the underlying data repository as requested
 * by the Ext.ux.BufferedGridView. It's size is limited to the config parameter
 * bufferSize and is thereby guaranteed to never hold more than this amount
 * of records in the store.
 *
 * Requesting selection ranges:
 * ----------------------------
 * This store implementation has 2 Http-proxies: A data proxy for requesting data
 * from the server for displaying and another proxy to request pending selections:
 * Pending selections are represented by row indexes which have been selected but
 * which records have not yet been available in the store. The loadSelections method
 * will initiate a request to the data repository (same url as specified in the
 * url config parameter for the store) to fetch the pending selections. The additional
 * parameter send to the server is the "ranges" parameter, which will hold a json
 * encoded string representing ranges of row indexes to load from the data repository.
 * As an example, pending selections with the indexes 1,2,3,4,5,9,10,11,16 would
 * have to be translated to [1,5],[9,11],[16].
 * Please note, that by indexes we do not understand (primary) keys of the data,
 * but indexes as represented by the view. To get the ranges of pending selections,
 * you can use the getPendingSelections method of the BufferedRowSelectionModel, which
 * should be used as the default selection model of the grid.
 *
 * Version-property:
 * -----------------
 * This implementation does also introduce a new member called "version". The version
 * property will help you in determining if any pending selections indexes are still
 * valid or may have changed. This is needed to reduce the danger of data inconsitence
 * when you are requesting data from the server: As an example, a range of indexes must
 * be read from the server but may have been become invalid when the row represented
 * by the index is no longer available in teh underlying data store, caused by a
 * delete or insert operation. Thus, you have to take care of the version property
 * by yourself (server side) and change this value whenever a row was deleted or
 * inserted. You can specify the path to the version property in the BufferedJsonReader,
 * which should be used as the default reader for this store. If the store recognizes
 * a version change, it will fire the versionchange event. It is up to the user
 * to remove all selections which are pending, or use them anyway.
 *
 * Inserting data:
 * ---------------
 * Another thing to notice is the way a user inserts records into the data store.
 * A user should always provide a sortInfo for the grid, so the findInsertIndex
 * method can return a value that comes close to the value as it would have been
 * computed by the underlying store's sort algorithm. Whenever a record should be
 * added to the store, the insert index should be calculated and the used as the
 * parameter for the insert method. The findInsertIndex method will return a value
 * that equals to Number.MIN_VALUE or Number.MAX_VALUE if the added record would not
 * change the current state of the store. If that happens, this data is not available
 * in the store, and may be requested later on when a new request for new data is made.
 *
 * Sorting:
 * --------
 * remoteSort will always be set to true, no matter what value the user provides
 * using the config object.
 *
 * @constructor
 * Creates a new Store.
 * @param {Object} config A config object containing the objects needed for the Store to access data,
 * and read the data into Records.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.Store = function(config) {

    config = config || {};

    // remoteSort will always be set to true.
    config.remoteSort = true;

    this.addEvents(
         /**
          * @event bulkremove
          * Fires when a bulk remove operation was finished.
          * @param {Ext.ux.BufferedGridStore} this
          * @param {Array} An array with the records that have been removed.
          * The values for each array index are
          * record - the record that was removed
          * index - the index of the removed record in the store
          */
        'bulkremove',
         /**
          * @event versionchange
          * Fires when the version property has changed.
          * @param {Ext.ux.BufferedGridStore} this
          * @param {String} oldValue
          * @param {String} newValue
          */
        'versionchange',
         /**
          * @event beforeselectionsload
          * Fires before the store sends a request for ranges of records to
          * the server.
          * @param {Ext.ux.BufferedGridStore} this
          * @param {Array} ranges
          */
        'beforeselectionsload',
         /**
          * @event selectionsload
          * Fires when selections have been loaded.
          * @param {Ext.ux.BufferedGridStore} this
          * @param {Array} records An array containing the loaded records from
          * the server.
          * @param {Array} ranges An array containing the ranges of indexes this
          * records may represent.
          */
        'selectionsload'
    );

    Ext.ux.grid.livegrid.Store.superclass.constructor.call(this, config);

    this.totalLength = 0;


    /**
     * The array represents the range of rows available in the buffer absolute to
     * the indexes of the data model. Initialized with  [-1, -1] which tells that no
     * records are currrently buffered
     * @param {Array}
     */
    this.bufferRange = [-1, -1];

    this.on('clear', function (){
        this.bufferRange = [-1, -1];
    }, this);

    if(this.url && !this.selectionsProxy){
        this.selectionsProxy = new Ext.data.HttpProxy({url: this.url});
    }

};

Ext.extend(Ext.ux.grid.livegrid.Store, Ext.data.Store, {

    /**
     * The version of the data in the store. This value is represented by the
     * versionProperty-property of the BufferedJsonReader.
     * @property
     */
    version : null,

    /**
     * Inserts a record at the position as specified in index.
     * If the index equals to Number.MIN_VALUE or Number.MAX_VALUE, the record will
     * not be added to the store, but still fire the add-event to indicate that
     * the set of data in the underlying store has been changed.
     * If the index equals to 0 and the length of data in the store equals to
     * bufferSize, the add-event will be triggered with Number.MIN_VALUE to
     * indicate that a record has been prepended. If the index equals to
     * bufferSize, the method will assume that the record has been appended and
     * trigger the add event with index set to Number.MAX_VALUE.
     *
     * Note:
     * -----
     * The index parameter is not a view index, but a value in the range of
     * [0, this.bufferSize].
     *
     * You are strongly advised to not use this method directly. Instead, call
     * findInsertIndex wirst and use the return-value as the first parameter for
     * for this method.
     */
    insert : function(index, records)
    {
        // hooray for haskell!
        records = [].concat(records);

        index = index >= this.bufferSize ? Number.MAX_VALUE : index;

        if (index == Number.MIN_VALUE || index == Number.MAX_VALUE) {
            var l = records.length;
            if (index == Number.MIN_VALUE) {
                this.bufferRange[0] += l;
                this.bufferRange[1] += l;
            }

            this.totalLength += l;
            this.fireEvent("add", this, records, index);
            return;
        }

        var split = false;
        var insertRecords = records;
        if (records.length + index >= this.bufferSize) {
            split = true;
            insertRecords = records.splice(0, this.bufferSize-index)
        }
        this.totalLength += insertRecords.length;

        // if the store was loaded without data and the bufferRange
        // has to be filled first
        if (this.bufferRange[0] <= -1) {
            this.bufferRange[0] = 0;
        }
        if (this.bufferRange[1] < (this.bufferSize-1)) {
            this.bufferRange[1] = Math.min(this.bufferRange[1] + insertRecords.length, this.bufferSize-1);
        }

        for (var i = 0, len = insertRecords.length; i < len; i++) {
            this.data.insert(index, insertRecords[i]);
            insertRecords[i].join(this);
        }

        while (this.getCount() > this.bufferSize) {
            this.data.remove(this.data.last());
        }

        this.fireEvent("add", this, insertRecords, index);

        if (split == true) {
            this.fireEvent("add", this, records, Number.MAX_VALUE);
        }
    },

    /**
     * Remove a Record from the Store and fires the remove event.
     *
     * This implementation will check for the appearance of the record id
     * in the store. The record to be removed does not neccesarily be bound
     * to the instance of this store.
     * If the record is not within the store, the method will try to guess it's
     * index by calling findInsertIndex.
     *
     * Please note that this method assumes that the records that's about to
     * be removed from the store does belong to the data within the store or the
     * underlying data store, thus the remove event will always be fired.
     * This may lead to inconsitency if you have to stores up at once. Let A
     * be the store that reads from the data repository C, and B the other store
     * that only represents a subset of data of the data repository C. If you
     * now remove a record X from A, which has not been in the store, but is assumed
     * to be available in the data repository, and would like to sync the available
     * data of B, then you have to check first if X may have apperead in the subset
     * of data C represented by B before calling remove from the B store (because
     * the remove operation will always trigger the "remove" event, no matter what).
     * (Common use case: you have selected a range of records which are then stored in
     * the row selection model. User scrolls through the data and the store's buffer
     * gets refreshed with new data for displaying. Now you want to remove all records
     * which are within the rowselection model, but not anymore within the store.)
     * One possible workaround is to only remove the record X from B if, and only
     * if the return value of a call to [object instance of store B].data.indexOf(X)
     * does not return a value less than 0. Though not removing the record from
     * B may not update the view of an attached BufferedGridView immediately.
     *
     * @param {Ext.data.Record} record
     * @param {Boolean} suspendEvent true to suspend the "remove"-event
     *
     * @return Number the index of the record removed.
     */
    remove : function(record, suspendEvent)
    {
        // check wether the record.id can be found in this store
        var index = this._getIndex(record);

        if (index < 0) {
            this.totalLength -= 1;
            if(this.pruneModifiedRecords){
                this.modified.remove(record);
            }
            // adjust the buffer range if a record was removed
            // in the range that is actually behind the bufferRange
            this.bufferRange[0] = Math.max(-1, this.bufferRange[0]-1);
            this.bufferRange[1] = Math.max(-1, this.bufferRange[1]-1);

            if (suspendEvent !== true) {
                this.fireEvent("remove", this, record, index);
            }
            return index;
        }

        this.bufferRange[1] = Math.max(-1, this.bufferRange[1]-1);
        this.data.removeAt(index);

        if(this.pruneModifiedRecords){
            this.modified.remove(record);
        }

        this.totalLength -= 1;
        if (suspendEvent !== true) {
            this.fireEvent("remove", this, record, index);
        }

        return index;
    },

    _getIndex : function(record)
    {
        var index = this.indexOfId(record.id);

        if (index < 0) {
            index = this.findInsertIndex(record);
        }

        return index;
    },

    /**
     * Removes a larger amount of records from the store and fires the "bulkremove"
     * event.
     * This helps listeners to determine whether the remove operation of multiple
     * records is still pending.
     *
     * @param {Array} records
     */
    bulkRemove : function(records)
    {
        var rec  = null;
        var recs = [];
        var ind  = 0;
        var len  = records.length;

        var orgIndexes = [];
        for (var i = 0; i < len; i++) {
            rec = records[i];

            orgIndexes[rec.id] = this._getIndex(rec);
        }

        for (var i = 0; i < len; i++) {
            rec = records[i];
            this.remove(rec, true);
            recs.push([rec, orgIndexes[rec.id]]);
        }

        this.fireEvent("bulkremove", this, recs);
    },

    /**
     * Remove all Records from the Store and fires the clear event.
     * The method assumes that there will be no data available anymore in the
     * underlying data store.
     */
    removeAll : function()
    {
        this.totalLength = 0;
        this.bufferRange = [-1, -1];
        this.data.clear();

        if(this.pruneModifiedRecords){
            this.modified = [];
        }
        this.fireEvent("clear", this);
    },

    /**
     * Requests a range of data from the underlying data store. Similiar to the
     * start and limit parameter usually send to the server, the method needs
     * an array of ranges of indexes.
     * Example: To load all records at the positions 1,2,3,4,9,12,13,14, the supplied
     * parameter should equal to [[1,4],[9],[12,14]].
     * The request will only be done if the beforeselectionsloaded events return
     * value does not equal to false.
     */
    loadRanges : function(ranges)
    {
        var max_i = ranges.length;

        if(max_i > 0 && !this.selectionsProxy.activeRequest
           && this.fireEvent("beforeselectionsload", this, ranges) !== false){

            var lParams = this.lastOptions.params;

            var params = {};
            params.ranges = Ext.encode(ranges);

            if (lParams) {
                if (lParams.sort) {
                    params.sort = lParams.sort;
                }
                if (lParams.dir) {
                    params.dir = lParams.dir;
                }
            }

            var options = {};
            for (var i in this.lastOptions) {
                options.i = this.lastOptions.i;
            }

            options.ranges = params.ranges;

            this.selectionsProxy.load(params, this.reader,
                            this.selectionsLoaded, this,
                            options);
        }
    },

    /**
     * Alias for loadRanges.
     */
    loadSelections : function(ranges)
    {
        if (ranges.length == 0) {
            return;
        }
        this.loadRanges(ranges);
    },

    /**
     * Called as a callback by the proxy which loads pending selections.
     * Will fire the selectionsload event with the loaded records if, and only
     * if the return value of the checkVersionChange event does not equal to
     * false.
     */
    selectionsLoaded : function(o, options, success)
    {
        if (this.checkVersionChange(o, options, success) !== false) {

            var r = o.records;
            for(var i = 0, len = r.length; i < len; i++){
                r[i].join(this);
            }

            this.fireEvent("selectionsload", this, o.records, Ext.decode(options.ranges));
        } else {
            this.fireEvent("selectionsload", this, [], Ext.decode(options.ranges));
        }
    },

    /**
     * Checks if the version supplied in <tt>o</tt> differs from the version
     * property of the current instance of this object and fires the versionchange
     * event if it does.
     */
    // private
    checkVersionChange : function(o, options, success)
    {
        if(o && success !== false){
            if (o.version !== undefined) {
                var old      = this.version;
                this.version = o.version;
                if (this.version !== old) {
                    return this.fireEvent('versionchange', this, old, this.version);
                }
            }
        }
    },

    /**
     * The sort procedure tries to respect the current data in the buffer. If the
     * found index would not be within the bufferRange, Number.MIN_VALUE is returned to
     * indicate that the record would be sorted below the first record in the buffer
     * range, while Number.MAX_VALUE would indicate that the record would be added after
     * the last record in the buffer range.
     *
     * The method is not guaranteed to return the relative index of the record
     * in the data model as returned by the underlying domain model.
     */
    findInsertIndex : function(record)
    {
        this.remoteSort = false;
        var index = Ext.ux.grid.livegrid.Store.superclass.findInsertIndex.call(this, record);
        this.remoteSort = true;

        // special case... index is 0 and we are at the very first record
        // buffered
        if (this.bufferRange[0] <= 0 && index == 0) {
            return index;
        } else if (this.bufferRange[0] > 0 && index == 0) {
            return Number.MIN_VALUE;
        } else if (index >= this.bufferSize) {
            return Number.MAX_VALUE;
        }

        return index;
    },

    /**
     * Removed snapshot check
     */
    // private
    sortData : function(f, direction)
    {
        direction = direction || 'ASC';
        var st = this.fields.get(f).sortType;
        var fn = function(r1, r2){
            var v1 = st(r1.data[f]), v2 = st(r2.data[f]);
            return v1 > v2 ? 1 : (v1 < v2 ? -1 : 0);
        };
        this.data.sort(direction, fn);
    },



    /**
     * @cfg {Number} bufferSize The number of records that will at least always
     * be available in the store for rendering. This value will be send to the
     * server as the <tt>limit</tt> parameter and should not change during the
     * lifetime of a grid component. Note: In a paging grid, this number would
     * indicate the page size.
     * The value should be set high enough to make a userfirendly scrolling
     * possible and should be greater than the sum of {nearLimit} and
     * {visibleRows}. Usually, a value in between 150 and 200 is good enough.
     * A lesser value will more often make the store re-request new data, while
     * a larger number will make loading times higher.
     */


    // private
    onMetaChange : function(meta, rtype, o)
    {
        this.version = null;
        Ext.ux.grid.livegrid.Store.superclass.onMetaChange.call(this, meta, rtype, o);
    },


    /**
     * Will fire the versionchange event if the version of incoming data has changed.
     */
    // private
    loadRecords : function(o, options, success)
    {
        this.checkVersionChange(o, options, success);

        // we have to stay in sync with rows that may have been skipped while
        // the request was loading.
        // if the response didn't make it through, set buffer range to -1,-1
        if (!o) {
            this.bufferRange = [-1,-1];
        } else {
            this.bufferRange = [
                options.params.start,
                Math.max(0, Math.min((options.params.start+options.params.limit)-1, o.totalRecords-1))
            ];
        }

        Ext.ux.grid.livegrid.Store.superclass.loadRecords.call(this, o, options, success);
    },

    /**
     * Get the Record at the specified index.
     * The function will take the bufferRange into account and translate the passed argument
     * to the index of the record in the current buffer.
     *
     * @param {Number} index The index of the Record to find.
     * @return {Ext.data.Record} The Record at the passed index. Returns undefined if not found.
     */
    getAt : function(index)
    {
        //anything buffered yet?
        if (this.bufferRange[0] == -1) {
            return undefined;
        }

        var modelIndex = index - this.bufferRange[0];
        return this.data.itemAt(modelIndex);
    },

//--------------------------------------EMPTY-----------------------------------
    // no interface concept, so simply overwrite and leave them empty as for now
    clearFilter : function(){},
    isFiltered : function(){},
    collect : function(){},
    createFilterFn : function(){},
    sum : function(){},
    filter : function(){},
    filterBy : function(){},
    query : function(){},
    queryBy : function(){},
    find : function(){},
    findBy : function(){}

});
/**
 * Ext.ux.grid.livegrid.Toolbar
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.Toolbar is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://www.siteartwork.de/livegrid>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * toolbar that is bound to a {@link Ext.ux.grid.livegrid.GridView}
 * and provides information about the indexes of the requested data and the buffer
 * state.
 *
 * @class Ext.ux.grid.livegrid.Toolbar
 * @extends Ext.Toolbar
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.Toolbar = Ext.extend(Ext.Toolbar, {

    /**
     * @cfg {Ext.grid.GridPanel} grid
     * The grid the toolbar is bound to. If ommited, use the cfg property "view"
     */

    /**
     * @cfg {Ext.grid.GridView} view The view the toolbar is bound to
     * The grid the toolbar is bound to. If ommited, use the cfg property "grid"
     */

    /**
     * @cfg {Boolean} displayInfo
     * True to display the displayMsg (defaults to false)
     */

    /**
     * @cfg {String} displayMsg
     * The paging status message to display (defaults to "Displaying {start} - {end} of {total}")
     */
    displayMsg : 'Displaying {0} - {1} of {2}',

    /**
     * @cfg {String} emptyMsg
     * The message to display when no records are found (defaults to "No data to display")
     */
    emptyMsg : 'No data to display',

    /**
     * Value to display as the tooltip text for the refresh button. Defaults to
     * "Refresh"
     * @param {String}
     */
    refreshText : "Refresh",

    initComponent : function()
    {
        Ext.ux.grid.livegrid.Toolbar.superclass.initComponent.call(this);

        if (this.grid) {
            this.view = this.grid.getView();
        }

        var me = this;
        this.view.init = this.view.init.createSequence(function(){
            me.bind(this);
        }, this.view);
    },

    // private
    updateInfo : function(rowIndex, visibleRows, totalCount)
    {
        if(this.displayEl){
            var msg = totalCount == 0 ?
                this.emptyMsg :
                String.format(this.displayMsg, rowIndex+1,
                              rowIndex+visibleRows, totalCount);
            this.displayEl.update(msg);
        }
    },

    /**
     * Unbinds the toolbar.
     *
     * @param {Ext.grid.GridView|Ext.gid.GridPanel} view Either The view to unbind
     * or the grid
     */
    unbind : function(view)
    {
        var st;
        var vw;

        if (view instanceof Ext.grid.GridView) {
            vw = view;
        } else {
            // assuming parameter is of type Ext.grid.GridPanel
            vw = view.getView();
        }

        st = view.ds;

        st.un('loadexception', this.enableLoading,  this);
        st.un('beforeload',    this.disableLoading, this);
        st.un('load',          this.enableLoading,  this);
        vw.un('rowremoved',    this.onRowRemoved,   this);
        vw.un('rowsinserted',  this.onRowsInserted, this);
        vw.un('beforebuffer',  this.beforeBuffer,   this);
        vw.un('cursormove',    this.onCursorMove,   this);
        vw.un('buffer',        this.onBuffer,       this);
        vw.un('bufferfailure', this.enableLoading,  this);

        this.view = undefined;
    },

    /**
     * Binds the toolbar to the specified {@link Ext.ux.grid.Livegrid}
     *
     * @param {Ext.grird.GridView} view The view to bind
     */
    bind : function(view)
    {
        this.view = view;
        var st = view.ds;

        st.on('loadexception',   this.enableLoading,  this);
        st.on('beforeload',      this.disableLoading, this);
        st.on('load',            this.enableLoading,  this);
        view.on('rowremoved',    this.onRowRemoved,   this);
        view.on('rowsinserted',  this.onRowsInserted, this);
        view.on('beforebuffer',  this.beforeBuffer,   this);
        view.on('cursormove',    this.onCursorMove,   this);
        view.on('buffer',        this.onBuffer,       this);
        view.on('bufferfailure', this.enableLoading,  this);
    },

// ----------------------------------- Listeners -------------------------------
    enableLoading : function()
    {
        this.loading.setDisabled(false);
    },

    disableLoading : function()
    {
        this.loading.setDisabled(true);
    },

    onCursorMove : function(view, rowIndex, visibleRows, totalCount)
    {
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onRowsInserted : function(view, start, end)
    {
        this.updateInfo(view.rowIndex, Math.min(view.ds.totalLength, view.visibleRows-view.rowClipped),
                        view.ds.totalLength);
    },

    // private
    onRowRemoved : function(view, index, record)
    {
        this.updateInfo(view.rowIndex, Math.min(view.ds.totalLength, view.visibleRows-view.rowClipped),
                        view.ds.totalLength);
    },

    // private
    beforeBuffer : function(view, store, rowIndex, visibleRows, totalCount, options)
    {
        this.loading.disable();
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onBuffer : function(view, store, rowIndex, visibleRows, totalCount)
    {
        this.loading.enable();
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onClick : function(type)
    {
        switch (type) {
            case 'refresh':
                if (this.view.reset(true)) {
                    this.loading.disable();
                } else {
                    this.loading.enable();
                }
            break;

        }
    },

    // private
    onRender : function(ct, position)
    {
        Ext.PagingToolbar.superclass.onRender.call(this, ct, position);

        this.loading = this.addButton({
            tooltip : this.refreshText,
            iconCls : "x-tbar-loading",
            handler : this.onClick.createDelegate(this, ["refresh"])
        });

        this.addSeparator();

        if(this.displayInfo){
            this.displayEl = Ext.fly(this.el.dom).createChild({cls:'x-paging-info'});
        }
    }
});

Ext.namespace('Rack.widget');
(function () {
var ns = Rack.widget;

// LoadableMenu
//   Extend Menu and add external loading capabilities.
//
// Configuration Options:
//   Any configuration options that can be passed to the Ext.menu.Menu 
//   component can also be passed to this component.
// 
// Public Methods:
//   load(url, params) - Load menu items in JSON format from a URL.
// 

(function () {
var supercon = Ext.menu.Menu;
var superproto = supercon.prototype;
var con = ns.LoadableMenu = Ext.extend(supercon, {
    load: function (url, params) {
        var loader = new Ext.menu.Item({text: 'Loading...'});
        this.addItem(loader);
        
        var failCallback = function () {
            this.remove(loader);
            this.add({text: 'Failed to load menu items'});
        }.createDelegate(this);
        
        Ext.lib.Ajax.request((params) ? 'POST' : 'GET', url, {
            success: function (o) {
                this.remove(loader);
                try {
                    Ext.decode(o.responseText).menu.forEach(function (e, i, a) {
                        this.add(e);
                    }.createDelegate(this));
                } catch (e) {
                    failCallback();
                }
            }.createDelegate(this),
            failure: failCallback
        }, params);
        
        return this;
    }
});
})();

Ext.reg('loadablemenu', ns.LoadableMenu);

})();


(function () {
var ns = Rack.widget;

// Column
//   Extend Container with smart defaults for use as a container layout.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.Container 
//   component can also be passed to this component.
// 

(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = ns.Column = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            autoEl: 'div',
            cls: config.baseCls
        }, config));
    }
});
})();

Ext.reg('column', ns.Column);

})();
// DataFieldPanel
// 
// Configuration Options:
// 
//   title - The title of the panel
// 
//   labelWidth - The width of the label column.  Can be any valid CSS width.
// 
//   fields - An array of field definitions to show on the panel.  A field 
//     definition consists of the following properties:
// 
//     label - The label of the field
//
//     data - The data or a function that returns the data
// 
//     scope - If data is a function, it will be executed in this scope
// 
//     renderer - A function that will be used to render the data
// 
// Public Methods
// 
//   redraw - Redraws the panel, grabbing new data if available.
// 

Rack.widget.DataFieldPanel = Ext.extend(Ext.Panel, {
    
    basicRowTpl: new Ext.Template('<tr class="underline"><td class="title" {width}>{label}</td><td>{data}</td></tr>'),
    
    initComponent: function () {
        Ext.Panel.prototype.initComponent.call(this);
        
        this.cls = 'rack-dfp';
        this.basicRowTpl.compile();
    },
    
    onRender: function () {
        Ext.Panel.prototype.onRender.apply(this, arguments);
        
        this.draw();
    },
    
    redraw: function () {
        this.draw();
    },
    
    draw: function () {
        var output = '<table width="100%">';
        var labelWidth = this.labelWidth;
        
        if (this.fields) {
            Ext.each(this.fields, function (f) {
                var label = f.label;
                var data = !(f.data) ? 
                    null : 
                    (typeof(f.data) === 'function') ? 
                        f.data.call(f.scope) : 
                        f.data;
                
                if (f.renderer) {
                    data = f.renderer.call(f.scope, data);
                }
                
                if (f.rowRenderer) {
                    output += f.rowRenderer.call(f.scope, label, data, labelWidth);
                } else {
                    output += this.basicRowTpl.apply({
                        width: (labelWidth) ? String.format('style="width:{0};"', labelWidth) : '',
                        label: (label) ? label + ':' : '&nbsp;',
                        data: data
                    });
                }
            }, this);
        }
        
        output += '</table>';
        this.body.update(output);
    }
});
Ext.reg('datafield', Rack.widget.DataFieldPanel);


// DataFieldPanel2
// 
//   Extends Panel and adds the ability to easily bind dynamic properties to 
//   a row of the component.  The component will also listen to the properties 
//   and redraw itself if they are changed.
// 
// Usage:
// 
//   var dp = new Rack.widget.DataFieldPanel2({
//       title: 'MyPanel',
//       labelWidth: '33%',
//       fields: [
//           {
//               label: 'MyProperty',
//               data: myObject.myProperty,
//               view: MyPropertyViewCls
//           },
//           ...
//       ]
//   });
// 
// Configuration Options:
// 
//   title - The title of the panel
//   
//   labelWidth - The width of the label column.  Can be any valid CSS width.
// 
//   fields - An array of field definitions to show on the panel.  A field 
//     definition consists of the following properties:
// 
//     label - The label of the field
//
//     data - The dynamic property that this field's value is pulled from
// 
//     view - A class or function that formats the data
// 
//     buttons - An array of button configurations to add to the field
// 

(function () {
var supercon = Ext.Panel;
var superproto = supercon.prototype;
var con = Rack.widget.DataFieldPanel2 = Ext.extend(supercon, {
    constructor: function (config) {
        config = config || {};
        
        supercon.call(this, Ext.apply({
            cls: 'rack-dfp',
            defaults: {
                labelWidth: config.labelWidth || 0.5
            },
            items: config.fields ? 
                config.fields.map(function (field) {
                    return Ext.applyIf(field, {
                        xtype: field.viewType === 'group' ? 'r-dfp-group-row' :
                            field.viewType === 'list' ? 'r-dfp-list-row' : 
                            'r-dfp-row'
                    }, this);
                }) : null
        }, config));
    }
});
})();

Ext.reg('datafield2', Rack.widget.DataFieldPanel2);


(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = Rack.widget.DFPRow = Ext.extend(supercon, {
    constructor: function (config) {
        var hidden = config.hidden || false;
        if (config.showif) {
            hidden = !config.showif();
        }
        if (config.hideif) {
            hidden = config.hideif();
        }
        
        supercon.call(this, {
            autoEl: 'div',
            layout: 'simple-column',
            monitorResize: true,
            cls: 'rack-dfp-row ' + (config.rowCls || ''),
            style: config.rowStyle || null,
            hidden: hidden,
            items: [
                {
                    xtype: 'r-dfp-tfield',
                    columnWidth: config.labelWidth,
                    label: config.label
                },
                {
                    xtype: 'r-dfp-field',
                    columnWidth: 1 - (config.labelWidth),
                    data: config.data,
                    view: config.view,
                    buttons: config.buttons
                }
            ]
        });
    }
});
})();

Ext.reg('r-dfp-row', Rack.widget.DFPRow);


(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = Rack.widget.DFPListRow = Ext.extend(supercon, {
    constructor: function (config) {
        this.config = config;
        this.data = config.data;
        
        supercon.call(this, {
            autoEl: 'div',
            items: null, 
            listeners: {
                render: this.onRendered,
                destroy: this.onDestroyed,
                scope: this
            }
        });
    },
    
    onRendered: function () {
        this.update();
        this.data.addListener(this.update, this);
    },
    
    onDestroyed: function () {
        this.data.removeListener(this.update, this);
    },
    
    update: function () {
        if (this.items) {
            this.items.each(function (item) {
                this.remove(item);
            }, this);
        }
        
        var items = this.data ? this.data().getItems() : [];
        Ext.each(items, function (item) {
            this.add({
                xtype: 'r-dfp-row',
                label: this.config.label,
                data: item,
                view: this.config.view,
                buttons: this.config.buttons,
                rowCls: this.config.rowCls,
                rowStyle: this.config.rowStyle
            });
        }, this);
        
        this.doLayout();
    }
});
})();

Ext.reg('r-dfp-list-row', Rack.widget.DFPListRow);


(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = Rack.widget.DFPGroupRow = Ext.extend(supercon, {
    constructor: function (config) {
        this.config = config;
        this.data = config.data;
        
        this.dataContainer = new Ext.Container({
            autoEl: 'div',
            monitorResize: true,
            columnWidth: 1 - config.labelWidth
        });
        
        supercon.call(this, {
            autoEl: 'div',
            cls: 'rack-dfp-row ' + (config.rowCls || ''),
            style: config.rowStyle || null,
            layout: 'simple-column',
            monitorResize: true,
            items: [
                {
                    xtype: 'r-dfp-tfield',
                    columnWidth: config.labelWidth,
                    label: config.label
                },
                this.dataContainer
            ],
            listeners: {
                render: this.onRendered,
                destroy: this.onDestroyed,
                scope: this
            }
        });
    },
    
    onRendered: function () {
        this.update();
        this.data.addListener(this.update, this);
    },
    
    onDestroyed: function () {
        this.data.removeListener(this.update, this);
    },
    
    update: function () {
        if (this.dataContainer.items) {
            this.dataContainer.items.each(function (item) {
                this.dataContainer.remove(item);
            }, this);
        }
        
        var items = this.data ? this.data().getItems() : [];
        Ext.each(items, function (item) {
            this.dataContainer.add({
                xtype: 'r-dfp-field',
                data: item,
                view: this.config.view,
                buttons: this.config.buttons
            });
        }, this);
        
        this.dataContainer.doLayout();
    }
});
})();

Ext.reg('r-dfp-group-row', Rack.widget.DFPGroupRow);


(function () {
var supercon = Ext.BoxComponent;
var superproto = supercon.prototype;
var con = Rack.widget.DFPTField = Ext.extend(supercon, {
    onRender: function (ct, position) {
        this.el = ct.createChild({
            cls: 'rack-dfp-tfield',
            html: this.label ? String.format('{0}:', this.label) : ''
        });
        superproto.onRender.call(this, ct, position);
    }
});
})();

Ext.reg('r-dfp-tfield', Rack.widget.DFPTField);


(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = Rack.widget.DFPField = Ext.extend(supercon, {
    constructor: function (config) {
        this.viewCmp = null;
        
        if (config.view) {
            this.viewCmp = new config.view(config.data);
        } else {
            this.viewCmp = new Rack.widget.Label({
                html: typeof config.data === 'function' ? config.data() : config.data
            });
        }
        
        supercon.call(this, Ext.apply({
            monitorResize: true,
            items: this.viewCmp
        }, config));
    },
    
    onRender: function (ct, position) {
        this.el = ct.createChild({cls: 'rack-dfp-field'});
        this.bodyEl = this.el.createChild({cls: 'body'});
        
        superproto.onRender.call(this, ct, position);
        
        if (this.buttons) {
            this.buttonEl = this.el.insertFirst({tag: 'span', cls: 'btn-ct'});
            
            Ext.each(this.buttons, function (b, i, a) {
                var h, t = this;
                var cfg = {};
                Ext.apply(cfg, b);
                
                // Override the button handler with a new one that passes 
                // the data object back to the original handler.
                if (b.handler) {
                    h = b.handler;
                    cfg.handler = function () {
                        var data = typeof(t.data) === 'function' ? t.data() : t.data;
                        h.call(cfg.scope, data, t, t.viewCmp);
                    };
                }
                
                (new Ext.Button(cfg)).render(this.buttonEl);
            }, this);
        }
    },
    
    getLayoutTarget: function () {
        return this.bodyEl;
    }
});
})();

Ext.reg('r-dfp-field', Rack.widget.DFPField);


Rack.widget.OrganizerLayout = function (config) {
    this.id = Ext.id();
    this.stateId = (config) ? config.stateId : null;
    this.cmpState = this.getState();
    this.cmpOrder = [];
    this.cmpLookup = {};
    this.dragProxy = new Ext.dd.StatusProxy();
    
    Ext.layout.ContainerLayout.apply(this, arguments);
};

Ext.extend(Rack.widget.OrganizerLayout, Ext.layout.ContainerLayout, {
    
    autoWidth: true,
    titleCollapse: false,
    hideCollapseTool: false,
    collapseFirst: false,
    animate: false,
    
    stateId: null,
    ddGroup: 'organizer',
    
    renderAll: function () {
        Ext.layout.ContainerLayout.prototype.renderAll.apply(this, arguments);
        this.restorePosition();
    },

    renderItem: function (c) {
        var cid = c.sharedId || c.id;
        
        if (!this.cmpState.cmps[cid]) {
            this.cmpState.cmps[cid] = {
                collapsed: true
            };
        }
        
        var state = this.cmpState.cmps[cid];
        
        this.cmpOrder.push(c);
        this.cmpLookup[cid] = c;
        
        if (this.animate === false) {
            c.animCollapse = false;
        }
        c.collapsible = true;
        c.collapsed = (c.alwaysCollapsed) ? true : (c.alwaysExpanded) ? false : state.collapsed;
        if (this.autoWidth) {
            c.autoWidth = true;
        }
        if (this.titleCollapse) {
            c.titleCollapse = true;
        }
        if (this.hideCollapseTool) {
            c.hideCollapseTool = true;
        }
        if (this.collapseFirst !== undefined) {
            c.collapseFirst = this.collapseFirst;
        }
        Ext.layout.ContainerLayout.prototype.renderItem.apply(this, arguments);
        c.header.addClass('x-accordion-hd');
        c.on('expand', this.onExpand, this);
        c.on('collapse', this.onCollapse, this);
        
        if (c.movable !== false) {
            c.organizer = this;
            
            // Drag Source
            var ds = new Rack.widget.OrganizerPanelDragSource(c.el, {
                proxy: this.dragProxy,
                ddGroup: this.ddGroup
            });
            ds.setHandleElId(c.header.id);
            ds.dragData = c;
            
            // Drop Target
            var dt = new Rack.widget.OrganizerPanelDropTarget(c.el, {
                ddGroup: this.ddGroup
            });
            dt.dropData = c;
        }
    },
    
    onExpand: function (c) {
        this.cmpState.cmps[c.sharedId || c.id].collapsed = false;
        
        this.saveState();
    },
    
    onCollapse: function (c) {
        this.cmpState.cmps[c.sharedId || c.id].collapsed = true;
        
        this.saveState();
    },
    
    moveAtoB: function (ca, cb) {
        var targetPos = this.cmpOrder.indexOf(cb);
        if (this.cmpOrder.indexOf(ca) > targetPos) {
            this.cmpOrder.remove(ca);
            this.cmpOrder.splice(targetPos, 0, ca);
            ca.el.insertBefore(cb.el);
        } else {
            this.cmpOrder.remove(ca);
            this.cmpOrder.splice(targetPos, 0, ca);
            ca.el.insertAfter(cb.el);
        }
        
        this.cmpState.order = this.cmpOrder.map(function (c) {
            return c.sharedId || c.id;
        }, this);
        
        this.saveState();
    },
    
    insertFirst: function (c) {
        if (c) {
            var parent = this.container.getLayoutTarget();
            parent.insertFirst(c.el.dom);
        }
    },
    
    saveState: function () {
        if (Ext.state.Manager) {
            Ext.state.Manager.set(this.stateId || this.id, this.cmpState);
        }
    },
    
    getState: function () {
        var defaultState = {
            cmps: {},
            order: []
        };
        
        return (Ext.state.Manager) ? Ext.state.Manager.get(this.stateId || this.id, defaultState) : defaultState;
    },
    
    restorePosition: function () {
        var order = this.cmpState.order.map(function (cid) {
            return this.cmpLookup[cid];
        }, this);
        
        if (order.length) {
            this.cmpOrder = order;
            order.slice(0).reverse().forEach(this.insertFirst, this);
        }
    }
});
Ext.Container.LAYOUTS.organizer = Rack.widget.OrganizerLayout;


// Accordion Panel Drag Source

Rack.widget.OrganizerPanelDragSource = Ext.extend(Ext.dd.DragSource, {

    onInitDrag: function (x, y) {
        var tpl = String.format('Move {0} here', this.dragData.title || 'this panel');
        this.proxy.update(tpl);
        this.onStartDrag(x, y);
        return true;
    }
});


// Accordion Panel Drop Target
Rack.widget.OrganizerPanelDropTarget = Ext.extend(Ext.dd.DropTarget, {

    notifyEnter: function (dd, e, panel) {
        Ext.dd.DropTarget.prototype.notifyEnter.call(this, dd, e, panel);

        return (panel !== this.dropData) ? this.dropAllowed : this.dropNotAllowed;
    },
    
    notifyOver: function (dd, e, panel) {
        return (panel !== this.dropData) ? this.dropAllowed : this.dropNotAllowed;
    },
    
    notifyDrop: function (dd, e, panel) {
        if (panel !== this.dropData) {
            panel.organizer.moveAtoB(panel, this.dropData);
            return true;
        }
        return false;
    }
});

(function () {
var ns = Rack.widget;

// ATMProxy
//   Extends DataProxy for use as a Store proxy that calls a specified method 
//   which should handle the loading of data.
//

(function () {
var supercon = Ext.data.DataProxy;
var superproto = supercon.prototype;
var con = ns.ATMProxy = Ext.extend(supercon, {
    constructor: function (startTask, scope) {
        this.startTask = startTask;
        this.taskScope = scope;
        
        supercon.call(this);
    },
    
    load: function (params, reader, callback, scope, arg) {
        params = params || {};
        
        if (this.fireEvent('beforeload', this, params) === false) {
            callback.call(scope, null, arg, false);
            return;
        }
        
        if (!this.startTask) {
            callback.call(scope, null, arg, false);
            return;
        }
        
        this.currentRequest = [reader, callback, scope, arg];
        this.startTask.call(this.taskScope || this, this, params);
    },
    
    finishLoad: function (data, error) {
        var reader = this.currentRequest[0];
        var callback = this.currentRequest[1];
        var scope = this.currentRequest[2];
        var arg = this.currentRequest[3];
        var result;
        
        if (error) {
            this.fireEvent('loadexception', this, arg, error);
            callback.call(scope, null, arg, false);
            return;
        }
        
        try {
            result = reader.readRecords(data);
        } catch (e) {
            this.fireEvent('loadexception', this, arg, null, e);
            callback.call(scope, null, arg, false);
            return;
        }
        this.fireEvent('load', this, arg, null);
        callback.call(scope, result, arg, true);
        return;
    },
    
    // private
    update: function (params, records) {}
});
})();

Ext.reg('atm-proxy', ns.ATMProxy);

})();


(function () {
var ns = Rack.widget;

// SelectBox
//   Extends ComboBox with smart defaults in order to act like a simple HTML 
//   select element.
// 
// Usage:
// 
//   var sb = new Rack.widget.SelectBox({
//       store: ['value1', 'value2']
//   });
// 
//   The store can be a 1-dimensional array, a 2-dimensional array 
//   (value, text), or any other kind of store.
// 
// Configuration Options: 
//   Any configuration options that can be passed to the 
//   Ext.form.ComboBox component can also be passed to this component.
// 

(function () {
var supercon = Ext.form.ComboBox;
var superproto = supercon.prototype;
var con = ns.SelectBox = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            editable: false,
            forceSelection: true,
            triggerAction: 'all',
            mode: 'local',
            stateful: false,
            listeners: Ext.apply({
                'beforestatesave': function () {
                    return false;
                },
                'beforestaterestore': function () {
                    return false;
                }
            }, config.listeners || {})
        }, config));
    }
});
})();

Ext.reg('selectbox', ns.SelectBox);

})();


(function () {
var ns = Rack.widget;

// PagingGridPanel
//   Extends GridPanel and adds a PagingToolbar that automatically rebinds if 
//   the store changes.
// 
// Usage:
// 
//   var gp = new Rack.widget.PagingGridPanel({
//       ...
//       pageSize: ##
//   });
// 
// Configuration Options: 
//   Any configuration options that can be passed to the 
//   Ext.grid.GridPanel component can also be passed to this component, 
//   as well as the following options:
//
//   pageSize - A number that indicates the number of records to display on 
//     each pages.
// 
//   pager - An object that can contain any of the configuration options for 
//     the Ext.PagingToolbar.  These options will be applied to the paging 
//     toolbar that is created automatically.
// 
// Public Methods:
// 
//   changePage(page) - Calls the corresponding method on the paging toolbar.
// 

(function () {
var supercon = Ext.grid.GridPanel;
var superproto = supercon.prototype;
var con = ns.PagingGridPanel = Ext.extend(supercon, {
    constructor: function (config) {
        var pagerConfig = config.pager || {};
        
        if (config.pageSize) {
            pagerConfig.pageSize = config.pageSize;
        }
        
        pagerConfig.store = config.store;
        
        config.bbar = new Ext.PagingToolbar(pagerConfig);
        
        supercon.call(this, config);
    },
    
    reconfigure: function (store, cm) {
        var pager = this.getBottomToolbar();
        pager.unbind(this.getStore());
        pager.bind(store);
        Ext.grid.GridPanel.prototype.reconfigure.call(this, store, cm);
    },
    
    changePage: function (page) {
        this.getBottomToolbar().changePage(page);
    }
});
})();

Ext.reg('paginggrid', ns.PagingGridPanel);

})();


(function () {
var ns = Rack.widget;

// FitWindow
//   Extend Window with smart defaults in order to contain a single child 
//   component.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.Window component 
//   can also be passed to this component.
// 

(function () {
var supercon = Ext.Window;
var superproto = supercon.prototype;
var con = ns.FitWindow = Ext.extend(supercon, {
    constructor: function (config) {
        config = Ext.apply({
            layout: 'fit', 
            plain: true,
            border: false,
            constrainHeader: true,
            buttonAlign: 'right',
    		stateful: false
        }, config);
        
        if (!config.stateful) {
            if (!config.listeners) {
                config.listeners = {};
            }
            config.listeners.beforestatesave = function () {
                return false;
            };
            config.listeners.beforestaterestore = function () {
                return false;
            };
        }
        
        supercon.call(this, config);
    }
});
})();

Ext.reg('fit-window', ns.FitWindow);

})();


(function () {
var ns = Rack.widget;

// TabWindow
//   Extend FitWindow and add a PlainTabPanel.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.Window component 
//   can also be passed to this component.
// 
// Public Methods:
// 
//   getTabPanel() - Returns the PlainTabPanel that is the child component of 
//     the window.
// 

(function () {
var supercon = ns.FitWindow;
var superproto = supercon.prototype;
var con = ns.TabWindow = Ext.extend(supercon, {
    constructor: function (config) {
        config = config || {};
        
        var tabConfig = {
            items: config.items || []
        };
        
        this.tabPanel = new ns.PlainTabPanel(tabConfig);
        
        config.items = this.tabPanel;
        
        supercon.call(this, config);
    }, 
    
    getTabPanel: function () {
        return this.tabPanel;
    }
});
})();

Ext.reg('tabwindow', ns.TabWindow);

})();


(function () {
var ns = Rack.widget;

// PlainTabPanel
//   Extend TabPanel with smart defaults.
// 
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.TabPanel 
//   component can also be passed to this component.
// 

(function () {
var supercon = Ext.TabPanel;
var superproto = supercon.prototype;
var con = ns.PlainTabPanel = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            plain: true,
            activeTab: 0,
            layoutOnTabChange: true,
            enableTabScroll: true
        }, config));
    }
});
})();

Ext.reg('plaintabpanel', ns.PlainTabPanel);

})();


(function () {
var ns = Rack.widget;

// WizardPanel & WizardWindow
// 
//   
// 
ns.WizardConfig = {
    constructor: function (config) {
        this.nextBtn = new Ext.Button({
            text: this.msg.NEXT,
            iconCls: 'icn-next',
            handler: this.onNextBtnClicked,
            scope: this,
            minWidth: 75
        });
        
        this.previousBtn = new Ext.Button({
            text: this.msg.PREVIOUS,
            iconCls: 'icn-previous',
            handler: this.onPreviousBtnClicked,
            scope: this,
            minWidth: 75
        });
        
        this.space1 = new Ext.Toolbar.Spacer();
        this.space2 = new Ext.Toolbar.Spacer();
        this.cancelBtn = new Ext.Button({
            text: this.msg.CANCEL,
            handler: this.onCancelBtnClicked,
            scope: this
        });
        
        var wizConf = {
            layout: 'fit',
            buttons: [
                this.previousBtn,
                this.nextBtn,
                this.space1,
                this.space2,
                this.cancelBtn
            ]
        };
        
        config = Ext.apply(wizConf, config);
        
        this.steps = config.items;
        this.currentPanel = null;
        
        this.tabPanel = new ns.PlainTabPanel({
            items: [config.items]
        });
        
        config.items = this.tabPanel;
        
        this.constructor.superclass.constructor.call(this, config);
        
        this.addEvents('next', 'previous', 'cancel');
        
        this.on('render', this.onPanelRendered, this);
    },
    
    msg: {
        NEXT: 'Next',
        PREVIOUS: 'Previous',
        FINISH: 'Finish',
        CANCEL: 'Cancel'
    },
    
    onPanelRendered: function () {
        if (!this.showCancel) {
            this.hideCancelBtn();
        }
        
        this.steps = this.tabPanel.items;
        
        Ext.each(this.steps, function (step) {
            this.tabPanel.hideTabStripItem(step);
        }, this);
        
        this.setActiveStep(0);
    },
    
    onNextBtnClicked: function () {
        this.fireEvent('next');
    },
    
    hideNextBtn: function () {
        this.nextBtn.hide();
    },
    
    showNextBtn: function () {
        this.nextBtn.show();
    },
    
    onPreviousBtnClicked: function () {
        this.fireEvent('previous');
    },
    
    hidePreviousBtn: function () {
        this.previousBtn.hide();
    },
    
    showPreviousBtn: function () {
        this.previousBtn.show();
    },
    
    onCancelBtnClicked: function () {
        this.fireEvent('cancel');
    },
    
    hideCancelBtn: function () {
        this.space1.hide();
        this.space2.hide();
        this.cancelBtn.hide();
    },
    
    showCancelBtn: function () {
        this.space1.show();
        this.space2.show();
        this.cancelBtn.show();
    },
    
    setActiveStep: function (step) {
        if (this.currentStep) {
            this.tabPanel.hideTabStripItem(this.currentStep);
        }
        this.currentStep = this.steps[step];
        this.tabPanel.unhideTabStripItem(this.currentStep);
        this.tabPanel.setActiveTab(this.currentStep);
        this.tabPanel.doLayout.defer(1, this.tabPanel);
    },
    
    getActiveStep: function () {
        return this.currentStep;
    }
};

ns.WizardPanel = Ext.extend(Ext.Panel, ns.WizardConfig);
ns.WizardWindow = Ext.extend(ns.FitWindow, ns.WizardConfig);

})();


(function () {
var ns = Rack.widget;

// SingleFieldPanel
// 
// Configuration Options:
// 
//   title - The title of the panel
// 
//   data - The data or a function that returns the data
// 
//   scope - If data is a function, it will be executed in this scope
// 
//   renderer - A function that will be used to render the data
// 
// Public Methods
// 
//   redraw - Redraws the panel, grabbing new data if available.
// 

(function () {
var supercon = Ext.Panel;
var superproto = supercon.prototype;
var con = ns.SingleFieldPanel = Ext.extend(supercon, {
    initComponent: function () {
        Ext.Panel.prototype.initComponent.apply(this, arguments);
        
        this.data = this.data || this.html;
        delete this.html;
        this.cls = 'rack-single-field-panel';
    },
    
    onRender: function () {
        Ext.Panel.prototype.onRender.apply(this, arguments);
        
        this.draw();
    },
    
    redraw: function () {
        this.draw();
    },
    
    draw: function () {
        var data = this.data;
        
        if (typeof data === 'function') {
            data = data.call(this.scope);
        }
        
        if (this.renderer) {
            data = this.renderer(data);
        }
        
        this.body.update(data);
    }
});
})();

Ext.reg('singlefield', ns.SingleFieldPanel);

// SingleFieldPanel2
// 
//   Extends Panel and adds the ability to easily bind a dynamic property to 
//   the component.  The component will also listen to the property and 
//   redraw itself when it is changed.
// 
// Usage:
// 
//   var dp = new Rack.widget.SingleFieldPanel2({
//       title: 'MyPanel',
//       data: myObject.myProperty,
//       view: MyPropertyViewCls
//   });
// 
// Configuration Options:
// 
//   title - The title of the panel
//   
//   data - The dynamic property that this panel's data is pulled from
// 
//   view - A class or function that formats the data
// 

(function () {
var supercon = Ext.Panel;
var superproto = supercon.prototype;
var con = ns.SingleFieldPanel2 = Ext.extend(supercon, {
    initComponent: function () {
        Ext.Panel.prototype.initComponent.apply(this, arguments);
        
        this.data = this.data || this.html;
        delete this.html;
        this.cls = 'rack-single-field-panel';
    },
    
    onRender: function () {
        Ext.Panel.prototype.onRender.apply(this, arguments);
        
        if (this.data) {
            this.data.addListener(this.onFieldUpdate, this);
        }
        
        this.draw();
    },
    
    onDestroy: function () {
        if (this.data) {
            this.data.removeListener(this.onFieldUpdate, this);
        }
        
        Ext.Panel.prototype.onDestroy.apply(this, arguments);
    },
    
    onFieldUpdate: function () {
        this.draw();
    },
    
    draw: function () {
        var data = this.data();
        
        if (this.view) {
            data = new this.view(data);
        }
        
        this.body.update(data.valueOf());
    }
});
})();

Ext.reg('singlefield2', ns.SingleFieldPanel2);

})();


(function () {
var ns = Rack.widget;

// LoginWindow
//   Extend Window for use as a generic login window.
//
// Configuration Options: 
//   No configuration options can be passed.
// 

(function () {
var supercon = Ext.Window;
var superproto = supercon.prototype;
var con = ns.LoginWindow = Ext.extend(supercon, {
    constructor: function () {
        this.userField = new Ext.form.TextField({
            fieldLabel: 'Username',
            allowBlank: false,
            anchor: '100%'
        });
        
        this.passField = new Ext.form.TextField({
            fieldLabel: 'Password',
            inputType: 'password',
            allowBlank: false,
            anchor: '100%'
        });
        
        supercon.call(this, {
            title: 'Please Log In',
            iconCls: 'icn-user',
            layout: 'fit', 
            width: 300,
            height: 160,
            plain: true,
            border: false,
            closable: false,
            resizable: false,
            constrainHeader: true,
    		stateful: false,
            listeners: {
                'beforestatesave': function () {
                    return false;
                }
            },
            items: {
                xtype: 'form',
                labelWidth: 65,
                bodyStyle: 'padding:5px;',
                items: [
                    {
                        bodyStyle: 'padding:5px 0px 10px;',
                        border: false,
                        html: '<em>You are not currently logged in.  Please log in to continue.</em>'
                    },
                    this.userField,
                    this.passField
                ]
            },
            defaultButton: 0,
            buttons: [
                {
                    text: 'Log In',
                    handler: this.onLoginBtnClicked,
                    scope: this
                }
            ],
            keys: {
                key: Ext.EventObject.ENTER,
                handler: this.onLoginBtnClicked,
                scope: this
                
            }
        });
        
        this.addEvents('login_clicked');
    },
    
    onLoginBtnClicked: function () {
        this.fireEvent('login_clicked');
    },
    
    setUser: function (user) {
        this.userField.setValue(user);
    },
    
    getUser: function () {
        return this.userField.getValue();
    },
    
    getPassword: function () {
        return this.passField.getValue();
    }
});
})();

Ext.reg('login-window', ns.LoginWindow);

})();


(function () {
var ns = Rack.widget;

// IFrameWindow
//   Extends FitWindow and embeds an IFramePanel as a child.
//
// Configuration Options: 
//   Any configuration options that can be passed to the 
//   FitWindow component can also be passed to this component.
// 

(function () {
var supercon = ns.FitWindow;
var superproto = supercon.prototype;
var con = ns.IFrameWindow = Ext.extend(supercon, {
    constructor: function (config) {
        config = config || {};
        
        var url = config.url || 'about:blank';
        delete config.url;
        
        supercon.call(this, Ext.apply({
            items: new ns.IFramePanel({
                url: url
            }),
            buttons: [
                {
                    text: 'Close',
                    handler: function () {
                        this.close();
                    },
                    scope: this
                }
            ]
        }, config));
    }
});
})();

Ext.reg('iframe-window', ns.IFrameWindow);

})();


(function () {
var ns = Rack.widget;

// IFramePanel
//   Extend ManagedIframePanel with smart defaults for use as a simple 
//   IFrame panel.
//
// Configuration Options: 
//   Any configuration options that can be passed to the 
//   Ext.ux.ManagedIframePanel component can also be passed to this component.
// 

(function () {
var supercon = Ext.ux.ManagedIframePanel;
var superproto = supercon.prototype;
var con = ns.IFramePanel = Ext.extend(supercon, {
    constructor: function (config) {
        config = config || {};
        
        if (config.url && !config.defaultSrc) {
            config.defaultSrc = config.url;
            delete config.url;
        }
        
        supercon.call(this, Ext.apply({
            tbar: [
                '->',
                {
                    iconCls: 'x-tbar-loading',
                    tooltip: 'Reload',
                    handler: this.reload,
                    scope: this
                }
            ],
            closable: false,
            stateful: false,
            listeners: {
                'beforestatesave': function () {
                    return false;
                }
            }
        }, config));
    },
    
    reload: function () {
        this.setSrc();
    }
});
})();

Ext.reg('iframe', ns.IFramePanel);

})();


(function () {
var ns = Rack.widget;

// CardContainer
//   Extend Container with smart defaults for use as a card layout.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.Container 
//   component can also be passed to this component.
// 

(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = ns.CardContainer = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            autoEl: 'div',
            layout: 'card',
            layoutConfig: {
                deferredRender: true
            },
            activeItem: 0
        }, config || {}));
    },
    
    showView: function (view) {
        if (this.items.indexOf(view) < 0) {
            this.add(view);
        }
        if (this.activeItemCls && this.layout.activeItem) {
            this.layout.activeItem.el.removeClass(this.activeItemCls);
        }
        this.layout.setActiveItem(view);
        view.doLayout();
        if (this.activeItemCls) {
            view.el.addClass(this.activeItemCls);
        }
    },
    
    each: function (fn, scope) {
        this.items.each(fn, scope);
    }
});
})();

Ext.reg('card-container', ns.CardContainer);

})();

(function () {
var ns = Rack.widget;

// LoadablePanel
//   Extend Panel and add easy loading capabilities.
//
// Configuration Options:
//   Any configuration options that can be passed to the Ext.Panel 
//   component can also be passed to this component.
// 

(function () {
var supercon = Ext.Panel;
var superproto = supercon.prototype;
var con = ns.LoadablePanel = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            autoScroll: true,
            cls: 'rack-panel-pad-body',
            autoLoad: {url: config.url},
            tbar: [
                '->',
                {
                    iconCls: 'x-tbar-loading',
                    tooltip: 'Reload',
                    handler: this.reload,
                    scope: this
                }
            ]
        }, config));
    },
    
    reload: function () {
        this.body.getUpdater().refresh();
    }
});
})();

Ext.reg('loadablepanel', ns.LoadablePanel);

})();


(function () {
var ns = Rack.widget;

// SectionButton
//   Extend BoxComponent for use as a pretty button.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.BoxComponent 
//   component can also be passed to this component.
//   
//   text - Text of the button
//   href - URL of the button
//   target - Browser target of the button
//   iconCls - Icon of the button
//   iconAlign - Position of the icon: 'left' or 'right'
//   current - Is the button activated or not
//   disabled - Is the button disabled or not
//   disabledTip - A string or a ToolTip configuration
// 

(function () {
var supercon = Ext.BoxComponent;
var superproto = supercon.prototype;
var con = ns.SectionButton = Ext.extend(supercon, {
    constructor: function (config) {
        this.addEvents('click', 'activated', 'deactivated', 'enabled', 'disabled');
        supercon.call(this, config);
    },
    
    onRender: function (ct, position) {
        this.el = ct.createChild({
            tag: 'a',
            href: this.href || 'javascript:(function(){return})();',
            target: this.target || '',
            id: this.id || this.getId(),
            cls: this.disabled ? 'disabled' : (this.current ? 'current' : '')
        }, position);
        
        this.leftEl = this.el.createChild({tag: 'span'});
        this.rightEl = this.leftEl.createChild({tag: 'span'});
        
        this.textEl = this.rightEl.createChild({
            tag: 'span',
            cls: this.text ? (this.iconAlign === 'right' ? 'icn-after' : 'icn-before') : 'icn',
            html: this.text
        });
        
        if (this.iconCls) {
            this.textEl.addClass(this.iconCls);
        }
        
        this.el.on('click', this.onClick, this);
        
        superproto.onRender.call(this, ct, position);
    },
    
    setText: function (t) {
        this.text = t;
        
        if (this.rendered) {
            this.textEl.update(t);
        }
    },
    
    setIcon: function (i) {
        this.iconCls = i;
        
        if (this.rendered) {
            return;
        }
        
        this.textEl.addClass(i);
        
        if (this.iconCls) {
            this.textEl.removeClass(this.iconCls);
        }
    },
    
    setHref: function (l) {
        if (this.rendered) {
            this.el.set({href: l});
        }
        this.href = l;
    },
    
    activate: function () {
        this.current = true;
        
        if (this.rendered) {
            this.el.addClass('current');
        }
        
        this.fireEvent('activated', this);
    },
    
    deactivate: function () {
        this.current = false;
        
        if (this.rendered) {
            this.el.removeClass('current');
        }
        
        this.fireEvent('deactivated', this);
    },
    
    disable: function () {
        this.disabled = true;
        
        if (this.rendered) {
            this.el.addClass('disabled');
            
            if (this.disabledTip) {
                var tipConfig = {
                    target: this.el
                };
                
                if (typeof(this.disabledTip) === 'string') {
                    tipConfig.html = this.disabledTip;
                } else {
                    Ext.apply(tipConfig, this.disabledTip);
                    tipConfig.html = tipConfig.html || tipConfig.text;
                }
                
                this.disabledTipCmp = new Ext.ToolTip(tipConfig);
            }
        }
        
        this.fireEvent('disabled', this);
    },
    
    enable: function () {
        this.disabled = false;
        
        if (!this.rendered) {
            this.el.removeClass('disabled');
            
            if (this.disabledTipCmp) {
                this.disabledTipCmp.destroy();
            }
        }
        
        this.fireEvent('enabled', this);
    },
    
    onClick: function (e) {
        if (this.disabled) {
            e.stopEvent();
            return;
        }
        
        this.fireEvent('click', this, e);
    }
});
})();

Ext.reg('section-btn', ns.SectionButton);


(function () {
var supercon = Ext.Panel;
var superproto = supercon.prototype;
var con = ns.SectionButtonContainer = Ext.extend(supercon, {
    constructor: function (config) {
        supercon.call(this, Ext.apply({
            border: false,
            autoScroll: true,
            bodyStyle: 'background-color:transparent;',
            defaultType: 'section-btn',
            cls: 'section-buttons'
        }, config || {}));
    }
});
})();

})();


(function () {
var ns = Rack.widget;

// Label
//   Extend BoxComponent for use as a text label.
//
// Configuration Options: 
//   Any configuration options that can be passed to the Ext.BoxComponent 
//   component can also be passed to this component.
// 

(function () {
var supercon = Ext.BoxComponent;
var superproto = supercon.prototype;
var con = ns.Label = Ext.extend(supercon, {
    constructor: function (config) {
        config = config || {};
        
        if (typeof(config) === 'string') {
            config = {
                html: config
            };
        }
        
        if (config.text) {
            config.html = config.text;
        }
        
        this.text = config.html;
        
        supercon.call(this, config);
    },
    
    onRender: function (ct, position) {
        if (!this.el) {
            this.el = ct.createChild({
                html: this.text
            }, position);
        }
        
        superproto.onRender.apply(this, arguments);
    },
    
    setText: function (text) {
        if (this.rendered) {
            this.el.update(text);
        }
        this.text = text;
    },
    
    getText: function (text) {
        return this.text;
    }
});
})();

Ext.reg('label', ns.Label);

})();

(function () {
var ns = Rack.widget;

(function () {
var supercon = ns.CardContainer;
var superproto = supercon.prototype;
var con = ns.EditableView = Ext.extend(supercon, {
    constructor: function (property, view, field) {
        this.prop = property;
        this.mode = 'view';
        this.viewCard = new ns.ViewCardFrame(view);
        this.editCard = new ns.EditCardFrame(field);
        this.editCard.on({
            save: this.onSave,
            cancel: this.onCancel,
            scope: this
        });
        
        supercon.call(this, {
            autoHeight: true,
            autoWidth: true,
            cls: 'rack-editable-view',
            items: [
                this.viewCard,
                this.editCard
            ]
        });
    },
    
    onSave: function () {
        // commit and save
    },
    
    onCancel: function () {
        // discard buffer
        this.view();
    },
    
    view: function () {
        if (this.mode === 'view') {
            return;
        }
        this.mode = 'view';
        
        this.showView(this.viewCard);
    },
    
    edit: function () {
        if (this.mode === 'edit') {
            return;
        }
        this.mode = 'edit';
        
        this.editCard.standard();
        this.showView(this.editCard);
    },
    
    inlineEdit: function () {
        if (this.mode === 'inline') {
            return;
        }
        this.mode = 'inline';
        
        this.editCard.inline();
        this.showView(this.editCard);
    }
});
})();


(function () {
var supercon = Ext.Container;
var superproto = supercon.prototype;
var con = ns.ViewCardFrame = Ext.extend(supercon, {
    constructor: function (view) {
        supercon.call(this, {
            autoEl: 'div',
            cls: 'view-frame',
            autoHeight: true,
            autoWidth: true,
            items: view
        });
    }
});
})();


(function () {
var supercon = Ext.BoxComponent;
var superproto = supercon.prototype;
var con = ns.EditCardFrame = Ext.extend(supercon, {
    constructor: function (field) {
        this.field = field;
        this.isInline = false;
        this.addEvents('save', 'cancel');
        supercon.call(this, {});
    },
    
    onRender: function (ct, position) {
        this.el = ct.createChild({
            cls: 'edit-frame ' + (this.isInline ? 'inline' : '')
        }, position);
        
        this.fieldEl = this.el.createChild({});
        
        this.field.render(this.fieldEl);
        
        this.buttonEl = this.el.createChild({tag: 'span', cls: 'btn-ct'});
        this.clearEl = this.el.createChild({style: 'clear:both;'});
        
        this.saveBtn = new Ext.Button({
            text: 'Save',
            renderTo: this.buttonEl,
            listeners: {
                click: this.onSaveClicked,
                scope: this
            }
        });
        
        this.cancelBtn = new Ext.Button({
            text: 'Cancel',
            renderTo: this.buttonEl,
            listeners: {
                click: this.onCancelClicked,
                scope: this
            }
        });
        
        superproto.onRender.call(this, ct, position);
    },
    
    onSaveClicked: function () {
        if (this.field.isValid()) {
            this.fireEvent('save');
        }
    },
    
    onCancelClicked: function () {
        this.fireEvent('cancel');
    },
    
    standard: function () {
        this.isInline = false;
        if (this.rendered) {
            this.el.removeClass('inline');
        }
    },
    
    inline: function () {
        this.isInline = true;
        if (this.rendered) {
            this.el.addClass('inline');
        }
    },
    
    doLayout: function () {}
});
})();

(function () {
var supercon = ns.EditableView;
var superproto = supercon.prototype;
var con = ns.EditableContactView = Ext.extend(supercon, {
    constructor: function (property) {
        supercon.call(this, 
            property,
            new Rack.service.core.RichContactView(property),
            new Ext.form.TextField({}));
    }
});
})();

})();


(function () {
var ns = Rack.widget;

(function () {
var supercon = Ext.layout.ColumnLayout;
var superproto = supercon.prototype;
var con = ns.SimpleColumnLayout = Ext.extend(supercon, {
    extraCls: 'rack-sc-column',
    
    isValidParent: function (c, target) {
        return c.getEl().dom.parentNode == this.ct.dom;
    },
    
    onLayout: function (ct, target) {
        var cs = ct.items.items, len = cs.length, c, i;
        this.ct = ct;
        target.addClass('rack-sc-row');
        this.renderAll(ct, this.ct.el);
        
        var size = Ext.isIE && target.dom != Ext.getBody().dom ? target.getStyleSize() : target.getViewSize();
        
        if (size.width < 1 && size.height < 1) { // display none?
            return;
        }
        
        var w = size.width - target.getPadding('lr');
        var el = null;
        
        for (i = 0; i < len; i++) {
            c = cs[i];
            el = c.getEl();
            if (c.columnWidth) {
                c.setSize(Math.floor(c.columnWidth * w - el.getMargins('lr') - 0.5));
            }
        }
    }
});
})();

Ext.Container.LAYOUTS['simple-column'] = ns.SimpleColumnLayout;

})();
