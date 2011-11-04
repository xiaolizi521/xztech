<!-- 
// Progress Bar Mechansisms    
    var avgLoadTime                 //set in page
    var progressEnd = 20;		    // set to number of progress <span>'s.
    var progressColor = '#003399';	// set to progress bar color
    var progressInterval = (avgLoadTime*50);
    var progressAt = progressEnd;
    var progressTimer;
    var progressRemain;
    
    /*if((! avgLoadTime) || (avgLoadTime = 0)) {
        progress_stop();    
    }*/
    function progress_clear() {
    	for (var i = 1; i <= progressEnd; i++) 
        document.getElementById('progress'+i).style.backgroundColor = 'transparent';
    	progressAt = 0;
    }
    function progress_update() {
    	progressAt++;
    	if (progressAt > progressEnd) progress_clear();
    	else document.getElementById('progress'+progressAt).style.backgroundColor = progressColor;
        updateStatus();
        progressTimer = setTimeout("progress_update()",progressInterval);
    
    }
    function updateStatus() {
        progressComplete = progressAt*5;
        if(progressComplete < 100) {
            document.forms['remain'].time.value = progressComplete;
            document.forms['remain'].load.value = avgLoadTime;
            window.status = 'Amount Complete: ' + progressComplete + '%';
        } else {
            document.forms['remain'].time.value = progressComplete;
            progress_stop();
        }
            
    }
    function progress_stop() {
        window.status = 'Rendering Complete.';
        clearTimeout(progressTimer);
        progressTimer = 0;
    }

// Layer Display Mechanism
    var flag = 0;
    var DHTML = (document.getElementById || document.all || document.layers);
	function ap_getObj(name) { 
		if (document.getElementById) { 
			return document.getElementById(name).style; 
		} 
		else if (document.all) { 
			return document.all[name].style;
		} 
		else if (document.layers) { 
			return document.layers[name];
		} 
	} 
	function ap_showWaitMessage(div,flag)  { 
    if (!DHTML) return;
        var flag = (flag);
        if(flag > 0) progress_update();
        else progress_stop(); 
		var x = ap_getObj(div); 
        x.visibility = (flag) ? 'visible':'hidden'
	if(! document.getElementById) 
     if(document.layers) 
        x.left=280/2; 
        //return true;     
	} 
//If page takes this long, show progress bar    
if(avgLoadTime > 6) {    
    ap_showWaitMessage('waitDiv', 1);
}

//-->