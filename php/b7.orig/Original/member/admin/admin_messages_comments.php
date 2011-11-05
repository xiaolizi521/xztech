<?PHP
echo "<table cellpadding='5' cellspacing='0' class='main'>
			";
echo"
<tr>
		<td valign='top'>Comment Post</td>
		<td><textarea name='comment_message' style='width: 420px; height: 320px' class='form'></textarea></td>
	</tr>
	<tr>
		<td></td>
		<input type='hidden' name='newsid' value='$id'> 
		<td align='center'><input type='submit' name='comment_add' value='Add comment' class='form'>   <input type='button' value='Reset Fields' class='form' onclick='form_messages.reset()'></td>
	</tr>";
echo "</table>";

?>