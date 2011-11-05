<?php
define("NO_AUTH",1);
require_once("authentication.php");

// The stored userid and password, if passed in by the user

// Pull it from the HTTP variables
if( !empty($HTTP_POST_VARS['core_userid']) ) {
    $userid = $HTTP_POST_VARS['core_userid'];
} else {
    $userid = "";
}

if( !empty($HTTP_POST_VARS['core_password']) ) {
    $password = $HTTP_POST_VARS['core_password'];
} else {
    $password = "";
}

$login_result = COREAUTH_LoginByUserid( $userid,
                                        $password );

if( $login_result != COREAUTH_LOGINRESULT_SUCCESS ) {
    if( $login_result == COREAUTH_LOGINRESULT_IS_SUSPENDED ) {
        require_once( 'security.php' );
        displayIsSuspended();
    } elseif( $login_result == COREAUTH_LOGINRESULT_INVALID_IP ) {
        $error_message = "Invalid IP Address";
        include_once("core_login_page.php");
    } elseif( $login_result == COREAUTH_LOGINRESULT_LDAP_ERROR ) {
        $error_message = "LDAP Connection Error";              // ARO: Might need to change this later.
        include_once("core_login_page.php");
    } else {
        $error_message = "Invalid Login or Password";
        include_once("core_login_page.php");
    }
    exit();
}

require_once("CORE_app.php");
global $cookie_domain;

#     sec   min   hr  day 
$month = 60 * 60 * 24 * 30;
setcookie ("COOKIE_last_login", $userid, time() + $month, 
           "/", $cookie_domain );

session_destroy();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
<?php
if( empty($refresh) or $refresh != "no" ):
?>
<!-- Refresh calling view -->
<meta http-equiv="refresh" content="0;url=/py/splash.pt">
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">


// /* LoadParent */
// /* This is from the Python Code */
// function loadParent( url, close, do_top ) {
//   if( url == '' ) {
//     // First see if opener has a reloader function
//     try {
//       opener.post_form_reloader();
//     } catch(e) {
//       try {
//         url = opener.location.href;
//       } catch(e) {
//         // Nothing To Do
//         url = '/';
//       }
//     }
//   }

//   if( url != '' ) {
//     try {
//         if( do_top ) {
//             opener.top.location.href=url;
//         } else {      
//             opener.location.href=url;
//         }
//     } catch(e) {
//         // Can't do anything.
//         if( do_top ) {
//             opener.location.href=url;
//         }
//     }
//   }
//   if( close ) {
//     window.close();
//   }
// }
// // end

// // Call it:
// // Use the same url (reload),
// // Close this window when done.
// // Don't use the top frame.
// loadParent( '', 1, 0 );

</SCRIPT>
<?php
endif;

?>
</HEAD>
<BODY>
<p>
This window should go away....
</p>
</BODY>
</HTML>
<?php
flush();

// For Emacs:
// Local Variables:
// mode: php
// c-basic-offset: 4
// End:
?>
