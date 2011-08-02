function getHTTPObject() {
	var xmlhttp = false;
	if (typeof XMLHttpRequest != 'undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		} catch (e) {
			xmlhttp = false;
		}
	} else {
		/*@cc_on
		@if (@_jscript_version >= 5)
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		@end @*/
	}
	return xmlhttp;
}

function login(form) {
	var username = document.getElementById("preauth-username").value;
	var password = document.getElementById("preauth-password").value;

	var http = getHTTPObject();
//	var url = "https://" + form.action.substr(8);
	var url = location.protocol + "//"+ location.hostname +"/home.php";

	
	http.open("get", url, false, username, password);
	http.send("");
	
//	alert("login() response from "+ url +" was "+ http.status +" for '"+ username +"', '"+ password +"'");

	
	if (http.status == 200) {
		document.location = url;
	} 
	else if (http.status == 401) {
        	alert("Invalid username and/or password!");
	}
//	else {
//		alert("Error code: "+ http.status);
        	//alert("Incorrect username and/or password!");
//	}
	return false;
}

function ielogout() {

	if (navigator.appName == "Microsoft Internet Explorer") {
		var flushed = document.execCommand("ClearAuthenticationCache");
		if (flushed) {
//			alert("Credentials succesfully flushed! If you press Reload you must relogin.");
		}
		else {
//			alert("Credentials not flushed! Are you running Internet Explorer 6.0 SP1 or later?");
		}
	}
	else {
//		alert("Not running Internet Explorer 6.0 SP1 or later!");
	}

}

function nocookieswarn() {
	alert("You do not have cookies enabled. \nIn order to login again, you must 'Cancel' the Authentication Window which appears, then use the Login Page to re-authenticate.");
}

function logout() {

	if (navigator.appName == "Microsoft Internet Explorer") {
		var flushed = document.execCommand("ClearAuthenticationCache");
		if (flushed) {
//			alert("Credentials succesfully flushed! If you press Reload you must relogin.");
		}
		else {
//			alert("Credentials not flushed! Are you running Internet Explorer 6.0 SP1 or later?");
		}
	}
	else {
//		alert("Not running Internet Explorer 6.0 SP1 or later!");
	}

//	document.execCommand('ClearAuthenticationCache');
	
	// make the HTTP Auth fail, sending a 
	var http = getHTTPObject();
//	var url = location.href;
	var url = location.protocol + "//"+ location.hostname +"/logout.php";
	
	http.open("get", url, false, 'logout_username', 'logout_password');
	http.send("");
	alert("logout() response from "+ url +" was "+ http.status +" for 'logout_username', 'logout_password'");

	if (http.status == 401) {
		document.location = "https://"+location.hostname +"/";
	}

}