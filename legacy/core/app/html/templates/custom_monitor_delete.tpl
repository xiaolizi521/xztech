<html>
<body>
{if $error}
    <h2> {$error} </h2>
{else}
<h2>Are you sure you want to delete the Custom Monitor: {$name} ? </h2>
    <table>
    <tr><td>
    <form action="custom_monitor_delete.php">
    <input type="hidden" name="action" value="DELETE_MONITOR">
    <input type="hidden" name="id" value="{$id}">
    </td></tr>
    <tr><td><INPUT type="submit" value="Delete">
        <INPUT type="Reset"> </td></tr>
    </form>
    </table>
{/if}
</body>
</html>
