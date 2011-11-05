/* Cut-N-Paste JavaScript from ISN Toolbox 
     Copyright 1999, Infohiway, Inc.  Restricted use is hereby
     granted (commercial and personal OK) so long as this code
     is not *directly* sold and the copyright notice is buried
     somewhere deep in your HTML document.  A link to our site
     http://www.infohiway.com is always appreciated of course,
     but is absolutely and positively not necessary. ;-)   */


// This function creates the array; do not modify
function initArray() {
 for (var i = 0; i < initArray.arguments.length; i++)
  this[i] = initArray.arguments[i];
 this.length = initArray.arguments.length;
}

// This is the array for the chrome variations. You may
// modify or add to these options. The fourth entry, "",
// is for no chrome.
var chrome = new initArray(
 "menubar,status",
 "menubar,toolbar,status",
 "menubar,status,resizable,scrollbars",
 "resizable,scrollbars,status",
 "status",
 "");

var popUpWin = '';
var describeIt = '';
var picture = '';
var winFeatures = '';

// This function makes the pop up window. Modify the
// tall and side variables as needed. See the script
// explanation on the Cut and Paste JavaScript site for
// a detailed explanation of this function:
// http://www.infohiway.com/javascript/popup/index.htm
function makePopUpNamedWin(pic,high,wide,text,features,name) {
 var tall = high + 0 // adjust for spacing to border above and below picture
 var side = wide + 0  // adjust for spacing to border on sides of picture
 describeIt = text
 picture = pic
 winFeatures = features
if (popUpWin && !popUpWin.closed) {
  popUpWin.close();
 }
   popUpWin = eval("window.open('"+picture+"','"+name+"','"+chrome[winFeatures]+",height="+tall+",width="+side+",screenX="+(screen.availWidth/2-side/2)+",screenY="+(screen.availHeight/2-tall/2)+"')");
   if (!popUpWin.opener) popUpWin.opener = self;
}
function makeAndReturnPopUpNamedWin(pic,high,wide,text,features,name) {
 var tall = high + 0 // adjust for spacing to border above and below picture
 var side = wide + 0  // adjust for spacing to border on sides of picture
 describeIt = text
 picture = pic
 winFeatures = features
if (popUpWin && !popUpWin.closed) {
  popUpWin.close();
 }
   popUpWin = eval("window.open('"+picture+"','"+name+"','"+chrome[winFeatures]+",height="+tall+",width="+side+",screenX="+(screen.availWidth/2-side/2)+",screenY="+(screen.availHeight/2-tall/2)+"')");
   if (!popUpWin.opener) popUpWin.opener = self;
    return popUpWin;
}

function makePopUpNamedWinNoClose(pic,high,wide,text,features,name) {
 var tall = high + 0 // adjust for spacing to border above and below picture
 var side = wide + 0  // adjust for spacing to border on sides of picture
 describeIt = text
 picture = pic
 winFeatures = features
   var w = eval("window.open('"+picture+"','"+name+"','"+chrome[winFeatures]+",height="+tall+",width="+side+",screenX="+(screen.availWidth/2-side/2)+",screenY="+(screen.availHeight/2-tall/2)+"')");
   if (!w.opener) w.opener = self;
}

function makePopUpWin(pic,high,wide,text,features) {
    makePopUpNamedWin(pic,high,wide,text,features,'COREExtraWin');
}

// ** This function writes the content to the new window. Modify as
// ** needed to suit your purposes. Be sure to include the variables
// ** describeIt and picture to capture the content passed from the
// ** links on your pages.

function update() {
   popUpWin.document.open();
   // content for the popup window is defined here
   //popUpWin.document.write("<html><head><title>Cut and Paste JavaScript!</title></head>");
   //popUpWin.document.write("<body><center><strong>" + describeIt + "</strong><p>");
   //popUpWin.document.write("<img src=" + picture + "><br>");
   //popUpWin.document.write("<font size=-1>&copy;1997 Dave Gibson</font><p>");
   //popUpWin.document.write("<a href='#' onClick='self.close()'>");
   //popUpWin.document.write("Return To Cut and Paste!</a></center></body></html>");
   popUpWin.document.close();
}

// -->

/* The href tag calls the function to make the pop up window.
     The parameters for this application are:
     1. The url of the image to be displayed. Change imageUrl to
        your url.
     2. The height of the image. Change as required.
     3. The width of the image. Change as required.
     4. Text to display in the pop up window.
     5. Chrome option for the pop up window. See chrome array above.

<a href="javascript:makePopUpWin('imageUrl',180,200,'text to display in pop up window',0)"></a>
*/

function NameValue(name, value) {
    /* An array of NameValue objects is passed to postPopup() */
    this.name = name
    this.value = value
}

function postPopup(uri, post_args) {
    /* post_args is an array of NameValue objects */
    if (navigator.appName == "Microsoft Internet Explorer") {
        var obj = new ActiveXObject("microsoft.XMLHTTP");
    } else {
        var obj = new XMLHttpRequest();
    }
    obj.open("POST", uri, false);
    obj.setRequestHeader('Content-Type',
                        'application/x-www-form-urlencoded');

    var value = "";
    var postvalue = "";
    for (var i = 0; i < post_args.length; i++) {         
        if (i != 0) {
            postvalue += "&";
        }
        postvalue += post_args[i].name;
        postvalue += "=";
        postvalue += escape(post_args[i].value);
    }
    obj.send(postvalue);

    popup = window.open("", "preview", "scrollbars=yes");
    popup.document.write(obj.responseText);
}

// Local Variables:
// mode: java
// End:
