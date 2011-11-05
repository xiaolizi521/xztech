<?php require_once("CORE_app.php"); ?>
<?
$computer=new RackComputer;

if (!isset($customer_number)) {
    $customer_number = $db->GetVal("
        select customer_number
        from server
        where computer_number = $computer_number");
}

$computer->Init($customer_number, $computer_number, $db);

if (!$computer->IsComputerGood()) {
    DisplayError("Unable to load any information about computer number $computer_number This computer may no longer exist.  If you continue to have problems contact the database administrator");
}

if(isset($command) and $command == "EDIT_INSTRUCTIONS") {
    $computer->SetEmergencyInstructions($info);
    $close_self = True;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>

<? if (isset($close_self)) { ?>
<script type="text/javascript">
self.opener.location.reload();
self.close();
</script>
<? } ?>

<title>CORE: Edit Device Management Guidelines</title>
<link href="/css/core2_basic.css" type="text/css" rel="stylesheet">
</head>
<body>

<form action="edit_emergency_instructions.php" method="POST">
<input type="hidden" name="command" value="EDIT_INSTRUCTIONS">
<input type="hidden" name="customer_number" value="<?= $customer_number; ?>">
<input type="hidden" name="computer_number" value="<?= $computer_number; ?>">

<table class="core_blue_dialog">
    <tr>
        <th>Edit Device Management Guidelines: #<?= $customer_number; ?>-<?= $computer_number; ?></th>
    </tr>
    <tr>
        <td>
            <? if (isset($error_message)) { ?>
                <h2 style="color:#f00;"><?= $error_message; ?></h2>
            <? } ?>
            <textarea cols="60" rows="13" name="info[instructions]" wrap="hard"><?HTprint($computer->getData("emergency_instructions"));?></textarea>
            <p class="small">Note: Due to security concerns, Device Management Guidelines will not display HTML.</p>
            <div class="buttons">
                <input type="submit" value="Save">
                <input type="button" value="Cancel" onclick="self.close();">
            </div>
        </td>
    </tr>
</table>
</form>

</body>
</html>
