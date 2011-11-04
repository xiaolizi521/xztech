<html>
<table border=1>
    <tr><th> Edit Server Monitor </th></tr>
    <form action="server_monitor_edit.php">
    <input type="hidden" name="action" value="EDIT_SERVER_MONITOR">
    <input type="hidden" name="id" value="{$id}">
    <tr>
        <td>Monitored URL:<br />
        <TEXTAREA name="description" rows="7"
            cols="80">{$description}</TEXTAREA> </td>
    </tr>
    <tr>
        <td>Match Text Notes:<br />
            <TEXTAREA name="notes" rows="7"
            cols="80">{$notes}</TEXTAREA> </td>
    </tr>
    <tr><td><INPUT type="submit" value="Send">
        <INPUT type="reset"> </td></tr>
    </form>
</table>
</html>
