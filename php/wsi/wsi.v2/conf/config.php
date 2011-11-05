<?php

	/* Define the Database Login Details */

	define("DB_HOST","localhost");
	define("DB_USER","wpsigs");
	define("DB_PASS","wpsigs");
	define("DB_NAME","wpsigs");

	/* Define Path Information */

	define("DEF_MAIN_URL","http://www.wpsigs.com");
	define("HARD_PATH_DIR","/home/wpsigs/public_html/");
	define("ADMIN_PATH", HARD_PATH_DIR . "admin");
	define("SMARTY_CACHE_PATH", HARD_PATH_DIR . "include/smarty/cache");
	define("SMARTY_TEMPLATE_PATH", HARD_PATH_DIR . "include/smarty/templates");
	define("SMARTY_COMPILE_PATH", HARD_PATH_DIR . "include/smarty/templates_c");
	define("SMARTY_CONFIG_PATH", HARD_PATH_DIR . "include/smarty/config");
	/* Define required php libs */

	include_once(HARD_PATH_DIR . "includes/php/classes/req/class.db.php");
//	include_once(HARD_PATH_DIR . "includes/php/func/gen.func.php");
	include_once(HARD_PATH_DIR . "includes/php/classes/req/class.session.php");


	/* Define Permissions Map */

	define("USER", 1); // This is a normal user
	define("MODERATOR", 2);
	define("ADMIN", 4);

	/* Define the global function that will verify permissable action upon requirement. */

	// Usage: isPermitted(array(PERM1, PERM2, PERM3 ....), $userPerms);
	// Return: 1 if permitted, 0 if not permitted.



?>