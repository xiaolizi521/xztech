/* Loadable Divs
 *
 * Loadable divs are the end-all to web design. They do everything. There are now
 * two kinds of loadable divs. The basic loadable div (called using loaddiv) will
 * just get a url and put the data in an element.
 * 
 * The second is more advanced. the loaddiv_advanced also takes an associative
 * array of arguments to pass to the server in a POST method. This is used for
 * advanced epaper.
 *
 * The third argument is a message (text) you want to appear while the request
 * is being processed.
 *
 * The fourth argument is the name of a div where you want the message ( third argument )
 * to appear.  If you don't specify the fourth argument, the default div used
 * is the element_id div to display the message.
 * 
 */

function loaddiv(uri, element_id, message, message_div) {
  /*
   * Basic loadble div -- takes a url and an element id. Loads the uri, puts the
   * resulting HTML in the element.
   */
  if (navigator.appName == "Microsoft Internet Explorer") {
    var obj = new ActiveXObject("microsoft.XMLHTTP");
  } else {
    var obj = new XMLHttpRequest();
  }
  obj.open("GET", uri, true);
  obj.onreadystatechange = function() {
    if (obj.readyState == 4) {
      var browser_div = document.getElementById(element_id);
      var text = obj.responseText;
      browser_div.innerHTML = text;
      if ( message_div ) {
        message_div.innerHTML = '';
      }
    }
  }
  if (message) {
    if ( message_div != undefined ) {
        var message_div = document.getElementById(message_div);
        message_div.innerHTML = '<p style="text-align: center; cursor: wait">'+message+'</p>';
    } else {
        var browser_div = document.getElementById(element_id);
        browser_div.innerHTML = '<p style="text-align: center; cursor: wait">'+message+'</p>';
    }
  }
  obj.send(null);
}

function loaddiv_post(uri, element_id, arguments) {
  /*
   * Advanced loadable div -- works like the regular loadable div
   * we've come to love, but also takes a list of arguments to use
   * for an HTTP Post.
   */
  
  if (navigator.appName == "Microsoft Internet Explorer") {
    var obj = new ActiveXObject("microsoft.XMLHTTP");
  } else {
    var obj = new XMLHttpRequest();
  }
  obj.open("POST", uri, true);
  obj.setRequestHeader('Content-Type',
                       'application/x-www-form-urlencoded');
  
  var value = "";
  var postvalue = "";
  for (var i in arguments) {
    if (i != 0) {
      postvalue += "&";
    }
    postvalue += arguments[i].name;
    postvalue += "=";
    postvalue += escape(arguments[i].value);
  }
  
  obj.onreadystatechange = function() {
    if (obj.readyState == 4) {
      var browser_div = document.getElementById(element_id);
      var text = obj.responseText;
      browser_div.innerHTML = text;
    }
  }
  
  obj.send(postvalue);
}

function loaddiv_post2(uri, element_id, names, values) {
  /* Similar to above, except it accepts two lists of arguments,
   *  one for names, and the other for the corresponding values
   *  to send in a HTTP post.
   */
 if (navigator.appName == "Microsoft Internet Explorer") {
    var obj = new ActiveXObject("microsoft.XMLHTTP");
  } else {
    var obj = new XMLHttpRequest();
  }
  obj.open("POST", uri, true);
  obj.setRequestHeader('Content-Type',
                       'application/x-www-form-urlencoded');

  var value = "";
  var postvalue = "";
  for (var i in names) {
    if (i != 0) {
      postvalue += "&";
    }
    postvalue += names[i];
    postvalue += "=";
    postvalue += escape(values[i]);
  }

  obj.onreadystatechange = function() {
    if (obj.readyState == 4) {
      var browser_div = document.getElementById(element_id);
      var text = obj.responseText;
      browser_div.innerHTML = text;
    }
  }

  obj.send(postvalue); 
}

function loaddiv_data(uri, element_id, elements) {
  /*
   * A way of automating the advanced loadable div. You pass in a list of
   * element ids to elements. The system will grab those elements and use
   * their names and values as arguments for an advanced loadable divs.
   */
  
    var arguments = new Array();
    var element = "";
    var index = 0;
    for (var i = 0; i < elements.length; i++) {
        element = document.getElementById(elements[i]);
        if (element != null) {
            arguments[index] = element;
            index += 1;
        }
        else {
            collection = document.getElementsByName(elements[i]);
            name = elements[i];
            for (var j = 0; j < collection.length; j++) {
                if (collection[j].checked == true) {
                    arguments[index] = {name: name, value: collection[j].value};
                    index += 1;
                }
            }
        }
    }
    loaddiv_post(uri, element_id, arguments);
}

function loaddiv_post_function(uri, element_id, arguments, ref_func) {
  /*
   * Similar to loaddiv_post, but this adds in a third parameter
   * that allows you to pass a function name to execute after
   * the div has loaded.
   */
  
  if (navigator.appName == "Microsoft Internet Explorer") {
    var obj = new ActiveXObject("microsoft.XMLHTTP");
  } else {
    var obj = new XMLHttpRequest();
  }
  obj.open("POST", uri, true);
  obj.setRequestHeader('Content-Type',
                       'application/x-www-form-urlencoded');
  
  var value = "";
  var postvalue = "";
  for (var i in arguments) {
    if (i != 0) {
      postvalue += "&";
    }
    postvalue += arguments[i].name;
    postvalue += "=";
    postvalue += escape(arguments[i].value);
  }
  
  obj.onreadystatechange = function() {
    if (obj.readyState == 4) {
      var browser_div = document.getElementById(element_id);
      var text = obj.responseText;
      browser_div.innerHTML = text;
      ref_func();
    }
  }
  
  obj.send(postvalue);
}

function loaddiv_data_function(uri, element_id, elements, ref_func) {
  /**
   *  This function works like load_div_data, but uses
   *  loaddiv_post_function instead.
   */

    var arguments = new Array();
    var element = "";
    var index = 0;
    for (var i = 0; i < elements.length; i++) {
        element = document.getElementById(elements[i]);
        if (element != null) {
            arguments[index] = element;
            index += 1;
        }
        else {
            collection = document.getElementsByName(elements[i]);
            name = elements[i];
            for (var j = 0; j < collection.length; j++) {
                if (collection[j].checked == true) {
                    arguments[index] = {name: name, value: collection[j].value};
                    index += 1;
                }
            }
        }
    }
    loaddiv_post_function(uri, element_id, arguments, ref_func);
}
