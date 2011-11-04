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
    
    // Creates a new subclass of base with all of the
    // functionality from the mixins
    mix: function (base, mixins) {
        var target, p;
        
        if (!mixins) {
            return base;
        }
        
        // The new class constructor
        target = function () {
            var args = arguments;
            // Call the original target's constructor
            base.apply(this, args);
            // Call the mixins' constructors
            mixins.forEach(function (e, i, a) {
                e.apply(this, args);
            }.createDelegate(this));
        };
        
        // Inherit the base
        Ext.extend(target, base);
        
        // Mix the new class with all of the mixins
        mixins.forEach(function (e, i, a) {
            for (p in e.prototype) {
                // Don't overwrite existing properties
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


// The following functions and implementations were taken
// from Douglas Crockford's website (javascript.crockford.com).
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
