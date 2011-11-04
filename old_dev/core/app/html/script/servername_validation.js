function isServerNameValid(namesList) {
   if(document.getElementById("isServerNameChanged").value == "false") {
       return true;
   }
   e = document.getElementById("usestdname");
   if(e.value == "yes") {
       field = document.getElementById("server_name_std");
   }
   else {
       field = document.getElementById("server_name_nostd");
   }
    
    if(checkComputerName(field.value,namesList) == false) {
        var cont = confirm("This Server Name is Invalid, would you like to continue with it?");
        if(cont == false) {
            field.select();
            return false; 
        }
    }
    else {
            return true;
    }
}

function checkComputerName(value) {
    var names = new Array();
    names = namesList.split("\t");
    //Same logic as it was previously in php
    var parts  = value.split(".");
    var len = parts.length;
    for(var i=0;i<len;i++) {
            // Precaution for ..org or domain. cases
                if(parts[i]=="") {
                            return false;
                                }
    }
    var res = names.indexOf(parts[len-1]);
    if((len>1) && (res!=-1)) {
            // .com, .to, etc.
                return true;
    }
    if (len>2) {
            // .co.uk, .com.au, etc.
                var tld = parts[len-2]+ "."+parts[len-1];
                    if(names.indexOf(tld)!=-1) {
                                return true;
                                    }
    }
    return false;
}

function serverNameChanged() {
    document.getElementById("isServerNameChanged").value = "true";
    }                    
