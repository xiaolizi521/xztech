<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

/*if ( !ereg ( '/logout', $_SERVER['SCRIPT_NAME'] ) ) {
	header ( 'Location: ' . $site_url . '/logout.php' );
}*/
 
$cookie_server = ereg_replace( 'http://|http://www.|www.', '', $_SERVER['SERVER_NAME'] );

ob_start();
//require_once ( 'db.php' );
//require_once ( 'header.php' );

function Logout() {
	global $site_url, $main_filename;
	echo '<b>Successfully Logged Out!</b>
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
		echo '						<a href="',  $GLOBALS['HTTP_REFERER'], '">Click here go back to where you came from</a><br />
						<a href="', $site_url, '">Click here to go back to the frontpage</a>
';
	}
}

function ErrorDisplay( $msg ) {
echo '<span style="color: red;"><b>', $msg, '</b></span>';
require_once ( 'login_form.php' );
}
?>

<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; text-align: center; height: 100%;" class="VerdanaSize1Main">
	<tr>
		<td style="text-align: center; vertical-align: middle;">
			<table cellpadding="0" cellspacing="0" style="width: 100%;" class="VerdanaSize1Main">
				<tr>
					<td style="text-align: center;"><?php
if ( isset ( $user_B7 ) ) {
//	header( 'Set-Cookie: user_id=\'\'; path=/; domain=.www.bleach7.com');
//	header( 'Set-Cookie: password=\'\'; path=/; domain=.www.bleach7.com');
	set_cookie ( 'user_id', '', time()-3600, '/', 'bleach7.com' );
	set_cookie( 'password', '', time()-3600, '/', 'bleach7.com' );
	Logout();
//	setcookie( "user_id", "", '0', "/", "$_SERVER[HTTP_HOST]", 0 );
//	setcookie( "password", "", '0', "/", "$_SERVER[HTTP_HOST]", 0 );
	ob_end_flush();
}
else {
	ErrorDisplay( 'You are not currently logged in' );
	ob_end_flush();
}
?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>