<?php
require_once("step1_logic.php");
$theme_info = array( "title" => "Step 1: Select the Role",
                     "next_url" => "step1_handler.php" );
require_once("./theme.php");

start_theme();
?>
<table border="0">
<tr>
<td>
<p>
Select the role which the Rackspace employee will
fulfill for this account.
</p>
</td>
<td>
<select name="account_role" size="<?=sizeof($roles)?>">
<?php
foreach( $roles as $id => $name ) {
    echo "<option value=\"$id\"";
    if( $id == $account_role ) {
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

<input type="submit" value=" Continue --&gt; " class="data">
<?php
end_theme();

?>