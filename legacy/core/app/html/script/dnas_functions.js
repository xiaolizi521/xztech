// Some Javascript functions specific for DNAS





function killDNASandReload(computer_id,dnas_store_id)
{
  if (navigator.appName == "Microsoft Internet Explorer") {var obj = new ActiveXObject("microsoft.XMLHTTP");} 
  else {var obj = new XMLHttpRequest();}    

  obj.onreadystatechange = function()
  {
    if (obj.readyState == 4)
    {
      var text = obj.responseText;
      if (text == 'none')
      {
        document.getElementById("dnas_storage_info").innerHTML = "No storage info found."
      }
      else
      {
        document.getElementById("dnas_storage_info").innerHTML = text;
      }
    }
  }

  obj.open("GET", "/py/computer/ajax/dnasInfo.pt?get_info=1&kill="+dnas_store_id+"&computer_number=" + computer_id, true);	
  obj.send(null);
  return false;
}



function retrieveDNASinfo(computer_id)
{

  if (navigator.appName == "Microsoft Internet Explorer") {var obj = new ActiveXObject("microsoft.XMLHTTP");} 
  else {var obj = new XMLHttpRequest();}    

  obj.onreadystatechange = function()
  {
    if (obj.readyState == 4)
    {
      var text = obj.responseText;
      if (text == 'none')
      {
        document.getElementById("dnas_storage_info").innerHTML = "No storage info found."
      }
      else
      {
        document.getElementById("dnas_storage_info").innerHTML = text;
      }
    }
  }

  obj.open("GET", "/py/computer/ajax/dnasInfo.pt?get_info=1&computer_number=" + computer_id, true);	
  obj.send(null);
  return false;
}