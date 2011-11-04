<?php
/*
 * This file contains include statements for all required files.
 * 
 * These referenced files are required for operation of all scripts found herein.
 * 
*/


define('DIR_ROOT', "/home/offbeatz/public_html/pulse/");
define('CLASS_ROOT', DIR_ROOT . "classes");
define('CONFIG_ROOT', DIR_ROOT . 'config/');

// Variable Setups
require_once(CONFIG_ROOT . "config.vars.php");
require_once(CONFIG_ROOT . "exceptions.php");

// Class setups
require_once(CLASS_ROOT . "db/class.db.php");
require_once(CLASS_ROOT . "global/class.session.php");

?>