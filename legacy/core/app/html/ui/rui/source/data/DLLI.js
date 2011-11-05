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