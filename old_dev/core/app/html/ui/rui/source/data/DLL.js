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
