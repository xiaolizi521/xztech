<?php

    /* Define the Database Login Details */

    define("DB_HOST","localhost");
    define("DB_USER","ipmgmt");
    define("DB_PASS","ipmgmt");
    define("DB_NAME","ipmgmt");

    /* Define Path Information */

    define("DEF_MAIN_URL","http://iptool.ghshosting.com");
    define("HARD_PATH_DIR","/home/gvoip/public_html/");
    define("ADMIN_PATH", HARD_PATH_DIR . "admin");
    /* Define required php libs */

    include_once(HARD_PATH_DIR . "include/class/class.mysql.php");
    include_once(HARD_PATH_DIR . "include/class/class.controller.php");


    /* Define Permissions Map */

    define("USER", 1); // This is a normal user
    define("MODERATOR", 2);
    define("ADMIN", 4);

    /* Define the global function that will verify permissable action upon requirement. */

    // Usage: isPermitted(array(PERM1, PERM2, PERM3 ....), $userPerms);
    // Return: 1 if permitted, 0 if not permitted.



?>
