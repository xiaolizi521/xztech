// Set up global variables

var currlocation = "local"; // This is to set either Local or WorldWide

var localdcs = ["local1","local2"]; // The local pollers to a data center. Change this for each DC.

var alldcs = ['remote1','remote2','remote3','remote4','remote5','remote6']; // All pollers
var pollerheads = ['pollerhead1','pollerhead2','pollerhead3','pollerhead4','pollerhead5','pollerhead6'];
var alldclocations = ['sat','sat','dfw','dfw','iad','iad','lon','lon'];
var foo = 0;
var local = "iad"; // change this variable to same as location in php (To be fixed to be automated from one location).
var currentextdcs = new Array();

var myDCs = new Array();

myDCs["sat"] = "sat.rackspace.com";
myDCs["dfw"] = "dfw1.rackspace.com";
myDCs["iad"] = "iad1.rackspace.com";
myDCs["lon"] = "lon.rackspace.com";

alldclocations.each(function(value,index){
	
	if(value != local) {
	
		currentextdcs[foo] = value;
		foo++;
	}
	
});



var myPollers = ['onms-1','onms-2'];

var currlocations = localdcs; // Initially we are local only.

var services = ["http","smtp","pop3","smtp","mysql","pgsql"]; // These are the services we poll currently.


// This is to retreive the values of a particular Radio group. Returns checked value.

function getRadioValue(idOrName) {

	var value = null;
	var element = document.getElementById(idOrName);
	var radioGroupName = null;  
	
	// If null, then the id must be the radio group name
	
	if (element == null) {
		radioGroupName = idOrName;
	} 
	
	else {
		radioGroupName = element.name;     
	}
	
	if (radioGroupName == null) {
	
		return null;
	}
	
	var radios = document.getElementsByTagName('input');
	
	for (var i=0; i<radios.length; i++) {
	
		var input = radios[ i ];    
	
		if (input.type == 'radio' && input.name == radioGroupName && input.checked) {                          
		
			value = input.value;
			break;
		}
		
	}
	
	return value;
}			

// Retreives the Host variable currently entered in the Form

function getHost() {

	var value = null;

	var element = document.getElementById('host');

	var value = element.value;

	return value;
}

// Main function. Performs the tests, handles the hiding and showing of panes, sends request to tool.

function performTests() {
	
	// Get the current location selected (worldwide or local)
	
	currlocation = getRadioValue("location");
	
	// Get the selected service test
	
	var service = getRadioValue("test");
	
	$('debug').innerHTML = myDCs[local];
	
	// Get the host
	
	var host = getHost();
	


	// Set the appropriate locations based on location
	
	if(currlocation == "local") {
		
		currlocations = localdcs;
	}
	
	else {
	
		currlocations = ['local1','local2','remote1','remote2','remote3','remote4','remote5','remote6'];
	}
	
	// Host cannot be null, host "" does not exist.
		
	if(host == null || host == "") {
	
		alert("You have not entered a host. This is required.");
	}
		

	// Otherwise... begin the testing process 
	
	else {

		// Reflect the current processing within the panes
	
		updateDivs(service, host);
		
		// Set the paramaters that are, for the most part, static. These are POST to the PHP script.
		
		var params = "hidden=4b40ef307bc0fa07f19450653a8253ae&host=" + host + "&service=" + service;
		
		// Local only requires two pollers, no need to go through all DCs again. Need to hide the unused panes.
		
		if (currlocation == 'local') {	
			
			// Hide non-local DCs.
			
			alldcs.each(function (value,index) {
				
				if(value != localdcs[0] && value != localdcs[1])
					
					$(value).hide();
				
			});
			
			pollerheads.each(function (value,index) {
				
				if(value != localdcs[0] && value != localdcs[1])
					
					$(value).hide();
				
			});

			// Send request to tool.php to process given command from requested pollers.
			
			localdcs.each(function (value,index) {
				
				new Ajax.Updater(value,'tool.php', { method: 'post', parameters: params + "&dc=" + local + "&poller=" + index });
			});
			
			
		}
		
		else {
		
			var string = '';
			
			// Show all DC panes.
			
			alldcs.each(function (value,index) {
				
				if(value != localdcs[0] && value != localdcs[1])
				
					$(value).show();
				
			});
			
			pollerheads.each(function (value,index) {
				
				if(value != localdcs[0] && value != localdcs[1])
					
					$(value).show();
				
			});
			
			// Send request to tool.php to process given command from requested pollers.
			var x = 0;
			

			
			localdcs.each(function (value,index) {
				
				new Ajax.Updater(value,'tool.php', { method: 'post', parameters: params + "&dc=" + local + "&poller=" + index });
			});
			
			alldcs.each(function (value,index){
				
				if (value != "local1" && value != "local2"){

					$('debug').innerHTML = currentextdcs[index];				
					new Ajax.Updater(value, 'tool.php', { method: 'post', parameters: params + "&dc=" + currentextdcs[index] + "&poller=" + x });
				}
				
				if(x == 0) { x=1; }
				else {x=0;}
				
			});
		}
	}
	
}

// This function updates the text in all of the DIVs/Table panes to reflect the current processing request.

function updateDivs(service, host) {
	
	for(i in services) {
		
		if(service == services[i]) {
			
			var servicetext = "<p>Now performing a " + service.toUpperCase() + " test using NMap on " + host + ". Please wait. This pane will refresh when the test is complete.</p>";			
			
		}
	}

	if (servicetext == null || servicetext == '') {
	

		var servicetext = "<p>Now performing a " + service.toUpperCase() + " test on " + host + ". Please wait. This pane will refresh when the test is complete.</p>";
		
	}

	for (x in currlocations) {
		

		$(currlocations[x]).innerHTML = servicetext;
	}

}

// Initiliaze the page. This first does a ping to the requested HOST from the CORE attempt.
// If CORE does not make the call to ping.php, localhost is displayed.

function init() {
	
	var host = getVar('host');
	var service = "ping";
	
	if (host == '') { 
		
		var host = "rackspace.com"; 
	}
	
	alldcs.each(function (value,index) {
	
		if(value != localdcs[0] && value != localdcs[1])
			
			$(value).hide();
		
	});

	pollerheads.each(function (value,index) {
		
		if(value != localdcs[0] && value != localdcs[1])
			
			$(value).hide();
		
	});			
	
	updateDivs(service,host);
	
	var params = "hidden=4b40ef307bc0fa07f19450653a8253ae&host=" + host + "&service=" + service;

	localdcs.each(function (value,index) {
		
		new Ajax.Updater(value,'tool.php', { method: 'post', parameters: params + "&dc=" + local + "&poller=" + index });
	});
}

// This function is set up to retreive the GET variables passed by CORE.

function getVar(name){

	get_string = document.location.search;         

	return_value = '';
	
	do { //This loop is made to catch all instances of any get variable.
	
		name_index = get_string.indexOf(name + '=');
	
		if(name_index != -1) {
		
			get_string = get_string.substr(name_index + name.length + 1, get_string.length - name_index);
		
			end_of_value = get_string.indexOf('&');
	
			if(end_of_value != -1)                
			
				value = get_string.substr(0, end_of_value);                
	
			else                
			
				value = get_string;                
		
			if(return_value == '' || value == '')
	
				return_value += value;
	
			else
		
				return_value += ', ' + value;
		}

	} while(name_index != -1)
	
	//Restores all the blank spaces.
	
		space = return_value.indexOf('+');
	
	while(space != -1) { 
	
		return_value = return_value.substr(0, space) + ' ' + 
		return_value.substr(space + 1, return_value.length);
	
		space = return_value.indexOf('+');

	}
	
	return(return_value);        
}