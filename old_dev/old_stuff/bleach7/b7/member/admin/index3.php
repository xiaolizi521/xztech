<?php
ob_start();
include ( "header.php" );
include ( "../settings.php" );
if ( !isset ( $_COOKIE['user_id'] ) && !isset ( $_COOKIE['password'] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
exit();
}

$auth = 0; 
if ( isset ($_GET['username']) && isset($_GET['password']) ) { 
include ( "../db.php" );
$enter_username = $_GET['username'];
$enter_password = md5 ( $_GET['password'] );
$result_userinfo = mysql_query ( "SELECT * FROM users WHERE username='$enter_username' AND password='$enter_password'" );
$user_info = mysql_fetch_array ( $result_userinfo );
if ( mysql_num_rows ( $result_userinfo ) != 0 && $user_info['type'] >= 20 && $user_info['user_id'] == $_COOKIE['user_id'] && $user_info['password'] == $_COOKIE['password'] ) 
{ 
$auth = 1; 
} 
} 

if ( $auth = 1 ) { 
echo'
<html>
<head>
<title>Bleach7 - Admin Control Panel</title>
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

';
}
else
{

echo'
<fieldset>
<form method="post" action="index.php"><table cellpadding="2" cellspacing="0" align="center" class="main">
	<tr>
		<td>Username:<br /><input type="text" name="username" style="width: 325px" class="form" /></td>
	</tr>
	<tr>
		<td>Password:<br /><input type="password" name="password" style="width: 325px" class="form" /></td>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" class="main">
				<tr>
					<td align="left"></td>
					<td align="right"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td align="right"><input type="submit" name="auth" value="Login!" size="20" class="form" />   <input type="button" value="Reset Fields" onclick="document.login_form.reset()" class="form" /></td>
	</tr>
</table>
</form>
</fieldset>
';
exit;
}
?>
