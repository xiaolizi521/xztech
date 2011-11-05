<?php
ob_start();
include ( "header.php" );
include ( "../settings.php" );
if ( !isset ( $_COOKIE['user_id'] ) && !isset ( $_COOKIE['password'] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
exit();
}

$auth = false; 
if ( isset ( $PHP_AUTH_USER ) && isset ( $PHP_AUTH_PW ) ) { 
include ( "../db.php" );
$enter_username = $PHP_AUTH_USER;
$enter_password = md5 ( $PHP_AUTH_PW );
$result_userinfo = mysql_query ( "SELECT * FROM users WHERE username='$enter_username' AND password='$enter_password'" );
$user_info = mysql_fetch_array ( $result_userinfo );
if ( mysql_num_rows ( $result_userinfo ) != 0 && $user_info['type'] >= 20 && $user_info['user_id'] == $_COOKIE['user_id'] && $user_info['password'] == $_COOKIE['password'] ) { 
$auth = true; 
} 
} 

if ( !$auth ) { 
header ( "WWW-Authenticate: Basic realm=$sitetitle Admin Control Panel" ); 
header ( "HTTP/1.0 401 Unauthorized" ); 
echo "<table height='100%' cellpadding='0' cellspacing='0' align='center' valign='top' class='main'><tr><td align='center'><b>Authentication Failed!</b><table height='5'><tr><td></td></tr></table>You have entered an invalid username and/or password<br>If you wish to try again, refresh the page</td></tr></table>";
exit; 

} else { 
?>
<html>
<head>
<title><?php echo $sitetitle ?> - Admin Control Panel</title>
<script type="text/javascript">
if (self.parent.frames.length != 0) {
self.parent.location.replace(document.location.href);
}
</script>
</head>

<frameset rows="25,*,25" framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
<frame src="admin.php?view=top" name="top" scrolling="no" frameborder="0" marginwidth="5" marginheight="5" noresize="yes" border="no" style="border-bottom: 1px solid #000000">
<frame src="admin.php?view=main" name="main" frameborder="0" marginwidth="20" marginheight="20" noresize="yes" border="no" style="overflow: auto" >
<frame src="admin.php?view=footer" name="footer" scrolling="no" frameborder="0" marginwidth="5" marginheight="5" noresize="yes" border="no" style="border-top: 1px solid #000000">
</frameset>

<noframes>
<body>
<p>Your browser does not support frames. Please get one that does!</p>
</body>
</noframes>
</html>

<?php
}
?>
