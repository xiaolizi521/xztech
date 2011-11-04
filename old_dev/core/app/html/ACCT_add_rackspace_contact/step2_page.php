<?php
require_once("step2_logic.php");
$theme_info = array( "title" => "Step 2: Select the Employee",
                     "next_url" => "step2_handler.php" );
require_once("./theme.php");

start_theme();
?>
<table border="0">
<tr>
<td>
<p>
Select the Employee that will be this account's
<?=$account_role_name?> contact.
</p>
<p>
<?php
#'
if( empty($show_all) ) {
    echo "<b>Showing</b> Only $team_name<br>";
    echo '<input type="submit" name="show_all" value=" Show All ';
    echo "$account_role_name";
    echo ' Contacts " class="data">';
} else {
    echo "<b>Showing:</b> All Contacts<br>";
    echo '<input type="submit" name="hide_all" value=" Show Only ';
    echo "$team_name";
    echo ' Contacts " class="data">';
}
?>
</p>
</td>
<td>
<select name="contact_id" size="9">
<?php
foreach( $people as $id => $name ) {
    echo "<option value=\"$id\"";
    if( $id == $contact_id ) {
        echo " SELECTED";
    }
    echo "> $name </option>\n";
}
?>
</select>
</td>
</tr>
</table>
<p>
Then click "Next" to continue.
</p>
</td>
</tr>
<tr> 
<td align="right"> 

<?php if( empty($SESSION_no_step1) ): ?>
<input type="submit" name="back" value=" &lt;-- Back " class="data">
<?php endif; ?>
<input type="submit" value=" Next --&gt; " class="data">
<?php
end_theme();

?>
