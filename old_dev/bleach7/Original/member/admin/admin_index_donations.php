<?php
				$result_donation_info = mysql_query( "SELECT * FROM index_info" );
				$donation_info = mysql_fetch_array( $result_donation_info );
				if ( count ( $errors ) > 0 ) {
					echo "<table cellpadding='0' cellspacing='0' class='main'>
						<tr>
							<td>";
						DisplayErrors();
					echo "</td>
							</tr>
						</table>
						";
				}
				echo "<br />
<table cellpadding='0' cellspacing='0' class='main'>
	<tr>
		<td style='width:200px;'>Requested Monthly Donation</td>
		<td>$<input type='text' name='donations' style='width: 35px' value='".stripslashes ( $donation_info['goal'] )."' class='form'></td>
	</tr>
	<tr>
		<td style='width:200px;'>Cost Amount for Main Site</td>
		<td>$<input type='text' name='main_server' style='width: 35px' value='".stripslashes ( $donation_info['main_server'] )."' class='form'></td>
	</tr>
	<tr>
		<td style='width:200px;'>Cost Amount for Media Server 1</td>
		<td>$<input type='text' name='media1' style='width: 35px' value='".stripslashes ( $donation_info['media1'] )."' class='form'></td>
	</tr>
	<tr>
		<td style='width:200px;'>Cost Amount for Media Server 2</td>
		<td>$<input type='text' name='media2' style='width: 35px' value='".stripslashes ( $donation_info['media2'] )."' class='form'></td>
	</tr>
	<tr>
		<td style='width:200px;'>Current Month</td>
		<td>&nbsp;&nbsp;<input type='text' name='month' style='width: 35px' value='".stripslashes ( $donation_info['month'] )."' class='form' readonly='true'></td>
	</tr>
	<tr>
		<td style='width:200px;'>Current Year</td>
		<td>&nbsp;&nbsp;<input type='text' name='year' style='width: 35px' value='".stripslashes ( $donation_info['year'] )."' class='form' readonly='true'></td>
	</tr>
</table>
<br />
<td align='center'><input type='submit' name='edit_donation_info' value='Edit Donation Info' class='form'> <input type='submit' name='new_month' value='New Month' class='form'> <input type='button' value='Reset Fields' class='form' onclick='form_index.reset()'> <input type='button' value='Go Back' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=index\"'></td>
";
?>