<?php
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] == 10 || $user_info['type'] == 21 || $user_info['type'] == 31 || $user_info['type'] >= 80 ) {
		if (!isbanned($user_info['user_id'] )) {
		$user = mysql_real_escape_string ( $_GET['user'] );
?>

<p><h2 align="center">Ban Users</h2><p>

<form action="index.php?page=member/ban" name="banform" method="post">
	<table width='99%' bgcolor='#ACAC99' cellspacing='1' cellpadding='2'>
		<tr>
			<td bgcolor='white' style='padding-left:15px; padding-right:15px;'><br style='font-size: 5px;' />
				<br style='font-size: 5px;' />
				<fieldset><legend>Ban User </font></legend>
					Please give username and length of ban. For the ban time please choose from calendar. <br />Please keep in mind the current time is : <b><?php echo date("h:i:s A"); ?><b><br />
					<br />
					<table cellpadding='0' cellspacing='0'>
						<tr>
							<td><i>Username</i></td>
							<td><i>Length</i></td>
							
						</tr>
						<tr>
							<td ><input name="username" type="text" style="width: 150px;" value="<?php echo $user;?>" size="30" /></td>
							<td ><script language="JavaScript" src="member/calendar2.js"></script><!-- Date only with year scrolling -->
								<input type="Text" name="banlength" value="" /><a href="javascript:cal7.popup();"><img src="/member/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the date" /></a><br />
								<script language="JavaScript"><!-- // create calendar object(s) just after form tag closed
									var cal7 = new calendar2(document.forms['banform'].elements['banlength']);
									cal7.year_scroll = true;
									cal7.time_comp = true;
									//--></script></td>
							</tr>
					</table>
					<table cellpadding='0' cellspacing='0'>
						<tr>
							<td><i>Reason</i></td>
						</tr>
						<tr>
							<td><input name="reason" type="text" style="width: 400px;" value="" /></td>
						</tr>
					</table>
				</fieldset>
				<div align="center">
					<input type='submit' name='submit' value='Ban User'>
				</div></td>
	  	</tr>
	</table>
</form>
<h2 align="center">UnBan Users</h2>
<form action="index.php?page=member/ban" name="unbanform" method="post">
	<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main" style="border-bottom: 1px solid #C3C3C3">
		<tr>
			<td style="text-decoration: underline; width: 10%;"><b>Tick</b></td>
			<td style="text-decoration: underline; width: 30%;"><b>Banned User</b></td>
			<td style="text-decoration: underline; width: 30%;"><b>Banned On</b></td>
			<td style="text-decoration: underline; width: 30%;"><b>Ban Expiry Date</b></td>
		</tr>
	</table>
<?php
$query= mysql_query("SELECT c.id id, c.user_id user_id, c.whenbanned whenbanned, c.banlength banlength, u.username username FROM comments_banned c, users u WHERE c.user_id = u.user_id ORDER BY whenbanned DESC");
while ($row =mysql_fetch_array($query)){
?>
	<table width="100%" cellpadding="3" cellspacing="0" border="0" class="main" style="border-bottom: 1px solid #C3C3C3">
		<tr>
			<td style="width: 10%;"><input type='checkbox' name='unbanuser[]' value='<?php print $row['id']; ?>'></td>
			<td style="width: 30%;"><b><?php print $row['username']; ?></b></td>
			<td style="width: 30%;"></b><?php print $row['whenbanned']; ?></td>
			<td style="width: 30%;"><?php print $row['banlength']; ?></td>
		</tr>
	</table>
<?php
}
?>
<div align="center">
	<input type="submit" name="unban" value="Unban Users" >
</div>
</form>
<?php
      	}else{ //if user is a banned mod.
            	print "<div align='center'><b>You may not use this facility while banned.</b></div>";
            	}

    }else{ //if user is not logged on
    	print "<div align='center'><b>Only moderators and above may access this facility.</b></div>";
    	}

}else{ //if user is not logged on
print "<div align='center'><b>please log in to use this facility.</b></div>";
}
?>


