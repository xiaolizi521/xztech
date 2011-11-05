


function testforupgradeactions(computer_number,upgrade_state){
       
      document.getElementById("upgrade_info").innerHTML="<table border=\"0\"><tr><td align='center'>" + 
                                                        "<span style=\"font-size:11px;\">Querying Activities</span>" + 
	                                                "</td></tr>" +
	                                                "<tr><td align='center'><img height=40 src='/images/juggle-3cascade.gif'>" + 
	                                                "</td></tr></table>";
      
      if (navigator.appName == "Microsoft Internet Explorer") {
	var obj = new ActiveXObject("microsoft.XMLHTTP");
      } else {
	var obj = new XMLHttpRequest();
      }
      obj.onreadystatechange = function() {
	if (obj.readyState == 4) {

	  var browser_div = document.getElementById("upgrade_info");
	  var text = obj.responseText;
	  
	  if (text == 'none')
	  {
	    browser_div.innerHTML = "";
	  } 
	  else
	  {
	    browser_div.innerHTML = "The following actions will take place if you choose to continue upgrading:<BR><BR>" + text;
	    document.forms[0].action = "/py/computer/changeStatus.pt";
	  }
	}
      }
      obj.open("GET", "/py/activities/ajax/provisioningData.pt?test_provisioning_for_device="+computer_number+"&new_status="+upgrade_state+"&type=upgrade", true);	
      obj.send(null);
      
      return false;
}

function testfordowngradeactions(computer_number,downgrade_state){
      document.getElementById("downgrade_info").style.display = 'block';

      document.getElementById("downgrade_info").innerHTML="<img height=40 src='/images/juggle-3cascade.gif'>";
      
      if (navigator.appName == "Microsoft Internet Explorer") {
	var obj = new ActiveXObject("microsoft.XMLHTTP");
      } else {
	var obj = new XMLHttpRequest();
      }
      obj.onreadystatechange = function() {
	if (obj.readyState == 4) {

	  var browser_div = document.getElementById("downgrade_info");
	  var text = obj.responseText;
	  
	  if (text == 'none')
	  {
	    browser_div.innerHTML = "";
	    browser_div.style.display = 'none';
	  } 
	  else
	  {
	    browser_div.innerHTML = "The following actions will take place if you choose to continue downgrading:<BR><BR>" + text;
	    //document.forms[0].action = "/py/computer/changeStatus.pt";
	  }
	}
      }
      obj.open("GET", "/py/activities/ajax/provisioningData.pt?test_provisioning_for_device="+computer_number+"&new_status="+downgrade_state+"&type=downgrade", true);	
      obj.send(null);
      
      return false;
}
