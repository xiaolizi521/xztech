<?php
require_once("finish_logic.php");
$theme_info = array( "title" => "Finish: Confirm and Add",
                     "next_url" => "finish_handler.php" );
require_once("./theme.php");

start_theme();
?>
<p>
You have selected to add
<b><?=$contact_name?> (<?=$contact_id?>)</b> as
<?php if( $use_an ) echo "an "; else echo "a "; ?>
<b><?=$account_role_name?> </b> contact
for account #<?=$account_number?> (<?=$account_name?>) (<?=$account_id?>).
</p>
<p>
Then click "Finish" to add this contact.
</p>
</td>
</tr>
<tr> 
<td align="right"> 

<input type="submit" name="back" value=" &lt;-- Back " class="data">
<input type="submit" value=" Finish " class="data">
<?php
end_theme();

?>