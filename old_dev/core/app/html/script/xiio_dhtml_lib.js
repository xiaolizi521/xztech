/*
Author: William La Morte &copy; copyright 2000
Email: wlm@xiio.com
http://www.xiio.com
There are no requirements for using this API. I only ask that you leave this comment intact.
This API was written to assist with my own works as well assist other developers with their work.
If you improve or enhance this script, please place your name and date below this comment
*/

/*
Improved by:
Date:
*/

//handle browser resizing problem for navigator 4
if(!window.orig_width) {
  window.onresize = reset_layers;
  window.orig_width = window.innerWidth;
  window.orig_height = window.innerHeight;
}

function reset_layers() {
    if (window.innerWidth != orig_width || window.innerHeight != orig_height) {
      location.reload();
    }
}

//declare globals to build object reference
var whichDom = "", rightDom = "", styleObj = ""
var isNav4, isNav6, isIE
var isBrand = navigator.appName
var agt = navigator.userAgent.toLowerCase()
var navVer = parseInt(navigator.appVersion)

isNav4 = (isBrand == "Netscape" && navVer < 5) ? true : false
isNav6 = (isBrand == "Netscape" && navVer >= 5) ? true : false

isNav46 = ((isBrand == "Netscape") && (parseFloat(navigator.appVersion) >= parseFloat(4.6)) && (parseFloat(navigator.appVersion) < parseFloat(4.7))) ? true : false
isNav47 = ((isBrand == "Netscape") && (parseFloat(navigator.appVersion) >= parseFloat(4.7))) ? true : false

isIE = ((agt.indexOf("msie") != -1) && (parseInt(navVer) >= 4)) ? true : false

//construct object reference
var doc = document
var layers = doc.layers
var all = doc.all

if (layers)  {
  whichDom = '["'
  rightDom = '"]'
} else if (all)  {
  whichDom = '.all.'
  styleObj = '.style'
} else {
  whichDom = '.getElementById("'
  rightDom = '")'
  styleObj = '.style'
}

//API object reference
function setObject(obj) {
var theObj
	if (typeof obj == "string")
		theObj = eval("document" + whichDom + obj + rightDom + styleObj)
	else
		theObj = obj
		return theObj
}

function objectExists(obj) {
    if (typeof obj == "string")
	return eval("document" + whichDom + obj + rightDom);
    else
	return obj;
}

//nav6 returns string values for positionable elements ie. left "100px"
//must parse string values and convert to number values
//move an object along the X axis
function moveXAxis(obj, x)	{
var theObj = setObject(obj)
var theLeft = getObjLeft(theObj)
newLeft = (parseInt(theLeft) + x)
  if (layers)	{//ok
		theObj.left = newLeft
  } else  {//ok IE and nav6
    theObj.left = (newLeft +"px")//convert back to string
  }
}

//move an object along the Y axis
function moveYAxis(obj, y)	{
var theObj = setObject(obj)
var theTop = getObjTop(theObj)
newTop = (parseInt(theTop) + y)
  if (layers)	{//ok
		theObj.top = newTop
  } else  {//ok IE and nav6
    theObj.top = (newTop +"px")
  }
}

function moveObjTo(obj, x, y) {
var theObj = setObject(obj)
	if (layers) {
		theObj.moveTo(x,y)
	} else if (all) {
		theObj.pixelLeft = x
		theObj.pixelTop = y
	} else	{//nav6
		theObj.left = x +"px"
		theObj.top = y +"px"
	}
}

function moveObjBy(obj, deltaX, deltaY) {
var theObj = setObject(obj)
  if (layers) {
    theObj.moveBy(deltaX, deltaY)
  } else if (all) {
    theObj.pixelLeft += deltaX
    theObj.pixelTop += deltaY
  } else  {
    var theLeft = getObjLeft(theObj)
    var theTop = getObjTop(theObj)
    setObjLeft(theObj, parseInt(theLeft)+deltaX)
    setObjTop(theObj, parseInt(theTop)+deltaY)
  }
}

function setZIndex(obj, z) {
var theObj = setObject(obj)
	theObj.zIndex = z
}

function getZIndex(obj)	{
var theObj = setObject(obj)
	return theObj.zIndex
}

function setBGColor(obj, color) {
var theObj = setObject(obj)
	if (layers) {//ng
		theObj.bgColor = color
	} else {
		theObj.backgroundColor = color//ok IE and nav6
    //beware nav6 returns rgb values ie rgb(255 255 255) when
		//specifying hexidecimal value
    //but returns colornames ie blue
	}
}

function getBGColor(obj)	{
var theObj = setObject(obj)
	if (layers)	{//buggy
		return theObj.bgColor
	} else	{//IE returns named value or hexadecimal
		return theObj.backgroundColor
		//nav6 returns rgb(255 255 255)!!!
	}
}

function setBorderColor(obj, color)  {
var theObj = setObject(obj)//ng nav4
  theObj.borderColor = color//ok IE and nav6
}

//netscape 6 returns parameters for all four borders
function getBorderColor(obj)	{
var theObj = setObject(obj)
	return theObj.borderColor
}

function show(obj) {
var theObj = setObject(obj)
	theObj.visibility = "visible"
}

function hide(obj) {
var theObj = setObject(obj)
	theObj.visibility = "hidden"
}

function expand(obj) {
var theObj = setObject(obj)
        theObj.display = "block"
}

function collapse(obj) {
var theObj = setObject(obj)
        theObj.display = "none"
}

//nav4 reads and returns value of "show" but also reads "visible"
//nav4 reads and returns value of "hide" but also reads "hidden"
//test???
function getObjVisibility(obj)	{
var theObj = setObject(obj)
if (theObj.visibility == "" || theObj.visibility == null)
return "visible"
else return theObj.visibility
}

function setObjPosition(obj,l,t)  {//same as moveObjTo()
var theObj = setObject(obj)
  if (layers) {
    theObj.left = l
    theObj.top = t
  } else if (all)  {
    theObj.pixelLeft = l
    theObj.pixelTop = t
  } else  {//nav6
    theObj.left = l + "px"
    theObj.top = t + "px"
  }
}

//nav6 returns string value ie left = 100px
//parse values???
function getObjLeft(obj)  {
var theObj = setObject(obj)
  if (layers) {
    return theObj.left
  } else if (all)  {
    return theObj.pixelLeft
  } else  {
    return parseInt(theObj.left)
  }
}

function setObjLeft(obj, l)	{
var theObj = setObject(obj)
	if (layers)	{
		theObj.left = l
	} else if (all)	{
		theObj.pixelLeft = l
	} else  {//nav6
    theObj.left = l + "px"
  }
}

function getObjTop(obj)  {
var theObj = setObject(obj)
  if (layers) {
    return theObj.top
  } else if (all)  {
    return theObj.pixelTop
  } else  {
    return parseInt(theObj.top)
  }
}

function setObjTop(obj, t)	{
var theObj = setObject(obj)
	if (layers)	{
		theObj.top = t
	} else if (all)	{
		theObj.pixelTop = t
	} else  {//nav6
    theObj.top = t + "px"
  }
}

function getObjHeight(obj) {
var theObj = setObject(obj)
	if (layers) {
		return theObj.clip.height
	} else if (all) {
		return theObj.pixelHeight
	} else	{//nav6
		return parseInt(theObj.height)
	}
}

//set dimensions in one shot
//ie4 and nav6 only
function setObjDimensions(obj,h,w)	{
var theObj = setObject(obj)
	if (layers)	{//does not work nav4
		theObj.height = h
		theObj.width = w
	} else if	(all)	{
		theObj.pixelHeight = h
		theObj.pixelWidth = w
	} else	{
		theObj.height = h + "px"
		theObj.width = w + "px"
	}
}

function setObjHeight(obj,h)	{
var theObj = setObject(obj)
	if (layers)	{//does not work nav4
		theObj.height = h
	} else if (all)	{
		theObj.pixelHeight = h
	} else  {//nav6
    theObj.height = h + "px"
  }
}

function getObjWidth(obj) {
var theObj = setObject(obj)
	if (layers) {
		return theObj.clip.width
	} else if (document.all) {
		return theObj.pixelWidth
	} else	{//nav6
		return parseInt(theObj.width)
	}
}

function setObjWidth(obj,w)	{
	var theObj = setObject(obj)
	if (all)	{//does not work nav4
		theObj.pixelWidth = w
	} else	{
		theObj.width = w
	}
}

function getInsideWindowWidth() {//ok
	if (all) {
		return document.body.clientWidth
	} else {
		return window.innerWidth
	}
}

function getInsideWindowHeight() {//ok
	if (all) {
		return document.body.clientHeight
	} else {
		return window.innerHeight
	}
}

function getPageLeft(obj)	{
var theObj = setObject(obj)
	if (all)	{
  return theObj.offsetLeft
	}	else	{
	return theObj.pageX
	}
}

function getPageTop(obj)	{
var theObj = setObject(obj)
	if (all)	{
  return theObj.offsetTop
	}	else	{
	return theObj.pageY
	}
}

//showHide() is a utility function using the custom javascript API
//usage: showHide('objectName', '[show | hide | toggle]')
function showHide()	{
var args = arguments
	for (var i = 0; i < args.length; i += 2)	{
	if (typeof args[i] == "string")
		var theObj = setObject(args[i])
	if (args[i+1] == "toggle")
		if ((getObjVisibility(theObj) == "visible") || (getObjVisibility(theObj) == "show"))
			hide(theObj)
		else show(theObj)
		else if (args[i+1] == "hide")	hide(theObj)
		else if (args[i+1] == "show")	show(theObj)
	}
}

//revised showHide() for z-index issue with nav6
function showAndHide()	{
var args = arguments
	for (var i = 0; i < args.length; i += 2)	{
		if (typeof args[i] == "string")	{
			var theObj = setObject(args[i])
		}
		if (args[i+1] == "toggle")	{
			if ((getObjVisibility(theObj) == "visible") || (getObjVisibility(theObj) == "show"))	{
				hide(theObj)
        setZIndex(theObj,0)
			} else	{
				setZIndex(theObj,100)
        show(theObj)
			} 
		} else if (args[i+1] == "hide")  {
      hide(theObj)
      setZIndex(theObj,0)
    } else if (args[i+1] == "show")  {
      show(theObj)
      setZIndex(theObj,100)
    } else  {
      theObj.visibility = "visible"
    }
	}
}


/*
open a new window, center it and set dimensions relative to the users screen
url: url of new document
name: document name
statbar: boolean yes or no for statusbar display
scroll: boolean yes or no for scrollbars
locate: boolean yes or no for locationbars
x: set the new window height with a percentage value relative to the users screen
y: set the new window width with a pecentage value relative to the users screen
usage: javascript:winOpen('index.htm','newwin','yes','yes','yes',.5,.5)
*/
function winOpen(url,name,statbar,scroll,locate,x,y)	{
var adjustedleft = 8//optional
var adjustedheight = 30//adjust height because of windows taskbar
var screenwidthremainder = screen.availWidth%2//really not needed, but won't hurt
var screenheightremainder = screen.availHeight%2
var screenwidth = screen.availWidth - screenwidthremainder
var screenheight = screen.availHeight - screenheightremainder
var winheight = parseInt(screenheight)* y//set new window height properties
var winwidth = parseInt(screenwidth)* x//set new window width properties
var winleft = parseInt(screenwidth/2) - (winwidth/2) - adjustedleft//optional
var wintop = parseInt(screenheight/2) - (winheight/2) - adjustedheight

var win = window.open(url,name,'width=' +winwidth+ ',height=' +winheight+',status=' +statbar+',scrollbars='+scroll+',location='+locate+',top='+wintop+',left='+winleft)
}

/*
set status bar message
usage:<a href="#" onmouseover="return msg('Hello')">
*/
function msg(x) {
if (window.status != x)
  window.status = x
  return true
}
