var msg = "";
var isInt = "^[0-9]+$"; 
var isStr = "^[a-zA-Z]+$";
var isDate = "^[0-9]+[-][01][0-9][-][0123][0-9]$";
var isAlphaNumeric = "^[a-zA-Z0-9]+$";
var isCustomerSearch = "^[0-9]+$|^[0-9]+[-][0-9]+$";
var isCurrency = "^[0-9]+[.][0-9]+$";

function checkAddFirewallModel() {
   with(document.fAddFirewallModel) {
   	if (model.value == "") {
		msg = "Please enter a model.";
		alert(msg);
		model.focus();
		return (false);
	}
   }
}
function checkAddFirewall() {
   with(document.fAddFirewall) { 
	if (!serialNumber.value.match(isAlphaNumeric)) {
		msg = "Please enter a valid serial number.";
           	alert(msg);
		serialNumber.focus();
		return (false);
	}
	
	if (model.value == "") {
		msg = "No models exist. Please add a model to the database.";
		alert(msg);
		model.focus();
		return (false);
	}
   }
}

function globalCustomerSearch() {
   with(document.fGlobalSearch) {
	if (!globalSearch.value.match(isCustomerSearch)) {
		msg = "Search for a customer, ex. 4, or customer server, ex. 4-1.";
		alert(msg);
		globalSearch.focus();
		return (false);
	}
   }
}

function checkBasicSearch() {
   with(document.fSearch) {
	if (!customerNumber.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		customerNumber.focus();
		return (false);
	}

	if (!customerServer.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		customerServer.focus();
		return (false);
	}
   }
}

function checkProvisionFirewall() {
   with(document.fProvisionFirewall) {

	if (!customerNumber.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		customerNumber.focus();
		return (false);
	}

	if (!customerServer.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		customerServer.focus();
		return (false);
	}

	if (serialNumber.value == "") {
		msg = "No firewalls are in the inventory.";
		alert(msg);
		serialNumber.focus();
		return (false);
	}
   }
}

function checkEditFirewall() {
   with(document.fEditFirewall) {
	if (rootPassword.value == "") {
		msg = "You must enter a valid password.";
		alert(msg);
		rootPassword.focus();
		return (false);
        }

	if (adminPassword.value == "") {
		msg = "You must enter a valid password.";
		alert(msg);
		adminPassword.focus();
		return (false);
        }

	if (!port.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		port.focus();
		return (false);
	}

	if (!mrc.value.match(isCurrency)) {
		msg = "Please enter in currency format, ex 9.99.";
		alert(msg);
		mrc.focus();
		return (false);
	}
   }
}

function checkReplaceFirewall() {
   with(document.fReplace) {
	
	if (newFirewall.value == "") {
		msg = "No firewalls are available. Please add more to the inventory";
           	alert(msg);
		newFirewall.focus();
		return (false);
	}

   }
}

function checkSearchByCustomer() {
   with(document.fSearchByCustomer) {
	if (!customer.value.match(isInt)) {
		msg = "Only integers are allowed in this field.";
		alert(msg);
		customer.focus();
		return (false);
        }
   }
}

function checkSearchByFirewall() {
   with(document.fSearchByFirewall) {
   	if (firewall.value == "") {
		msg = "Please enter a valid serial number.";
		alert(msg);
		firewall.focus();
		return (false);	
	}
   }
}

function checkSearchByDate() {
   with(document.fSearchByDate) {
	if (!date.value.match(isDate)) {
		msg = "Dates should be entered in the format: yyyy-mm-dd.";
		alert(msg);
		date.focus();
		return (false);
	}
   }
}

function checkSearchByDateRange() {
   with(document.fSearchByDateRange) {
	if (!beginDate.value.match(isDate)) {
		msg = "Dates should be entered in the format: yyyy-mm-dd.";
		alert(msg);
		beginDate.focus();
		return (false);
	}

	if (!endDate.value.match(isDate)) {
		msg = "Dates should be entered in the format: yyyy-mm-dd.";
		alert(msg);
		endDate.focus();
		return false;
	}
   }

}
