/*
 * This file contains the javascript used by Sugar on most every page.
 *
 */

/* Popup */
var popup_windows = Array();

function doPopUp( url, name, width, height, features ) {
  if( name != '_new' &&
      popup_windows[name] &&
      !popup_windows[name].closed &&
      popup_windows[name].location ) {
    popup_windows[name].close();
  }

  if( height != 0 && width != 0 ) {
    screenX = screen.availHeight/2 - height/2;
    screenY = screen.availWidth/2 - width/2;

    // Build the feature list.
    feat = features+",height="+height+",width="+width+",screenX="+screenX+",screenY="+screenY;
  } else {
      feat = features;
  }

  
  if (name != '_new') {
    var temp = eval("window.open('"+url+"','"+name+"','"+feat+"');");
    popup_windows[name] = temp;
    if( !popup_windows[name].opener ) popup_windows[name].opener = self;
  } else {
   /* If it is a test system then name the window */
   var windowName = "";
   var temp = eval("window.open('"+url+"','"+windowName+"','"+feat+"');");
  }
  
  temp.focus();
}

function toggleCheckBoxes(button, check)
{
  children = button.parentNode.childNodes;
  for(i = 0;i < children.length; i++)
  {
    if (children[i].type == "checkbox")
      children[i].checked = check;
  }
} 

/*
 * Read in the content of a page into a String
function getPage( pageURL ) 
{
  var data = "";
  is = pageURL.openStream();
  var input = new java.io.BufferedReader(new java.io.InputStreamReader( is ));

  var aLine = "";
  while((aLine = input.readLine()) != null) 
  {
    data += aLine;
  }
  return data;
}
 */

/* CORE-7721 */
function doPopupCategoryCheck(url, name, width, height, features)
{
    if(document.getElementById('queue_id').selectedIndex == -1)
    {
        alert("Please select any of the Queue from the list");
        return false;
    }
    else
    {
        doPopUp( url, name, width, height, features );    
    }
}

/* LoadParent */
/* Due to JS security, this cannot be here. :-( */

// Local Variables:
// mode: java
// End:
