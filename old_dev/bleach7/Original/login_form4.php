<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################
?>

<form method="post" action="<?php echo "$site_path/login" ?>"><table cellpadding="2" cellspacing="0" class="main">
	<tr> 
		<td align="left"><b>Username:</b></td>
		<td width="7"></td>
		<td><input type="username" name="username" size="12" class="form"></td>
	</tr>
	<tr> 
		<td align="left"><b>Password:</b></td>
		<td width="7"></td>
		<td><input type="password" name="password" size="12" class="form"></td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" align="center" class="main">
	<tr>
		<td align="center"><input type="checkbox" name="cookieuser" checked><b>Remember Info?</b></td>
	</tr>
	<tr>
		<td height="7"></td>
	</tr>
	<tr>
		<td align="center"><a href="<?php echo "$site_path/register" ?>"><b>Not Registered?</b></a><br /><a href="<?php echo "$site_path/pwrecover" ?>"><b>Forgot Password?</b></a></td>
	</tr>
	<tr>
		<td height="7"></td>
	</tr>
	<tr>
		<td align="center"><input type="submit" name="login_submit" value="Login!" size="20" class="form"></td>
	</tr>
</table>
</form>