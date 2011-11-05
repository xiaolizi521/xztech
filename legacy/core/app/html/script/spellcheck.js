/* Spell Checker - scott.simpson@rackspace.com
 * This javascript makes an AJAX request to a python library.  The
 * python library then returns a JSON response with the words which 
 * are spelled wrong.  Much of the rest of this is display oriented
 * to provide a nice user interface.
 */

var req;
var words = "";
var correct = "";
var fixword = new Array(3);
var last = 0;

//bug #167801 @ mozilla.org for FF v1.0-1.5
var ta = document.getElementsByTagName("TEXTAREA");
if(ta.length > 0)
{
    ta[0].focus();
}

function spellCheck( element_name ){
    url="/py/spellcheck.pt"; 
    param = "data=" + encodeURIComponent(document.getElementById(element_name).value);
    //alert(param);
    loadJSONDoc(url, param, element_name);
}

/* this function makes a get request to a url with parameters 
 * in the url string.  handles the response with the handleJson()
 * function
 */
function loadJSONDoc(url, param, comment_field) {
	req = false;
    // branch for native XMLHttpRequest object
    if(window.XMLHttpRequest) {
    	try {
			req = new XMLHttpRequest();
        } catch(e) {
			req = false;
        }
    // branch for IE/Windows ActiveX version
    } else if(window.ActiveXObject) {
       	try {
        	req = new ActiveXObject("Msxml2.XMLHTTP");
      	} catch(e) {
        	try {
          		req = new ActiveXObject("Microsoft.XMLHTTP");
        	} catch(e) {
          		req = false;
        	}
		}
    }
	if(req) {
		req.onreadystatechange = function () { handleJson( comment_field) };
		req.open("POST", url, true);
        req.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		req.send(param);
	}
}

/* handles the response from the XMLHttpRequest object, instead
 * of using an XML response, it hands it back in JSON format.
 */
function handleJson( comment_field ){
    if(req.readyState == 4){
        document.getElementById('waiting').style.visibility = "hidden";
        if(req.status == 200){
            var resp =  req.responseText;
            //alert(resp);
            var func = new Function("return " + req.responseText);
            var objt = func();
            // the block that contains our spellcheck doohicky
            var status = objt[0];
            words = objt[1];
            correct = objt[2];
            properties = objt[3];
            //if there are mispelled words, we have to build up the div that displays the spellcheck functionaliy.
            if (status == -1) {
                build_checker(comment_field);
            } 
            //no mispellings (at least the words used are spelled correctly, doesn't mean they are the correct words)
            else if (status == 1) {
                alert("Spellcheck is complete");
                exit(1);
            } 
            //something happend with the python interface, not the javascript.
            else {
                alert("There was an internal error");
                exit(1);
            }            
        }
        //something happened while talking to the python interface.
        else {
            alert(req.status + ": " + req.statusText);
            exit(1);
        }
    }//end outer if
    else {
        document.getElementById('waiting').style.visibility = "visible";
        //do some little waiting thingy
    }   
}

function build_checker(comment_field) {
    //show the spell checker layer.
    var div = document.getElementById("spellcheck");
    var usermsg = new String(document.getElementById(comment_field).value);
    var displaymsg = new String();
    var pos = 0;
    div.style.visibility = "visible";

    //hide the select boxes, they will display over browser elements since they are system level attributes
    var to_hide = document.getElementsByTagName("SELECT");
    for( var i = 0; i < to_hide.length; i++) {
        to_hide[i].style.visibility = "hidden";
    }

    usermsg = usermsg.replace(/\n/g, "<br/>");
    // to get a correct word for a misspelled word: correct[words[i]][0]
    for( var i = 0; i < words.length; i++ ) {
        var word = words[i].replace(/'/, "&apos;");
        var index = usermsg.indexOf(word, pos);
        displaymsg += usermsg.substr(pos, index-pos) + "<span onClick='suggestWords(\"" + words[i] + "\", " + index + "); return true' class='misspell' id=\"" + word + "_" + index + "\" >" + word + "</span>";
        pos = usermsg.indexOf(words[i], pos) + words[i].length;
    }
    if (pos < usermsg.length) {
        displaymsg += usermsg.substr(pos, usermsg.length);
    }
    document.getElementById("usertext").innerHTML = displaymsg;
}

function cancelSpellCheck() {
    document.getElementById("spellcheck").style.visibility = "hidden";
    document.getElementById('waiting').style.visibility = "hidden";

    //reset the div elements in the layer.
    document.getElementById("suggest").innerHTML = "";
    document.getElementById("usertext").innerHTML = "";
    
    //unhide the select boxes
    var to_hide = document.getElementsByTagName("SELECT");
    for( var i = 0; i < to_hide.length; i++) {
        to_hide[i].style.visibility = "visible";
    }
    
    //reset the globals used.
    fixword = new Array(3);
    words = "";
    correct = "";
}

function hideSpellCheck(comment_field) {
    document.getElementById("spellcheck").style.visibility = "hidden";
    document.getElementById('waiting').style.visibility = "hidden";
    //replace all of the remaining spans.  they would show up if put into the form field
    var ut_div = document.getElementById("usertext");
    for (var x = 0; x < ut_div.childNodes.length; x++) {
        if (ut_div.childNodes[x].tagName == "SPAN") {
            var child_span = ut_div.childNodes[x];
            ut_div.replaceChild(child_span.firstChild, child_span);
        }
        else if (ut_div.childNodes[x].tagName == "BR") {
            var child_span = ut_div.childNodes[x];
            ut_div.replaceChild(document.createTextNode("\n"), child_span);
        }
    }
    //fix some entity types
    var usertext = document.getElementById("usertext").innerHTML;
    usertext = usertext.replace(/&gt;/, char(188));
    usertext = usertext.replace(/&lt;/, '<');
    usertext = usertext.replace(/&amp;/, '&');
    document.getElementById("usertext").innerHTML = usertext;
    //alert(usertext);
    
    //save the corrected entry to the form field.
    document.getElementById(comment_field).value = document.getElementById("usertext").innerHTML;
    
    //reset the div elements in the layer.
    document.getElementById("suggest").innerHTML = "";
    document.getElementById("usertext").innerHTML = "";
    
    //unhide the select boxes
    var to_hide = document.getElementsByTagName("SELECT");
    for( var i = 0; i < to_hide.length; i++) {
        to_hide[i].style.visibility = "visible";
    }
    
    //reset the globals used.
    fixword = new Array(3);
    words = "";
    correct = "";
}

function suggestWords(word, index) {
    var wordlist = "";
    for(var i = 0; i < correct[word].length; i++) {
        wordlist += "<span style='line-height:1.4;' id='" + word + i + "' onmouseover=\"this.className='over'\" onmouseout=\"this.className='notover'\" onclick=\"setWord('" + word + "', " + i + ", " + index + "); \">" + correct[word][i] + "</span><br/>";
    }
    document.getElementById("suggest").innerHTML = wordlist;
}

function setWord(word, replace, index) {
    //turn off borders for the last clicked item.
    document.getElementById(word + last).style.border="none";
    //set the style to clicked
    document.getElementById(word + replace).className='clicked'; 
    //put a border around it.
    document.getElementById(word + replace).style.border="1px solid black";
    //set the last clicked item to this one.
    last = replace;
    //save the word as the one they want to replace.
    fixword[0] = word;
    fixword[1] = correct[word][replace];
    fixword[2] = index;
} 

function replaceWord() {
    if (fixword[0]) {
        //get the div the text is in
        var ut_div = document.getElementById("usertext");
        //get the span that our word is in
        var word_span = document.getElementById(fixword[0] + "_" + fixword[2]);
        //replace the word in the span with the new one.
        word_span.innerHTML = fixword[1];
        //replace the whole span with the value inside the span.
        ut_div.replaceChild(word_span.firstChild, word_span);
        //reset the fixword array.
        fixword = new Array(3);
        document.getElementById("suggest").innerHTML = "";
    }
}

function replaceAllWords() {
    if (fixword[0]) {
        //get the div the text is in
        var ut_div = document.getElementById("usertext");
        for (var x = 0; x < ut_div.childNodes.length; x++) {
            if (ut_div.childNodes[x].tagName == "SPAN") {
                if (ut_div.childNodes[x].innerHTML.match(fixword[0])) {
                    var child_span = ut_div.childNodes[x];
                    child_span.innerHTML = fixword[1];
                    ut_div.replaceChild(child_span.firstChild, child_span);
                }
            }
        }
    } 
    fixword = new Array(3);
    document.getElementById("suggest").innerHTML = "";
}

function removeElement(divNum) {
  var d = document.getElementById('usertext');
  var olddiv = document.getElementById(divNum);
  var val = olddiv.value;
  d.removeChild(olddiv);
  return olddiv.value;
}
