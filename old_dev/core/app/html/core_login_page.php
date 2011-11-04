<?php

if( !empty($HTTP_COOKIE_VARS["COOKIE_last_login"]) ) {
    $last_login = $HTTP_COOKIE_VARS["COOKIE_last_login"];
} else {
    $last_login = "";
}

/*
 * Clean out the login cookies.
 */
if( !defined("NO_AUTH") ) {
    define("NO_AUTH",1);
}
require_once("CORE.php");
global $session_key;

$cookie_domain = ".rackspace.com";
setcookie ($session_key, '', 0, "/", $cookie_domain );
setcookie ("PHPSESSID", '', 0, "/", $cookie_domain );

$cookie_domain = "core.rackspace.com";
setcookie ($session_key, '', 0, "/", $cookie_domain );
setcookie ("PHPSESSID", '', 0, "/", $cookie_domain );

$cookie_domain = "";
setcookie ($session_key, '', 0, "/", $cookie_domain );
setcookie ("PHPSESSID", '', 0, "/", $cookie_domain );


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
  <title>CORE: Login</title>
  <LINK HREF="/css/core2_basic.css" REL="stylesheet">
  <STYLE>
    .trouble {
        margin: 0.3ex 1em;
    }
    </STYLE>

<SCRIPT type="text/javascript">

moveTo(200,250);

</SCRIPT>
</HEAD>
<BODY>

<FORM METHOD="POST" ACTION="/core_login_handler.php" >
<div class="blueman_div_border">
<span class="blueman_span_title">CORE: Login</span>
          <?php
          if( !empty($refresh) ) {
            echo '<input type="hidden" name="refresh" value="'.$refresh.'">';
          }
          ?>
			<TABLE BORDER="0"
			       CELLSPACING="2"
			       CELLPADDING="2" ALIGN="center">
                        <TR><TD COLSPAN=3><FONT COLOR=RED><SMALL>
                        CORE now uses your Single-Sign-On password.
                        This is the same as your email account.
                        </SMALL>
<?php
            if( !empty($error_message) ) {
                print '<p style="border: solid thin red; text-align: center">';
                print "\n";
                print $error_message;
            }
?>
                        </FONT></TD></TR>
			<TR>
				<TD> Username: </TD>
				<TD>
					<INPUT TYPE="text"
					       NAME="core_userid"
					       SIZE="8"
                           VALUE="<?=$last_login?>"
                    >
				</TD>
				<TD ROWSPAN="3"> <IMG SRC="/images/lock.jpg" 
				          WIDTH="100" 
						  HEIGHT="100" 
                          BORDER="0"
						  ALT="Open Sesame"> </TD>
			</TR>
			<TR>
				<TD> Password: </TD>
				<TD>
					<INPUT TYPE="password"
					       NAME="core_password"
					       SIZE="8">
				</TD>
			</TR>
			<TR>
				<TD COLSPAN="2"
				    ALIGN="right"><INPUT TYPE="submit"
                                         VALUE="LOGIN"
                                         STYLE="padding-left: 2ex; padding-right: 2ex"
                                         CLASS="form_button">
                </TD>
			</TR>
			</TABLE>

<div style="border: solid 1px black">
<p style="font-size: large; font-weight: bold;">
Troubleshooting Tips
</p>
<?php 
if( empty( $GLOBALS['HTTP_COOKIE'] ) ) {
?> <p class="trouble">
CORE requires cookies to be turned on.  Please make sure
that your browser is set to allow cookies. </p>
<?php } ?>
<p class="trouble">
If your time isn't correct, then you won't be able to login.
Check that these times are reasonably close.
</p>
<pre class="trouble">
CORE Time: <?= strftime("%Y/%m/%d %I:%M %p %Z"); ?>

Your Time: <SCRIPT type="text/javascript">
function ddigit( num ) {
    // Double the digit of an int
    // EXAMPLE: 4 becomes 04
    return (num<10) ? '0' + num : num;
}

var date = new Date();
var day  =  ddigit( date.getDate() );
var month = ddigit( date.getMonth() + 1 );
var year =          date.getFullYear();
var hours = ddigit( date.getHours() );
var mins =  ddigit( date.getMinutes() );

if ( hours < 12 ) {
    var ap = "AM";
} else {
    var ap = "PM";
    if ( hours != 12 ) {
        hours = ddigit( hours - 12 );
    }
}

document.write(year + "/" + month + "/" + day + " " + hours + ":" + mins + " " + ap );
</SCRIPT></pre>
<? // End of clock thingy
?>
</div>

</div>
</FORM>

</BODY>
</HTML>
