<html>
<table>
<tr><td>
    <table border=1>
        <tr><th colspan=4> Available Monitors </th></tr>
        {foreach name=monitors item=monitor from=$monitors}
            {if $monitor.selected }
            <tr bgcolor="grey">
            {else}
            <tr>
            {/if}
            <td>
                <a href="custom_monitor_edit.php?id={$monitor.id}">[Edit]</a>
                <a href="custom_monitor_delete.php?id={$monitor.id}">[Delete]</a>
            </td>
            <td> {$monitor.name} </td>
            <td> {$monitor.points} </td>
            <td> {$monitor.description} </td>
            </tr>
        {/foreach} 
    </table>
</td></tr>
<tr><td>
    <a href="custom_monitor_add.php"> Add New Monitor </a>
</td></tr>
</table>
</html>

