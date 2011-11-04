<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Login :: What.CD</title>
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="-1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" />
	<link href="static/styles/public/style.css" rel="stylesheet" type="text/css" />
	<script src="static/functions/global.js" type="text/javascript"></script>
	<script src="static/functions/validate.js" type="text/javascript"></script>
</head>
<body>
<div id="head"></div>
<table cellpadding="0" cellspacing="0" border="0" id="maincontent">
	<tr>
		<td align="center" valign="middle">
			<div id="logo">
				<a href="index.php">Home</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="login.php">Login</a>
 
			</div>
<div style="width:320px;">
<script type="text/javascript" language="javascript">
//<![CDATA[
function formVal() {
	clearErrors('loginform');
	if (!$('username').value.match(/^[a-z0-9_?]+$/i)) { return showError('username','You did not enter a valid username.'); }
	if ($('password').value=="" || $('password').value.length>40 || $('password').value.length<6) { return showError('password','You entered an invalid password.'); }
}
//]]>
</script>
	<form name="loginform" id="loginform" method="post" action="login.php" onsubmit="return formVal();">
	You have <font color="green"><strong>6</strong></font> attempts remaining.<br /><br />
	<strong>WARNING:</strong> You will be banned for 6 hours after your login attempts run out.<br /><br />
	<table cellpadding="2" cellspacing="1" border="0" align="center">
		<tr valign="top">
			<td align="right">Username&nbsp;</td>
			<td align="left"><input type="text" name="username" id="username" class="inputtext" /></td>
		</tr>
		<tr valign="top">
			<td align="right">Password&nbsp;</td>
			<td align="left"><input type="password" name="password" id="password" class="inputtext" /></td>
		</tr>
		<tr valign="top">
			<td colspan="2" align="right">
				<input type="checkbox" id="keeplogged" name="keeplogged" value="1" />
				<label for="keeplogged">Keep me logged in</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><input type="submit" name="login" value="Log In!" class="submit" /></td>
		</tr>
	</table>
	</form>
	<br /><br />
	Lost your password? <a href="login.php?act=recover">Recover it here!</a>
</div>
		</td>
	</tr>
</table>
<div id="foot"><div id="copyleft"></div></div>
</body>
</html>