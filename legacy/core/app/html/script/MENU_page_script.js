var testUrl =  "http://www.rackspace.com";
var isOpera = navigator.userAgent.indexOf('Opera');

function childDestination(myUrl) {
    opener.parent.workspace.location.href = myUrl;
    window.close();
}

function childTargetDestination(myUrl,target) {
    target.location.href = myUrl;
    window.close();
}

function closeMe() {
        opener.has_nav = 0;
            window.close();
}

function falseMe() {
        opener.has_nav = 0;
}

function beenOpened() {
        opener.has_nav = 1;
}

function rollExit(state) {
        b_exit = eval("b_close_" + state);
        document.exit_button.src = b_exit.src;
}

function changeTDBG(cellName,arrowName) {
    // ARROW ROLLOVER
    if ( arrowName != 'none') {
        arrow = eval("document." + arrowName);
        arrow.src = next_on_ns4.src;
    }

    //Opera Exit
    if ( isOpera >= 0) {
        return 1;
    }

    //BACKGROUND COLOR CHANGE
    if (document.getElementById && !document.all) {
        //MOZILLA
        document.getElementById(cellName).bgColor = "#0000CC";
        //document.getElementById(cellName + "a").bgColor = "#0000CC";
    } else if (document.layers) {
        // Netscape 4.x
        il = eval("document." + cellName + "layer");
        il.bgColor = "#0000CC";
    } else if (document.all) {
        // IE
        cell = eval("document.all." + cellName);
        //cella = eval("document.all." + cellName + "a");
        cell.bgColor = "#0000CC";
        //cella.bgColor = "#0000CC";
    } 
}

function changeTDBGBack(cellName,arrowName) {
    // ARROW ROLLOVER
    if ( arrowName != 'none') {
        arrow = eval("document." + arrowName);
        arrow.src = next_off.src;
    }
   
    //Opera Exit
    if ( isOpera >= 0) {
        return 1;
    }

    //BACKGROUND COLOR CHANGE
    if (document.getElementById && !document.all) {
        //MOZILLA
        document.getElementById(cellName).bgColor = "#3266CC";
        //document.getElementById(cellName + "a").bgColor = "#3266cc";
    } else if (document.layers) {
        // Netscape 4.x
        il = eval("document." + cellName + "layer");
        il.bgColor = "#3266CC";
   } else if (document.all) {
        // IE
        cell = eval("document.all." + cellName);
        //cella = eval("document.all." + cellName + "a");
        cell.bgColor = "#3266CC";
        //cella.bgColor = "#3266CC";
    } 
}
