<?
    include('CORE_app.php');

    $customer= new RackCustomer;
    $customer->Init($account_number, $db);
    $computers = $customer->LoadComputers();
    $computer_list = $customer->computer_list;
    $firewalls = array();
    foreach ($computer_list as $comp)
        if ($comp->isNetDevice() and
            $comp->data['status_number'] <= STATUS_ONLINE)
                $firewalls[] = $comp;
    include('tools_body.php');
?>
<table border="1"><tr><td>
<form method="get" action="/tools/organize_net_device.php">
<input type="hidden" name="firewall_return" value="<?= htmlspecialchars($firewall_return) ?>">
<table cellpadding="5" cellspacing="0" border="0" width="550">
<tr><td colspan="4" nowrap="true" bgcolor="#003399"><font color="#FFFFFF"><b>Select a device to be Organized</b></font></td><td bgcolor="#003399"><font color="#FFFFFF"><b>Children</b></font></td></tr>
<?php
    $i = 0;
    $checked = 'checked';
    foreach ($firewalls as $firewall)
    {
        $row_color = ($i % 2) ? '#dce6f0' : '#ffffff';
?>
<tr>
<td bgcolor="<?= $row_color ?>"><input type="radio" name="device_number" value="<?= $firewall->data['computer_number'] ?>" <?= $checked ?>></td>
<td bgcolor="<?= $row_color ?>"><IMG SRC='<?= getIconForServer($firewall->data['computer_number']) ?>'></td>
<td bgcolor="<?= $row_color ?>"><?= $firewall->data['computer_number'] ?></td>
<td bgcolor="<?= $row_color ?>"><?= $firewall->data['server_name'] ?> (<?= $firewall->data['server_nickname'] ?>)</td>
<td bgcolor="<?= $row_color ?>"><?= sizeof($firewall->getConnectedComputerNumbers()) ?></td>
</tr>
<?php
        $checked = '';
        $i++;
    }
?>
</table>
<div align="right"><input type="submit" value="Configure"></div>
</form>
</td></tr></table>
<br>
<form method='get'><input type='button' onclick="location.href='<?= htmlspecialchars($firewall_return) ?>'" value="Don't organize any devices"></form>
<?= page_stop() ?>
</html>

