/*
 * Smart Table Javascript Code
 *
 * Smart Tables are essentially the tables where you can choose what
 * columns to show and what columns to hide. This all relates to the
 * s_table custom tag.
 * 
 * By Ken Kinder <kkinder@rackspace.com>
 */


function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function smarttable_togglecol(col_name) {
  /*
   * This function toggles the display for columns (or anything else)
   * with the passed name.
   */
  var checkbox = document.getElementById(col_name + '_cb');
  if (checkbox.checked) {
    smarttable_showcol(col_name);
  } else {
    smarttable_hidecol(col_name);
  }
}

function smarttable_hidecol(col_name) {
  /*
   * Hides columns by a given name
   */
  var cell_list = document.getElementsByName(col_name);
  for (var i=0; i < cell_list.length; i++) {
    var element = cell_list[i];
    element.className += " col_hide";
  }
  
  date = new Date();
  date.setFullYear(date.getFullYear() + 1);
  setCookie("smarttable_" + col_name, "hide", date);
}

function smarttable_showcol(col_name) {
  /*
   * Hides columns by a given name
   */
  var cell_list = document.getElementsByName(col_name);
  for (var i=0; i < cell_list.length; i++) {
    var element = cell_list[i];
    removeClassName(element, "col_hide")
  }
  
  date = new Date();
  date.setFullYear(date.getFullYear() + 1);
  setCookie("smarttable_" + col_name, "show", date);
}

function smarttable_togglefilter(table_id) {
  /*
   * Toggles the visibility of the column chooser. This being the drop
   * down menu that lets the user choose what columns to show.
   *
   * The table_id is the id of the smart table.
   */
  var filtermenu = document.getElementById(table_id + '_' + 'colfilter');
  if (filtermenu.className == 'col_menu_show dropmenu') {
    filtermenu.className = 'col_menu_hide dropmenu'
  } else {
    filtermenu.className = 'col_menu_show dropmenu'
  }
}

function smarttable_showfilter(table_id) {
  /*
   * Shows the column chooser.
   */
  var filtermenu = document.getElementById(table_id + '_' + 'colfilter');
  filtermenu.className = 'col_menu_show dropmenu'
}

function smarttable_hidefilter(table_id) {
  /*
   * Hides the column chooser.
   */
  var filtermenu = document.getElementById(table_id + '_' + 'colfilter');
  filtermenu.className = 'col_menu_hide dropmenu'
}

function smarttable_rolldown(row_id) {
  var row = document.getElementById(row_id + '_tr');
  var roll = document.getElementById(row_id + '_roll');
  var rollup = document.getElementById(row_id + '_rollup');
  roll.className = "roller_hide";
  rollup.className = "";
  row.className = "roller_show";
}

function smarttable_rollup(row_id) {
  var row = document.getElementById(row_id + "_tr");
  var roll = document.getElementById(row_id + '_roll');
  var rollup = document.getElementById(row_id + '_rollup');
  roll.className = "";
  rollup.className = "roller_hide";
  row.className = "roller_hide";
}

function smarttable_clear(row_id) {
  var row = document.getElementById(row_id);
  row.innerHTML = "";
}

function smarttable_init(table_id) {
  table = document.getElementById(table_id);
  
  var headrow = table.rows.item(0);
  var checkbox;
  
  date = new Date();
  date.setFullYear(date.getFullYear() + 1);
  for (var i=0; i < headrow.cells.length; i++) {
    c = getCookie("smarttable_" + table_id + "_" + i);
    if (c == "hide") {
      var checkbox = document.getElementById(table_id + '_' + i + '_cb');
      checkbox.checked = 0;
      smarttable_hidecol(table_id + "_" + i);
      setCookie("smarttable_" + i, "hide", date);
    }
  }
  
}