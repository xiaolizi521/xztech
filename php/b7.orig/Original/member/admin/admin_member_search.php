<?php
	echo "Enter Member's Username
		<table style='height: 5px' cellpadding='0' cellspacing='0'>
			<tr>
				<td></td>
			</tr>
		</table>
		<input type='text' name='member_username' class='form' style='width: 300px'>
		<table style='height: 5px' cellpadding='0' cellspacing='0'>
				<tr>
				<td></td>
			</tr>
		</table>
		<input type='button' value='Search Member' class='form' onclick='document.form_member.submit()'>   <input type='button' value='New Search' class='form' onclick='document.form_member.reset()'>
		";
		echo "<input type='hidden' name='member_search'>
		";
		echo "<table style='height: 20px;' cellpadding='0' cellspacing='0'>
			<tr>
				<td></td>
			</tr>
		</table>
		";
?>