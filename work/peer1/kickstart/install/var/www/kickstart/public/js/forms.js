<!--


// use for the onClick method for the all-<groupname> checkbox
function ToggleGroup (node) {
	group_name = node.name.replace(/all-/, "");

	// climb up the DOM to the FORM we are inside; stop if we get to the BODY tag
	while (node.localName != 'FORM' && node.localName != 'BODY') {
		node = node.parentNode;
	}
	
	// if we found a FORM, get the status of the All checkbox, and apply it to all checkboxes with this name 
	if (node.localName == 'FORM') {
		var all_box = 'all-' + group_name;
		var turn_to = node.elements[all_box];
		for (var i = 0; i < node.elements.length; i++) {
	    		if(node.elements[i].type == 'checkbox' && node.elements[i].name == group_name){
				node.elements[i].checked = turn_to.checked;
	    		}
	  	}
	}
}

// use for onClick method for items in a checkbox group, to check or uncheck the group's Allbox appropriately
function CheckGroupAllbox (node) {
	var group_name = node.name.replace(/all-/, '');
	var allbox_name = 'all-' + group_name;
	
	// climb up the DOM to the FORM we are inside; stop if we get to the BODY tag
	while (node.localName != 'FORM' && node.localName != 'BODY') {
		node = node.parentNode;
	}
	
	// if we found a FORM, get the status of the All checkbox, and apply it to all checkboxes with this name 
	if (node.localName == 'FORM') {
		var all_checked = all_unchecked = true;
		
		for (var i = 0; i < node.elements.length; i++) {
	    		if(node.elements[i].type == 'checkbox' && node.elements[i].name == group_name) {
				if ( node.elements[i].checked == false ) { all_checked = false; }
				if ( node.elements[i].checked == true )  { all_unchecked = false; }
	    		}
	  	}
		
	  	if (all_checked == true) { node.elements[allbox_name].checked = true; }
		else { node.elements[allbox_name].checked = false; }
	}
}


// call from onLoad in BODY tag, finds all "All" checkboxes on the page and runs CheckStatusGroup on them
function CheckAllGroupAllbox () {
	if (document.forms[1]) {
		for (var j = 0; j < document.forms.length; j++) {
			for (var i = 0; i < document.forms[j].elements.length; i++) {
				if( document.forms[j].elements[i].type == 'checkbox' &&  document.forms[j].elements[i].name.match(/all-/) ) {
					CheckGroupAllbox(document.forms[j].elements[i]);
				}
			}
		}
	}
}




/*

// Checks to see if all boxes for a particular group are set, 
//  and set the 'All' box appropriately if they are all checked or not checked
function CheckGroupStatus (node) {
	groupname = node.name.replace(/all-/, '');
	all_box = node.name;

	// climb up the DOM to the FORM we are inside; stop if we get to the BODY tag
	while (node.localName != 'FORM' && node.localName != 'BODY') {
		node = node.parentNode;
	}
	
	// if we found a FORM, get the status of the All checkbox, and apply it to all checkboxes with this name 
	if (node.localName == 'FORM') {
		var all_checked = all_unchecked = true;

		for (var i = 0; i < node.elements.length; i++) {
	    		if(node.elements[i].type == 'checkbox' && node.elements[i].name == groupname) {
				if ( node.elements[i].checked == false ) { all_checked = false; }
				if ( node.elements[i].checked == true )  { all_unchecked = false; }
	    		}
	  	}
		
	  	if (all_checked == true) { node.elements[all_box].checked = true; }
		else { node.elements[all_box].checked = false; }
	}
}

*/



// DEPRECATED
// used at load time, finds all "All" checkboxes on the page and runs CheckStatus on them
function CheckStatusAll () {
	if (document.checkboxForm != null) {
		for (var i = 0; i < document.checkboxForm.elements.length; i++) {
	    		if( document.checkboxForm.elements[i].type == 'checkbox' && 
	    		    document.checkboxForm.elements[i].name.match(/all-/) ) {
				groupname = document.checkboxForm.elements[i].name.replace(/all-/, "");
				CheckStatus(groupname);
	    		}
		}
	}
}


// DEPRECATED
function CheckStatus (elements) {
	if (document.checkboxForm != null) {
		var all_box = 'all-' + elements;
		var all_checked = all_unchecked = true;

		for (var i = 0; i < document.checkboxForm.elements.length; i++) {
	    		if(document.checkboxForm.elements[i].type == 'checkbox' && document.checkboxForm.elements[i].name == elements){
				if ( document.checkboxForm.elements[i].checked == false ) all_checked = false;
				if ( document.checkboxForm.elements[i].checked == true )  all_unchecked = false;
	    		}
	  	}
	  	if (all_checked == true) {
	  		document.checkboxForm.elements[all_box].checked = true;
	  	}
		else {
			document.checkboxForm.elements[all_box].checked = false;
	  	}
	}
}

// DEPRECATED
function CheckAll (elements) {
	var all_box = 'all-' + elements;
	if (document.getElementById(all_box) != null) {
		var turn_to = document.checkboxForm.elements[all_box];
		for (var i = 0; i < document.checkboxForm.elements.length; i++) {
	    		if(document.checkboxForm.elements[i].type == 'checkbox' && document.checkboxForm.elements[i].name == elements){
	      		document.checkboxForm.elements[i].checked = turn_to.checked;
	    		}
	  	}
	}
}




function showhide(divName) {
	if (document.getElementById) {
		// this is the way the standards work
		var style2 = document.getElementById(divName).style;
		style2.display = style2.display == "block" ? "none":"block";
	}
	else if (document.all) {
		// this is the way old msie versions work
		var style2 = document.all[divName].style;
		style2.display = style2.display == "block" ? "none":"block";
	}
	else if (document.layers) {
		// this is the way nn4 works
		var style2 = document.layers[divName].style;
		style2.display = style2.display == "block" ? "none":"block";
	}
}


function inlinehide(spanName) {
	if (document.getElementById) {
		// this is the way the standards work
		var style2 = document.getElementById(spanName).style;
		style2.display = style2.display == "inline" ? "none":"inline";
	}
	else if (document.all) {
		// this is the way old msie versions work
		var style2 = document.all[spanName].style;
		style2.display = style2.display == "inline" ? "none":"inline";
	}
	else if (document.layers) {
		// this is the way nn4 works
		var style2 = document.layers[spanName].style;
		style2.display = style2.display == "inline" ? "none":"inline";
	}
}

function loadGraph(linkURL) {
	if (document.getElementById) {
		// this is the way the standards work
		var graph_img = document.getElementById('graph_image');
		graph_img.src = linkURL;
	}
	else if (document.all) {
		// this is the way old msie versions work
		var graph_img = document.all['graph_image'];
		graph_img.src = linkURL;
	}
	else if (document.layers) {
		// this is the way nn4 works
		var graph_img = document.layers['graph_image'];
		graph_img.src = linkURL;
	}
}


function toggleID(x) {
	if(!document.getElementById) return false;
	var e = document.getElementById(x);
	(e.style.display == "block") ? e.style.display = "none" : e.style.display = "block";
	return true;
}

function hideID(x) {
	if(!document.getElementById) return false;
	var n = document.getElementById(x);
	n.style.display = "none"; 
	return true;
}

function showID(x) {
	if(!document.getElementById) return false;
	var n = document.getElementById(x);
	if(n.localName == 'TR') { n.style.display = "table-row"; }
	else { n.style.display = "block"; }
	return true;
}

function enableInID(x) {
	if(!document.getElementById) return false;
	var nodes = document.getElementById(x).childNodes; 
	for (var i = 0; i < nodes.length; i++) {
		alert(nodes[i].localName);
		if(nodes[i].localName == "INPUT") {
			nodes[i].disabled = 'false';
		}
	}
}

function disableInID(x) {
	if(!document.getElementById) return false;
	var nodes = document.getElementById(x).childNodes; 
	for (var i = 0; i < nodes.length; i++) {
		alert(nodes[i].localName);
		if(nodes[i].localName == "INPUT") {
			nodes[i].disabled = 'true';
		}
	}
}


function setDisabled(id, disabled)
{
	if ( !document.getElementById || !document.getElementsByTagName) return;

	var nodesToDisable = {button :'', input :'', optgroup :'', option :'', select :'', textarea :''};

	var node, nodes;
	var div = document.getElementById(id);
	if (!div) return;

	nodes = div.getElementsByTagName('*');
	if (!nodes) return;

	var i = nodes.length;
	while (i--) {
		node = nodes[i];
		if ( node.nodeName && node.nodeName.toLowerCase() in nodesToDisable ) {
			node.disabled = disabled;
//			if (node.disabled) { addClass(node, "disabled"); }
//			else { removeClass(node, "disabled"); }
		}
	}
}



function toggle(x)
{
	x.className = (x.className=='show') ? 'hide' : 'show';
}

function toggle2(x)
{
	x.className = (x.className=='showa') ? 'showb' : 'showa';
}

function toggle3(x)
{
	// a -> b -> c -> a ...
	x.className = (x.className=='showa') ? 'showb' : (x.className=='showb') ? 'showc' : 'showa';
}

function toggleHidden(node) {
	// travel up the DOM to the 'hide' DIV
	while (node.className != 'hide' && node.className != 'show' && node.localName != 'BODY') {
		node = node.parentNode;
	}
	if ( node.localName != 'BODY' ) {
		node.className = (node.className=='show') ? 'hide' : 'show';
	}
}

function setFormElementTitle(node, target_element, title_text) {
	var nodes = node.form.elements; 
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == target_element) { 
			nodes[i].title= title_text; 
		} 
	}
}


function getFormElement(node, target_element) {
	var nodes = node.form.elements;
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == target_element) { 
			return nodes[i];
		} 
	}
	return null;
}

function getThisRow(node) {
	// travel up the DOM to this row
	while (node.localName != 'TR' && node.localName != 'BODY') {
		node = node.parentNode;
	}
	if ( node.localName == 'TR' ) {
		return node;
	}
	return null;
}

function addClass (node,className){
	var currentClass = node.className;
	if(!new RegExp(("(^|\\s)" + className + "(\\s|$)"), "i").test(currentClass)){
		node.className = currentClass + ((currentClass.length > 0)? " " : "") + className;
	}
//	alert('add: ' + node.className);
	return node;
}

function removeClass (node, className){
	var classToRemove = new RegExp(("(^|\\s)" + className + "(\\s|$)"), "i");
	node.className = node.className.replace(classToRemove, function (match){
		var retVal = "";
		if(new RegExp("^\\s+.*\\s+$").test(match)){
			retVal = match.replace(/(\s+).+/, "$1");
		}
		return retVal;
	}).replace(/^\s+|\s+$/g, "");
//	alert('del: ' + node.className);
	return node;
}

function hoverToggle(a) {

	nodes = document.getElementById("sidenav_hovers").childNodes;

	// turn off all of the floats first
	var hidden_nodes = "";
	for (var i = 0; i < nodes.length; i++) {
		if ( nodes.item(i).id && nodes.item(i).id.match('_float') ) { nodes.item(i).style.display = 'none'; }
	}
	
	// now try and turn on the element indicated
	if ( a && ! a.id.match('_float') ) {
		var node = document.getElementById(a.id + '_float');
		if(node) { node.style.display = "block"; }
	}
}

//--> 