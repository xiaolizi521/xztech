<html>
<table border=1>
    <tr><th> Delete Server Monitor </th></tr>
    <form action="server_monitor_delete.php">
    <input type="hidden" name="action" value="DELETE_SERVER_MONITOR">
    <input type="hidden" name="id" value="{$id}">
    <tr>
        <td> <b> Are you sure you want to delete this? </b> </td>
    </tr>
    <tr>
        <td>Description: <pre>{$description}</pre></td>
    </tr>
    <tr>
        <td>Notes: <pre>{$notes}</pre> </td>
    </tr>
    <tr><td><INPUT type="submit" value="Delete">
         </td></tr>
    </form>
</table>
</html>
