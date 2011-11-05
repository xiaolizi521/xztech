<?php
$theme_info = array( "title" => "Unauthorized!" );

require_once("./theme.php");

start_theme();
?>
<p>
You are not allowed to add Rackspace contacts to this account!
</p>
<p>
Only members of the support team assigned to this account are allowed to
add rackspace contacts.
</p>
<p>
<b>Click "Close" to close this window.</b>
</p>
</td>
</tr>
<tr> 
<td align="right"> 

<input type="submit" name="start" value=" Close " class="data"
       onClick="window.close();">
<?php
end_theme();

?>