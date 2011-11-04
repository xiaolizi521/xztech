<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( ereg ( "/register.php", "$_SERVER[SCRIPT_NAME]" ) ) {
include ( "header.php" );
echo "<script>document.location.href='$site_url/$main_filename?page=register'</script>";
}

$register_form = "true";

if ( !isset ( $_POST[submit] ) ) {
srand((double)microtime()*1000000); 
$_SESSION[verify_string] = md5(rand(0,9999)); 
$_SESSION[verify_new_string] = substr($_SESSION[verify_string],17,5);
}
?>

<form method="post">
<table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid <?php echo $bordercolor2 ?>" align="center"><tr><td align="center" valign="middle" class="main">


<?php
if ( !isset ( $_COOKIE[user_id] ) ) {
if ( isset ( $_POST[submit] ) ) {
$username = stripslashes( $_POST[username] ); 
$password = stripslashes( $_POST[password] ); 
$email_address = stripslashes( $_POST[email_address] ); 

if ( empty ( $username ) || empty ( $password ) || empty ( $email_address ) || empty ( $verify ) ) { 
echo "<font color='red'><b>Username/Password/Email Address/Verification Fields Not Inputted.</b></font><p>";
} elseif ( strlen( $password ) < 5 ) {
echo "<font color='red'><b>Password Must Be Greater Than 5 Characters.</b></font><p>";
} elseif ( !ereg ( "@", $email_address ) ) {
echo "<font color='red'><b>Email Address Must Be A Valid Email Address.</b></font><p>";
} elseif ( !eregi ( "^[a-z0-9\-_\.]+$", $username ) || !eregi ( "^[a-z0-9\-_\.]+$", $password ) ) {
echo "<font color='red'><b>Username And/Or Password Must Be Alphanumeric</b></font><p>";;
} elseif ( strlen ( $username ) > 15 || strlen ( $password ) > 15 ) {
echo "<font color='red'><b>Username And/Or Password Must Be Less Than 15 Characters</b></font><p>";
} else {
$result_email_check = mysql_query( "SELECT email_address FROM users WHERE email_address='$email_address'" ); 
$result_username_check = mysql_query( "SELECT username FROM users WHERE username='$username'" ); 

$email_check = mysql_num_rows( $result_email_check ); 
$username_check = mysql_num_rows( $result_username_check ); 

if ( $email_check > 0 ) { 
echo "<font color='red'><b>Email Address Is Already In Database.<br>Choose Another Email Address And Try Again.</b></font><p>";
unset( $email_address ); 
} elseif ( $username_check > 0 ) { 
echo "<font color='red'><b>Username Is Already In Use. Choose Another Username And Try Again.</b></font><p>";
unset( $username ); 
} elseif ( $_POST[verify] != $_SESSION[verify_new_string] ) {
echo "<font color='red'><b>Incorrect Verification Code</b></font><p>";
} else {

if ( session_is_registered ( 'referrer_id' ) ) {
$update_referrer = mysql_query( "UPDATE users SET referrals=(referrals+1) WHERE username='$_SESSION[referrer_id]'" );
unset( $_SESSION['referrer_id'] );
} 

echo "<b>Successfully Registered!</b>";
echo "<table height='10'><tr><td></td></tr></table>";
echo "<a href='$site_url/$main_filename'>Click here to continue</a>";
$register_form = "false";
}

}

}

} else {
unset( $_SESSION['referrer_id'] );
$register_form = "false";
echo "<b>You Are Already Registered</b>";
echo "<table height='10'><tr><td></td></tr></table>";
echo "<a href='$site_url/$main_filename'>Click here to continue</a>";
}

if ( $register_form == "true" ) {
?>
<table cellpadding="0" cellspacing="0" class="main">
<tr><td><b>Username</b></td><td width="7"></td><td><input type="text" name="username" value="<?php echo $_POST[username] ?>" size="25" class="textbox"></td></tr>
<tr><td height="5"></td></tr>
<tr><td><b>Password</b></td><td width="7"></td><td><input type="password" name="password" size="25" class="textbox"></td></tr>
<tr><td height="5"></td></tr>
<tr><td><b>Email Address</b></td><td width="7"></td><td><input type="text" name="email_address" value="<?php echo $_POST[email_address] ?>" size="25" class="textbox"></td></tr>
<?php
if ( session_is_registered ('referrer_id') ) {
echo "<tr><td height='5'></td></tr>";
echo "<tr><td><b>Referrer</b></td><td width='7'></td><td><i>$_SESSION[referrer_id]</i></td></tr>";
}
?>
<tr><td height="5"></td></tr>
<tr><td><b><img src="verification.php?code=<?php echo $_SESSION[verify_string] ?>"></b></td><td width="7"></td><td><input type="text" name="verify" size="25" class="textbox"></td></tr>
</table>
<table height="5"><tr><td></td></tr></table>
<table cellpadding="0" cellspacing="0"><tr><td width="17"></td><td><input type="submit" name="submit" value="Register" class="submit_button"></td></tr></table>
<?php
} else {
echo "";
}
?>

</td></tr></form></table>
