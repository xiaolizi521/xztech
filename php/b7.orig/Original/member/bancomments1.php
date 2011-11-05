<?php
if ( isset ( $user_info['user_id'] ) ) {
	if ( $user_info['type'] >= 3 ) {
	$user = mysql_real_escape_string ( $_GET['user'] );
?>
<form action="index.php?page=member/ban" name="banform" method="post">
<table width='99%' bgcolor='#ACAC99' cellspacing='1' cellpadding='2'>

						<td bgcolor='white' style='padding-left:15px; padding-right:15px;'><br style='font-size: 5px;'>
						<br style='font-size: 5px;'>
						<fieldset>
							<legend>Ban User </font></legend>
						Please give username and length of ban. For the ban time please choose from calendar. <br>Please keep in mind the current time is : <b><?php echo date("h:i:s A"); ?><b><br>

							<br>

							<table cellpadding='0' cellspacing='0'>
								<tr>
									<td><i>Username</i></td>
									<td><i>Length</i></td>
								</tr>
								<tr>

								  <td >
<input name="username" type="text" style='width: 150px;' value='<?php echo $user;?>' size="30" >

									</td> <td >
<script language="JavaScript" src="member/calendar2.js"></script><!-- Date only with year scrolling -->


		



			
				<input type="Text" name="banlength" value="">
				<a href="javascript:cal7.popup();"><img src="/member/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the date"></a><br>

			
			    <script language="JavaScript">
			<!-- // create calendar object(s) just after form tag closed

				var cal7 = new calendar2(document.forms['banform'].elements['banlength']);
				cal7.year_scroll = true;
				cal7.time_comp = true;

			//-->
			    </script>
</td>
								</tr>
							</table>
						</fieldset>




<div align="center">
						<input type='submit' name='submit' value='Ban User'>
						</div>
					  </td>
				  </tr>
				</table></form>

<?php

}else{ //if user is not logged on
	print "Only moderators and above may access this facility.";
	}

}else{ //if user is not logged on
print "please log in to use this facility";
}

?>
