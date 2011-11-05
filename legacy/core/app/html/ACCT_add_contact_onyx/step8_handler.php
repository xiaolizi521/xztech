<?php

require_once("CORE_app.php");

function gset( $lvar, $rvar ) {
    global $$lvar, $$rvar;
    if( isset($$rvar) ) {
        $$lvar = $$rvar;
    } else {
        $$lvar = '';
    }
}

session_register("SESSION_email");
session_register("SESSION_step");
session_register("SESSION_error");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step7_page.php");
} else {
        $email = trim($email);
        gset("SESSION_email","email");        

        $GLOBALS["SESSION_error"] = '';
        if(empty($SESSION_email)) {
            $GLOBALS["SESSION_error"] .= "ERROR: Primary email address is blank.
                <br>\n";
        }
        else if(!isValidEmailAddress($SESSION_email)) {
            $GLOBALS["SESSION_error"] .= "ERROR: Primary email address 
                is invalid. <br>\n";
        }        

        if (empty($GLOBALS['SESSION_error'])) {
            $SESSION_step += 1;
            ForceReload("step9_page.php");
        } else {
            ForceReload($HTTP_REFERER);
        }
}

?>
