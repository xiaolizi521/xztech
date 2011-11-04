<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################
?>
<fieldset>
<form method="post" action="<?php echo "?page=member/login" ?>"><table cellpadding="2" cellspacing="0" align="center" class="main">
	<tr>
		<td>Username:<br /><input type="username" name="username" style="width: 325px" class="form"></td>
	</tr>
	<tr>
		<td>Password:<br /><input type="password" name="password" style="width: 325px" class="form"></td>
	</tr>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" class="main">
				<tr>
					<td align="left"><input type="checkbox" name="cookieuser" 	checked="checked">Remember Info?</td>
					<td align="right"><a href="<?php echo "?page=member/register" ?>">Not Registered?</a><br /><a href="<?php echo "?page=member/pwrecover" ?>">Forgot Password?</a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td align="right"><input type="submit" name="login_submit" value="Login!" size="20" class="form">   <input type="button" value="Reset Fields" onclick="document.login_form.reset()" class="form"></td>
	</tr>
</table>
</form>
</fieldset>










