Rack.util.removeContextMenu = function (el) {
    Ext.fly(el).on('contextmenu', function (e) {
        e.stopEvent();
    });
    return this;
};

Rack.util.addContextMenu = function (el, menu) {
    var show = function (e) {
        menu.showAt([e.getPageX(), e.getPageY()]); // Show on the mouse
    };
    
    if (Ext.isOpera) { // Ctrl + Click for Opera
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
// START - Parser Scope

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

// Run the parsers every time content is updated
Ext.Element.prototype.update = Rack.sequence(
    Ext.Element.prototype.update, 
    Rack.util.runParsers);

Ext.onReady(Rack.util.startParsers, Rack.util);


// Tables to Grids
Rack.util.registerParser(function (el) {
    Ext.DomQuery.select('table.rack-grid', el).
    map(function (e, i, a) {
        // Removing the class now will prevent this 
        // element from being grabbed twice
        Ext.fly(e).removeClass('rack-grid');
        return e;
    }).
    forEach(function (e, i, a) {
        var g = new Ext.grid.TableGrid(e);
        g.render();
        g.getSelectionModel().lock();
    });
});

// Links to Buttons
Rack.util.registerParser(function (el) {
    Ext.DomQuery.select('a.rack-button', el).
    map(function (e, i, a) {
        // Removing the class now will prevent this 
        // element from being grabbed twice
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

// END - Parser Scope
})();