<?php

/* Database Constants */
define ("DB_HOST", "localhost");
define ("DB_USER", "whatpulse");
define ("DB_PASS", "FU55mwh3CzfZBFSK");
define ("DB_NAME", "whatpulse");
define ("USER_DB", "userDB");
define ("XML_DB", "xmlDB");

// Define Statement Constants for ImageDB

define("GET_IMAGE", "select image_data from icache where userid = ?");
define("UPDATE_XML", "update isettings set xmldata = ? where uid = ?");
define("IMAGE_STORE", "update icache set image_data = ?, lastupdated = ? where uid = ?");
define("IMAGE_NEW_STORE","insert (`image_data`, `lastupdated`, `uid`) into icache VALUES (?, ?, ?)");
define("IMAGE_CACHE_STATUS", "select lastupdated from icache where uid = ?");
define("IMAGE_RECALL", "select image_data from icache where uid = ?");

$xmlsearch = array(
				"&sup2;",
				"&sup3;",
				"&iquest;",
				"&szlig;",
				"&sect;",
				"&eth;",
				"&ouml;",
				"&oslash;",
				"&Uuml;",
				"&deg;",
				"&aacute;",
				"&eacute;",
				"&egrave;",
				"&copy;",
				"&yen;",
				"&ntilde;",
				"&auml;",
				"&uuml;",
				"&ETH;",
				"@",
				"&reg;",
				"&Auml;",
				"&acute;",
				"&");

$xmlreplace = array(
				2,
				3,
				"?",
				"B",
				"S",
				"o",
				"o",
				"o",
				"U",
				"o",
				"a",
				"e",
				"e",
				"c",
				"Y",
				"n",
				"a",
				"u",
				"D",
				"A",
				"R",
				"A",
				"",
				"and");
				

?>