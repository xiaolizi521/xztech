// Behavioral Essence
// If behaviors are specified, it mixes the target with the behaviors.
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
        
        // If we have behaviors to mix in,
        // we create a new subclass.
        if (this.behaviors) {
            Target = Rack.mix(this.target, this.behaviors);
        }
        
        if (this.validate()) {
            return new Target(this.params);
        }
    }
});
