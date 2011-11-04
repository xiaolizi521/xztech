<html>
<table>
<tr><td>
    <table>
        <form action="custom_monitor_edit.php">
        <input type="hidden" name="action" value="EDIT_MONITOR">
        <input type="hidden" name="id" value="{$id}">
        <tr><th align=left><b> Edit Monitor </b> </th></tr>
        {if $edit_successful}
            <tr><td bgcolor="gray"> <h3>Edit Successful!  </h3>
                <a href="custom_monitors.php">Back to List of Monitors </a>
            </td></tr>    
        <tr><td> <BR> </td></tr>
        {/if}
        <tr><td>Name: <INPUT name="name" type="text" 
                size=60 value="{$name}"></td></tr>
        <tr><td>Points: <INPUT name="points" type="text" 
                size=5 value="{$points}"> </td></tr>
        <tr><td>Description:
            <TEXTAREA name="description" rows="20"
                cols="80">{$description}</TEXTAREA> </td></tr>
        <tr><td><INPUT type="submit" value="Send">
            <INPUT type="reset"> </td></tr>
        </form>
    </table>
</td>
</tr>
</table>
</html>

