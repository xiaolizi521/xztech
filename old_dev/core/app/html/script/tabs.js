function settab(active_tab_name) {
  /*
    Activates active_tab_name
  */
  var inactive_tab_content = null;
  var inactive_tab = null;
  var active_tab_content = null;
  var active_tab = null;
  
  for (i = tabs.length - 1; i >= 0; i--) {
    tab = tabs[i];
    if (tab != active_tab_name) {
      inactive_tab_content = document.getElementById(tab + '_content');
      try {
          inactive_tab_content.className = "tab_hiding";
      } catch (e) {
          ;
      }
      
      inactive_tab = document.getElementById(tab + '_tab');
      try {
          inactive_tab.className = "inactive_tab";
      } catch (e) {
          ;
      }
      bottom_inactive_tab = document.getElementById(tab + '_tab_bottom');
      try {
        bottom_inactive_tab.className = "inactive_tab_bottom";
      } catch (e) {
        ;
      }
    }
  }

  active_tab_content = document.getElementById(active_tab_name + '_content');
  try {
      active_tab_content.className = "tab_showing";
  } catch(e) {
      return ''; // Well, that tab doesn't exist.
  }

  try {
    anchor = document.anchors.namedItem('top');
    anchor.focus()
  } catch(e) {
    ;
  }

  active_tab = document.getElementById(active_tab_name + '_tab');
  active_tab.className = "active_tab";
  bottom_active_tab = document.getElementById(active_tab_name + '_tab_bottom');
  try {
    bottom_active_tab.className = "active_tab_bottom";
  } catch(e) {
    ;
  }

  setCookie(tab_space + "_tab", active_tab_name);
  
  return active_tab_name;
}

// Cookie code -- taken from echoecho.com
function getCookie(NameOfCookie) {
  if (document.cookie.length > 0) {
    begin = document.cookie.indexOf(NameOfCookie+"=");
    if (begin != -1) {
      begin += NameOfCookie.length+1;
      end = document.cookie.indexOf(";", begin);
      if (end == -1) end = document.cookie.length;
      return unescape(document.cookie.substring(begin, end));
    }
  }
  return null;
}

function setCookie(NameOfCookie, value, expiredays) {
  var ExpireDate = new Date ();
  ExpireDate.setTime(ExpireDate.getTime() + (expiredays * 24 * 3600 * 1000));
  
  document.cookie = NameOfCookie + "=" + escape(value) +
      ((expiredays == null) ? "" : "; expires=" + ExpireDate.toGMTString());
}

// Local Variables:
// mode: java
// End:
