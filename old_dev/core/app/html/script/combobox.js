/*
 * Combobox Javascript
 * Ken Kinder <kkinder@rackspace.com>
 */

function combobox_setup(combobox_name) {
  var combobox_text = document.getElementById(combobox_name + "_text");
  var combobox_menu = document.getElementById(combobox_name + "_menu");
  var combobox_menu_wrap = document.getElementById(combobox_name + "_menu_wrap");
  var combobox_button = document.getElementById(combobox_name + "_button");
  var combobox_input_area = document.getElementById(combobox_name + "_input_area");
  var new_menu_width = combobox_input_area.offsetWidth - 5;
  combobox_menu.style.width = new_menu_width + "px";
  // combobox_menu_wrap.style.top = (combobox_input_area.offsetTop + combobox_input_area.offsetHeight - 5) + "px";
}

function combobox_down(combobox_name) {
  var combobox_menu = document.getElementById(combobox_name + "_menu_wrap");
  var combobox_button = document.getElementById(combobox_name + "_button");
  combobox_menu.className = "combobox_menu";
  combobox_setup(combobox_name);
  combobox_button.src = "/images/combobox/combobox-up.gif";
}

function combobox_up(combobox_name) {
  var combobox_menu = document.getElementById(combobox_name + "_menu_wrap");
  var combobox_button = document.getElementById(combobox_name + "_button");
  combobox_menu.className = "combobox_menu_hiding";
  combobox_button.src = "/images/combobox/combobox-down.gif";
}

function combobox_toggle(combobox_name) {
  var combobox_menu = document.getElementById(combobox_name + "_menu_wrap");
  if (combobox_menu.className == "combobox_menu") {
    combobox_up(combobox_name);
  } else {
    combobox_down(combobox_name);
  }
}

function combobox_setval(combobox_name) {
  var combobox_menu = document.getElementById(combobox_name + "_menu");
  var combobox_text = document.getElementById(combobox_name + "_text");
  combobox_text.value = combobox_menu.value;
  combobox_up(combobox_name);
  combobox_text.focus();
}
