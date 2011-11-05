<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !ereg ( '/logout.php', $_SERVER['SCRIPT_NAME'] ) ) {
	header ( 'Location: ' . $site_url . '/logout.php' );
}
 
$cookie_server = ereg_replace( 'http://|http://www.|www.', '', $_SERVER['SERVER_NAME'] );

ob_start();
require_once ( 'db.php' );
require_once ( 'header.php' );

function Logout() {
	global $site_url, $main_filename;
	echo '<table cellpadding="0" cellspacing="0" style="width: 100%;" class="main">
	<tr>
		<td style="text-align: center"><b>Successfully Logged Out!</b>
			<table style="height: 10px;">
				<tr>
					<td></td>
				</tr>
			</table>
';
	if ( ( empty ( $GLOBALS['HTTP_REFERER'] ) ) || ereg( 'login', $_SERVER['SCRIPT_NAME'] ) || ereg( 'logout', $_SERVER['SCRIPT_NAME'] ) ) {
		echo '';
	}
	else {
		echo '			<a href="',  $GLOBALS['HTTP_REFERER'], '">Click here go back to where you came from</a><br />
			<a href="', $site_url, '/' , $main_filename, '">Click here to go back to the frontpage</a></td>
	</tr>
</table>';
}

function ErrorDisplay( $msg ) {
echo "<table width='100%' cellpadding='0' cellspacing='0' class='main'><tr><td align='center'>";
echo "<center><font face='verdana' color='red' size='1'><b>$msg</b></font></center>";
echo "<table width='25%' cellpadding='0' cellspacing='0'>";
echo "<tr><td height='7'></td></tr>";
echo "<tr><td>";
include ( "login_form.php" );
echo "</td></tr></table>";
echo "</td></tr></table>";
}
?>

<html>
<head>
<title><?php echo $sitetitle ?> - Logout</title>
</head>

<body scroll="auto" bgcolor="<?php echo $bgcolor ?>" background="<?php echo $bgbackground ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="60%" height="100%" cellpadding="0" cellspacing="0" border="0" align="center"><tr>
<td align="center" valign="middle">
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid <?php echo $bordercolor2 ?>" class='main'><tr><td>

<?php
if ( isset ( $user_info[user_id] ) ) {
Logout();
setcookie( "user_id", "", time()-60*60*24*30, "/", "$cookie_server", 0 );
setcookie( "username", "", time()-60*60*24*30, "/", "$cookie_server", 0 );
setcookie( "type", "", time()-60*60*24*30, "/", "$cookie_server", 0 );
setcookie( "password", "", time()-60*60*24*30, "/", "$cookie_server", 0 );
ob_end_flush();
} else {
ErrorDisplay( $msg = "You are not currently logged in" );
ob_end_flush();
}
?>

</td></tr></table>
</td></tr></table>



















