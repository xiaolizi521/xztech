// Essence
// Automatically creates setter functions for a specified parameter list.
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
