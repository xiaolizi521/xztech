// Some Javascript functions specific for Virtual Machines VIRT-3

 
function suspendVM(computer_number){
      document.getElementById("vm_suspend").innerHTML="<img height=40 src='/images/juggle-3cascade.gif'>";
      
      if (navigator.appName == "Microsoft Internet Explorer") {
	var obj = new ActiveXObject("microsoft.XMLHTTP");
      } else {
	var obj = new XMLHttpRequest();
      }
      obj.onreadystatechange = function() {
	if (obj.readyState == 4) {

	  var browser_div = document.getElementById("vm_suspend");

	  var text = obj.responseText;
	  browser_div.innerHTML = text + '<br/><a class="text_button" onclick="suspendVM(' + computer_number + ')">Turn VM Off</a>';
	  
	  if (text == 'success')
	  {
	    browser_div.innerHTML = "Virtual Machine successfully suspended and powered off";
	    window.location.reload();
	  } 
	}
      }
      obj.open("GET", "/py/computer/ajax/suspendVM.pt?computer_number=" + computer_number, true);	
      obj.send(null);
      
      return false;
}


function getUUID(requesturl,txtfld)
{
  document.getElementById("discoveruuid").innerHTML = "<center>Asking the virtual center<blink>.</blink></center>";	
  if (navigator.appName == "Microsoft Internet Explorer") {var obj = new ActiveXObject("microsoft.XMLHTTP");} 
  else {var obj = new XMLHttpRequest();}    
  
  obj.onreadystatechange = function()
  {
    if (obj.readyState == 4) 
    {
      var text = obj.responseText;
      if (text == 'not found')
      {
        document.getElementById("discoveruuid").innerHTML = "Sorry, this machine was not found in the virtual center."
      }
      else
      {
	document.getElementById("discoveruuid").innerHTML = "The UUID has been located, click save now.";
	document.getElementById("uuid").value = text;
      }
    }
  }
  
  obj.open("GET", "/py/computer/ajax/genRequest.pt?request_url=" + escape(requesturl), true);
  obj.send(null);	
  return false;
}

function retrieveVmInfo(requesturl)
{
  document.getElementById("getvccinfo").innerHTML = "<center>Asking the virtual center<blink>.</blink></center>";	
  if (navigator.appName == "Microsoft Internet Explorer") {var obj = new ActiveXObject("microsoft.XMLHTTP");} 
  else {var obj = new XMLHttpRequest();}    

  obj.onreadystatechange = function()
  {
    if (obj.readyState == 4)
    {
      var text = obj.responseText;
      if (text == 'not found')
      {
        document.getElementById("getvccinfo").innerHTML = "Sorry, this machine was not found in the virtual center."
      }
      else
      {
        document.getElementById("getvccinfo").innerHTML = text;
      }
    }
  }

  obj.open("GET",  "/py/computer/ajax/genRequest.pt?request_url=" + escape(requesturl), true);
  obj.send(null);
  return false;
}


function getVMNetDisplay(computer_number, panel_type, current_value){

  if (panel_type == 'vmnet_cab_panel'){

    document.getElementById(panel_type).innerHTML = current_value + "&nbsp&nbsp" +
      "<a href='#' onclick='makeEditVMNetPanels(" + computer_number + ',"vmnet_cab_panel","' + current_value + '");' + "return false;'>" + 
	                '<img src="/ui/v2/rui/resources/images/icons/famfam/computer_edit.png" title="Edit Cabinet Panel"></a>';
  }

  if (panel_type == 'vmnet_agg_panel'){

    document.getElementById(panel_type).innerHTML = current_value + "&nbsp&nbsp" +
      "<a href='#' onclick='makeEditVMNetPanels(" + computer_number + ',"vmnet_agg_panel","' + current_value + '");' + "return false;'>" + 
	                '<img src="/ui/v2/rui/resources/images/icons/famfam/computer_edit.png" title="Edit Aggregate Panel"></a>';
  }


}

function saveVMNetPanels(computer_number, panel_type){
  if (navigator.appName == "Microsoft Internet Explorer") {
    var obj = new ActiveXObject("microsoft.XMLHTTP");
  } else {
    var obj = new XMLHttpRequest();
  }
  obj.open("POST", "/py/computer/ajax/vmnetPanels.pt", true);
  obj.setRequestHeader('Content-Type',
                       'application/x-www-form-urlencoded');

  var the_value = document.getElementById(panel_type + "_input").value;

  if (the_value.indexOf('"') >= 0){
    alert ("Double Quotes are illegal in VMNet Panels");
    return;
  }

  var the_post = "computer_number=" + computer_number + "&panel_type=" + panel_type + "&value=" + escape(the_value);

  obj.onreadystatechange = function() {
    if (obj.readyState == 4) {
      getVMNetDisplay(computer_number, panel_type, obj.responseText);
    }
  }

  obj.send(the_post);
  
}


function makeEditVMNetPanels(computer_number, panel_type, current_value){
  // this will load the div given  with an edit field

  document.getElementById(panel_type).innerHTML = '<input type="text" id="' + panel_type + '_input" value="' + current_value + '">' + 
    "<a href='#' onclick='getVMNetDisplay(" + computer_number + ',"' + panel_type + '","' + current_value + '");' + "return false;'>" +
    '<img src="/ui/v2/rui/resources/images/icons/famfam/cancel.png" title="Cancel"></a>' + "&nbsp&nbsp" +
    "<a href='#' onclick='saveVMNetPanels(" + computer_number + ',"' + panel_type + '");' + "return false;'>" +
    '<img src="/ui/v2/rui/resources/images/icons/famfam/database_save.png" title="Save Panel"></a>';

  

}
