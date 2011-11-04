<?php
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

			echo "<table cellpadding='5' cellspacing='0' class='main'>
	<tr>
		<td>Donator's Name</td>
		<td><input type='text' name='donator' style='width: 410px' value='' class='form'></td>
	</tr>
	<tr>
		<td>Total Amount Donated</td>
		<td>$<input type='text' name='amount' style='width: 100px' value='' class='form'></td>
	</tr>
	<tr>
		<td>* - Real Names Unknown</td>
	</tr>
</table>
<table cellpadding='5' cellspacing='0' class='main'>
	<tr>
		<td align='center'><input type='submit' name='add_donator' value='Add Donator' class='form'> <input type='button' value='Reset Fields' class='form' onclick='form_index.reset()'> <input type='button' value='Go Back' class='form' onclick='document.location=\"$PHP_SELF?view=main&amp;type=index&amp;action=donator\"'></td>
	</tr>
</table>";
?>
