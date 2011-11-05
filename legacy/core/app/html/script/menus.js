window.top.name = "workspace";
var menu_list = new Array();

var forms_hidden = 0;

// Tags that ignore the z_layer
var no_zlayer_tags = new Array("select", "textarea");

function dynamicMenuPositioning() {
  /*

    This function does two things:

    (1) It fixes broken Internet Explorer position rules for the new
        menus.

        Microsoft declined to implement position: fixed. The logical work around is to
        position the main content div in the bulk of the page and set it to scroll.
        However, position: absolute is broken in IE too, so we have this JavaScript to
        reposition the main content div. This is a workaround for the workaround that
        breaks with IE.

    (2) It dynamicly changes the display of the toolbar depending on whether it should
        wrap.

  -Ken Kinder <kkinder@rackspace.com>
  */

  var menu_tools = document.getElementById("menu_tools");
  var menubar = document.getElementById("menubar");

  if (menu_tools) {
    if (menu_tools.className == "menu_tools_relative") {
      menu_tools.className = "menu_tools";
    }

    var menu_buttons = document.getElementsByName("menu_button");
    var last_menu_button = menu_buttons.item(menu_buttons.length - 1)

    if (menu_tools.offsetLeft - 10 <
        (last_menu_button.offsetLeft + last_menu_button.offsetWidth)) {
      menu_tools.className = "menu_tools_relative";
    }

    if (menu_tools.offsetHeight < 16) {
      menu_tools.style.height = 16;
    }

    if (menubar.offsetHeight < menu_tools.offsetHeight) {
      menubar.style.height = menu_tools.offsetHeight;
    }
  }

  var mainbody = document.getElementById("mainbody");
  if (!mainbody) {
    mainbody = document.getElementsByTagName("html")[0];
  }
  var maincontent = document.getElementById("maincontent");
  var menu_height = menubar.offsetHeight;
  var document_height = mainbody.offsetHeight;

  // Windows scrollable div thing
  if (navigator.appName == "Microsoft Internet Explorer") {
    maincontent.style.height = document_height - menu_height - 2 + 'px';
    maincontent.style.overflow = 'auto';
  }
  maincontent.style.top = menu_height + 'px';

  resetMenus();
}

function clearHistory() {
  loaddiv("/py/clear_history.esp", "historyMenu");
}

function clearSession() {
  if (window.confirm("This will clear out ALL session data, including History, Ticket Filter Settings, DCOPs Queue Filters, Managed Exchange inputs, etc. Are you sure you want to do this?"))
     loaddiv("/py/clear_session.pt", "historyMenu");
}

function menuRedirect(url) {
  window.top.location.href = url;
}

function hideBadElements() {
  // Hides elements that don't obey the z-layer
  if (!forms_hidden) {
    for (var tag_i=0; tag_i < no_zlayer_tags.length; tag_i++) {
      var tag = no_zlayer_tags[tag_i];

      var elements = document.getElementsByTagName(tag);
      for (var i=0; i < elements.length; i++) {
        var element = elements[i];
        if (!hasClassName(element, "menu_zlayer_hide")) {
          element.className += " menu_zlayer_hide";
        }
      }
    }
    forms_hidden = 1;
  }
}

function showBadElements() {
  // Shows elements that don't obey the z-layer

  if (forms_hidden) {
    for (var tag_i=0; tag_i < no_zlayer_tags.length; tag_i++) {
      var tag = no_zlayer_tags[tag_i];

      var elements = document.getElementsByTagName(tag);
      for (var i=0; i < elements.length; i++) {
        var element = elements[i];
        removeClassName(element, "menu_zlayer_hide");
      }

      forms_hidden = 0;
    }
  }
}

function runSearch(selectBox) {
    var search_value = document.getElementById("search-value");
    var search_type = document.getElementById("search-type");

    if (search_type.value == "info_search") {
        if (search_value.value) {
            menuRedirect("/CORE_info_search.php?search_number="
                + search_value.value);
        } else {
            menuRedirect("/CORE_info_search.php");
        }
    } else if (search_type.value == "cert_search") {
        menuRedirect("/py/sslcert/search.pt?domain_search=" + search_value.value);
    } else if (search_type.value == "core_search") {
        if (search_value.value.length > 0) {
	        menuRedirect("/core-search/controller/core-search?searchType=all&query=" + search_value.value + "&command=Search");
	    }
	    else {
	        menuRedirect("/core-search/");
	    }
    } else if (search_type.value == "onyx_account_search") {
            doPopUp("/py/account/search.pt?account_name="
                + search_value.value,
            "Onyx_Account_Search", 700, 500, "scrollbars=1,resizable=1");
            search_value.value = '';
            selectBox.selectedIndex=0;
    } else if (search_type.value == "quick_find") {
        menuRedirect("/tools/quick_find.php3");
    } else if (search_type.value == "ticket_search") {
        menuRedirect("/py/ticket/search.pt");
    } else if (search_type.value == "score_search") {
        if (search_value.value.length > 0) {
            menuRedirect("/py/score/?q=" + escape(search_value.value));
        } else {
            menuRedirect("/py/score/");
        }
    } else if (search_value.value) {
        var value = search_value.value;

        if (/[#-]/.test(value)) {
            if (value[0] == '#') {
                value = value.substring(1, value.length);
            }
            menuRedirect("/search_redirect.php?search_type=" + search_type.value + "&search_number=" + value);
        } else if (search_type.value == "alert_search"
                || /[0-9]\.[0-9]/.test(search_value.value)) {
            doPopUp("/py/rackwatch/alert-load.pt?standalone=1&alert_number="
                + search_value.value,
            "Alert_Display", 900, 400, "scrollbars,resizeable");
            search_value.value = '';
            selectBox.selectedIndex=0;
        } else if (search_type.value == "customer_search") {
            menuRedirect("/ACCT_main_workspace_page.php?account_number="
                + search_value.value);
        } else if (search_type.value == "computer_search") {
            menuRedirect("/ACCT_main_workspace_page.php?computer_number="
                + search_value.value);
        } else {
            menuRedirect("/search_redirect.php?search_type=" + search_type.value + "&search_number=" + value);
        }
    }
}

function checkEnter(event) {
  if (event.which) {
    var keypressed = event.which;
  } else {
    var keypressed = event.keyCode;
  }
  if (keypressed == 13) {
    runSearch();
    return false;
  }
}

function logoutCore() {
  if(window.confirm("Are you sure you want to exit CORE?")) {
    top.location = "/tools/logout.php";
  }
}

function callPrint() {
    // Print won't work for IE 4.x
    printwin = window.open();
    //for(elem = 0; elem < document.all.length; ++elem) {
    //  if(document.all[elem].id != menubar) {
    //    printwin.document.write(document.all[elem].innerHTML);
    //  }
    //}
    printwin.document.write(document.getElementById('maincontent').innerHTML);
    //printwin.document.getElementById('menubar').innerHTML = " ";
    //window.print();
    //frame = opener.parent.workspace.content;
    //if( frame && frame.print ) {
        //frame.focus(); // IE sucks
        //frame.print();
    //} else {
        //frame = opener.parent.workspace;
        //if( frame && frame.print ) {
            //frame.focus(); // IE sucks
            //frame.print();
        //} else {
            //alert('To print this page, right click and select Print');
        //}
    //}
    //window.close();
}

//*****************************************************************************
// Do not remove this notice.
//
// Copyright 2000 by Mike Hall.
// See http://www.brainjar.com for terms of use.
//*****************************************************************************

//----------------------------------------------------------------------------
// Code to determine the browser and version.
//----------------------------------------------------------------------------

function Browser() {

  var ua, s, i;

  this.isIE    = false;  // Internet Explorer
  this.isNS    = false;  // Netscape
  this.version = null;

  ua = navigator.userAgent;

  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  // Treat any other "Gecko" browser as NS 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
}

var browser = new Browser();

//----------------------------------------------------------------------------
// Code for handling the menu bar and active button.
//----------------------------------------------------------------------------

var activeButton = null;

// Capture mouse clicks on the page so any active button can be
// deactivated.

if (browser.isIE)
  document.onmousedown = pageMousedown;
else
  document.addEventListener("mousedown", pageMousedown, true);

function pageMousedown(event) {

  var el;

  // If there is no active button, exit.

  if (activeButton == null)
    return;

  // Find the element that was clicked on.

  if (browser.isIE)
    el = window.event.srcElement;
  else
    el = (event.target.tagName ? event.target : event.target.parentNode);

  // If the active button was clicked on, exit.

  if (el == activeButton)
    return;

  // If the element is not part of a menu, reset and clear the active
  // button.

  if (getContainerWith(el, "DIV", "menu_dropdown") == null) {
    resetButton(activeButton);
    activeButton = null;
  }
}

function resetMenus() {
  // Collapse Menus
  resetButton(activeButton);
  activeButton = null;
}

function buttonClick(event, menuId) {
  var button;

  // Get the target button element.

  if (browser.isIE)
    button = window.event.srcElement;
  else
    button = event.currentTarget;

  // Blur focus from the link to remove that annoying outline.

  button.blur();

  // Associate the named menu to this button if not already done.
  // Additionally, initialize menu display.

  if (button.menu == null) {
    button.menu = document.getElementById(menuId);
    button.menu.className += " menu_dropdown_active";
    if (button.menu.isInitialized == null)
      menuInit(button.menu);
  }

  // Reset the currently active button, if any.

  if (activeButton != null)
    resetButton(activeButton);

  // Activate this button, unless it was the currently active one.

  if (button != activeButton) {
    depressButton(button);
    activeButton = button;
  }
  else
    activeButton = null;

  return false;
}

function buttonMouseover(event, menuId) {

  var button;

  // Find the target button element.

  if (browser.isIE)
    button = window.event.srcElement;
  else
    button = event.currentTarget;

  // If any other button menu is active, make this one active instead.

  if (activeButton != null && activeButton != button)
    buttonClick(event, menuId);
}

function depressButton(button) {
  var x, y;

  hideBadElements();

  // Update the button's style class to make it look like it's
  // depressed.

  button.className += " menu_button_active";

  // Position the associated drop down menu under the button and
  // show it.

  x = getPageOffsetLeft(button);
  y = getPageOffsetTop(button) + button.offsetHeight;

  // For IE, adjust position.

  if (browser.isIE) {
    x += button.offsetParent.clientLeft;
    y += button.offsetParent.clientTop;
  }

  // Add style that makes it visible
  button.menu.className += " menu_dropdown_active";

  button.menu.style.left = x + "px";
  button.menu.style.top  = y + "px";
  button.menu.style.visibility = "visible";


}

function resetButton(button) {

  showBadElements();
  // Restore the button's style class.

  removeClassName(button, "menu_button_active");
  // Hide the button's menu, first closing any sub menus.
  try {
    if ( button != null && button.menu != null) {
      // Do general stuff
      closeSubMenu(button.menu);
      removeClassName(button.menu, "menu_dropdown_active");
      button.menu.style.visibility = "hidden";
    }
  } catch(e) {
  }
}

//----------------------------------------------------------------------------
// Code to handle the menus and sub menus.
//----------------------------------------------------------------------------

function menuMouseover(event) {

  var menu;

  // Find the target menu element.

  if (browser.isIE)
    menu = getContainerWith(window.event.srcElement, "DIV", "menu_dropdown");
  else
    menu = event.currentTarget;

  // Close any active sub menu.

  if (menu.activeItem != null)
    closeSubMenu(menu);
}

function menuItemMouseover(event, menuId) {

  var item, menu, x, y;

  // Find the target item element and its parent menu element.

  if (browser.isIE)
    item = getContainerWith(window.event.srcElement, "A", "menu_menuitem");
  else
    item = event.currentTarget;
  menu = getContainerWith(item, "DIV", "menu_dropdown");


  // Close any active sub menu and mark this one as active.

  if (menu.activeItem != null)
    closeSubMenu(menu);
  menu.activeItem = item;

  // Highlight the item element.
  item.className += " menu_menuitem_highlight";

  // Initialize the sub menu, if not already done.

  if (item.subMenu == null) {
    item.subMenu = document.getElementById(menuId);
    item.subMenu.className += " menu_dropdown_active";
    if (item.subMenu.isInitialized == null)
      menuInit(item.subMenu);
  }

  // Get position for submenu based on the menu item.

  x = getPageOffsetLeft(item) + item.offsetWidth;
  y = getPageOffsetTop(item);

  // Adjust position to fit in view.

  var maxX, maxY;

  if (browser.isNS) {
    maxX = window.scrollX + window.innerWidth;
    maxY = window.scrollY + window.innerHeight;
  }
  if (browser.isIE) {
    maxX = (document.documentElement.scrollLeft   != 0 ? document.documentElement.scrollLeft    : document.body.scrollLeft)
         + (document.documentElement.clientWidth  != 0 ? document.documentElement.clientWidth   : document.body.clientWidth);
    maxY = (document.documentElement.scrollTop    != 0 ? document.documentElement.scrollTop    : document.body.scrollTop)
         + (document.documentElement.clientHeight != 0 ? document.documentElement.clientHeight : document.body.clientHeight);
  }
  maxX -= item.subMenu.offsetWidth;
  maxY -= item.subMenu.offsetHeight;

  if (x > maxX)
    x = Math.max(0, x - item.offsetWidth - item.subMenu.offsetWidth
      + (menu.offsetWidth - item.offsetWidth));
  y = Math.max(0, Math.min(y, maxY));

  // Position and show it.

  item.subMenu.style.left = x + "px";
  item.subMenu.style.top  = y + "px";
  item.subMenu.style.visibility = "visible";

  // Stop the event from bubbling.

  if (browser.isIE)
    window.event.cancelBubble = true;
  else
    event.stopPropagation();
}

function closeSubMenu(menu) {

  if (menu == null || menu.activeItem == null)
    return;

  // Recursively close any sub menus.

  if (menu.activeItem.subMenu != null) {
    closeSubMenu(menu.activeItem.subMenu);
    removeClassName(menu.activeItem.subMenu, "menu_dropdown_active")
    menu.activeItem.subMenu.style.visibility = "hidden";
    menu.activeItem.subMenu = null;
  }
  removeClassName(menu.activeItem, "menu_menuitem_highlight");
  menu.activeItem = null;
}

//----------------------------------------------------------------------------
// Code to initialize menus.
//----------------------------------------------------------------------------

function menuInit(menu) {

  var itemList, spanList;
  var textEl, arrowEl;
  var itemWidth;
  var w, dw;
  var i, j;

  // For IE, replace arrow characters.
  if (browser.isIE) {
    menu.style.lineHeight = "2.5ex";
    spanList = menu.getElementsByTagName("SPAN");
    for (i = 0; i < spanList.length; i++)
      if (hasClassName(spanList[i], "menu_arrow")) {
        spanList[i].style.fontFamily = "Webdings";
        spanList[i].firstChild.nodeValue = "4";
      }
  }

  // Find the width of a menu item.

  itemList = menu.getElementsByTagName("A");
  if (itemList.length > 0)
    itemWidth = itemList[0].offsetWidth;
  else
    return;

  // For items with arrows, add padding to item text to make the
  // arrows flush right.

  for (i = 0; i < itemList.length; i++) {
    spanList = itemList[i].getElementsByTagName("SPAN");
    textEl  = null;
    arrowEl = null;
    for (j = 0; j < spanList.length; j++) {
      if (hasClassName(spanList[j], "menu_menuitemtext"))
        textEl = spanList[j];
      if (hasClassName(spanList[j], "menu_arrow"))
        arrowEl = spanList[j];
    }
    if (textEl != null && arrowEl != null) {
      textEl.style.paddingRight = (itemWidth
        - (textEl.offsetWidth + arrowEl.offsetWidth)) + "px";
    }
  }

  // Fix IE hover problem by setting an explicit width on first item of
  // the menu.

  if (browser.isIE) {
    w = itemList[0].offsetWidth;
    itemList[0].style.width = w + "px";
    dw = itemList[0].offsetWidth - w;
    w -= dw;
    itemList[0].style.width = w + "px";
  }

  // Mark menu as initialized.

  menu.isInitialized = true;
}

//----------------------------------------------------------------------------
// General utility functions.
//----------------------------------------------------------------------------

function getContainerWith(node, tagName, className) {

  // Starting with the given node, find the nearest containing element
  // with the specified tag name and style class.

  while (node != null) {
    if (node.tagName != null && node.tagName == tagName &&
        hasClassName(node, className))
      return node;
    node = node.parentNode;
  }

  return node;
}

function hasClassName(el, name) {

  var i, list;

  // Return true if the given element currently has the given class
  // name.

  list = el.className.split(" ");
  for (i = 0; i < list.length; i++)
    if (list[i] == name)
      return true;

  return false;
}

function removeClassName(el, name) {

  var i, curList, newList;
  try {
    if (el == null || el.className == null)
      return;
  } catch (e) {
    return;
  }

  // Remove the given class name from the element's className property.

  newList = new Array();
  curList = el.className.split(" ");
  for (i = 0; i < curList.length; i++)
    if (curList[i] != name)
      newList.push(curList[i]);
  el.className = newList.join(" ");
}

function getPageOffsetLeft(el) {

  var x;

  // Return the x coordinate of an element relative to the page.

  x = el.offsetLeft;
  if (el.offsetParent != null)
    x += getPageOffsetLeft(el.offsetParent);

  return x;
}

function getPageOffsetTop(el) {

  var y;

  // Return the x coordinate of an element relative to the page.

  y = el.offsetTop;
  if (el.offsetParent != null)
    y += getPageOffsetTop(el.offsetParent);

  return y;
}
