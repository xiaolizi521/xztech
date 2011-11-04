<?php
			if ( isset ( $id ) && !empty ( $id ) ) { //editing donator has an id
				$id = mysql_real_escape_string ( $_GET[id] );
				$result_edit_donator = mysql_query( "SELECT * FROM donator WHERE id=$id" );
				$edit_donator = mysql_fetch_array( $result_edit_donator );
				if ( mysql_num_rows ( $result_edit_donator ) > 0 ) { //a valid donator id is found
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
<table cellpadding='5' cellspacing='0' border='0' class='main'>
	<tr>
		<td>Donator's Name</td>
		<td><input type='text' name='donator' style='width: 410px' value='".stripslashes ( $edit_donator['donator'] )."' class='form'></td>
	</tr>
	<tr>
		<td>Total Amount Donated</td>
		<td>$<input type='text' name='amount' style='width: 100px' value='".stripslashes ( $edit_donator['amount'] )."' class='form'></td>
	</tr>
</table>
<br />
<td align='center'><input type='submit' name='edit_donator' value='Edit Donator' class='form'> <input type='button' value='Reset Fields' class='form' onclick='form_index.reset()'> <input type='button' value='Go Back' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=index&amp;action=donator\"'></td>
";
				}
			}
			else { //no valid donator id found
				echo "<p style='text-align: center;'><b>Invalid Donator ID</b></p>";
			}
?>
<tr style="visibility:hidden;"></tr>