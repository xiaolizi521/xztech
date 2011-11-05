<?php

require_once('CORE_app.php');


session_register("SESSION_contact_id");
session_register("SESSION_account_id");
session_register("SESSION_role_id");
session_register("SESSION_step");


if( !empty($back) ) {
        ForceReload("step1_page.php");
} elseif( !empty($next) ) {
        if( empty( $confirm ) ) {
                ForceReload($HTTP_REFERER);
                exit;
        }

        if( $confirm == "yes" ) {
                $SESSION_step += 1;
                ForceReload("step5_page.php");
        } else {
                # Quit
                print "<html><head>\n";
                print "<SCRIPT LANGUAGE=\"JavaScript\">\n";
                print "<!--\n" 
                print "function close_it() { window.close(); }\n"
                print "//-->\n";
                print "</SCRIPT>\n";                
                print "</head>\n";
                print '<body onLoad="setTimeout(close_it,1)"></body>';
                print "</html>\n\n";
                exit;
        }
} else {
        ForceReload($HTTP_REFERER);
        exit;
}


?>