<?php
require_once("CORE_app.php"); 
require_once("class.parser.php");
require_once("class.mailer.php");
require_once("cancel_computer_handler.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html id="mainbody">
    <head>
        <title>CORE: Queueing Multiple Servers For Cancellation</title>
    </head>
<?php
$SCHEDULE_HOUR = 8; # hour when servers show up in queue
$COMMAND_QUEUE_CANCELLATION = "ADD_TO_QUEUE";
$COMMAND_UNQUEUE_CANCELLATION = "NOT_AN_OPTION_HERE";
$COMMAND_OVERRIDE = "NOT_AN_OPTION_HERE";
$COMMAND_CONFIRM_CANCELLATION = "NOT_AN_OPTION_HERE";
$COMMAND_CANCEL_NOW = "NOT_AN_OPTION_HERE";
$MOVING_QUESTION = "moving question";
$MAX_DAYS_IN_ADVANCE = 150; # limit cancellation scheduling
$SERVERS_TO_PROCESS_AT_ONCE = 1;

if ($_REQUEST['command'] != $COMMAND_QUEUE_CANCELLATION) {
    ?><body><p>This path of execution is only for multiple cancellation queueing of servers; no command values other than ADD_TO_QUEUE are allowed.</p></body></html><?php
    exit();
}

if (!in_dept("SUPPORT|ACCOUNT_EXECUTIVE|AR|PRODUCTION|AUP")) {
    ?><body><p>You do not have access to this service.</p></body></html><?php
    exit();
}

if (strlen($_REQUEST['reason_info']) < 20)
{
    ?><body><p>Please enter more info on how we could prevent the customer from leaving.</p></body></html><?php
    exit();
}

if (empty($_REQUEST['notification_checkbox']))
{
    ?><body><p>You must check the box "I have notified the customer."</p></body></html><?php
    exit();
}

$account_number = $_REQUEST["account_number"];

// optional text to display (usually previous runs of this page when there are a lot of servers)
$prepended_message = isset($_REQUEST["prepended_message"]) ? $_REQUEST["prepended_message"] : '';

$server_counter = 0;
foreach ($_REQUEST as $varName => $value) {
    if (strpos($varName, "srvr") === 0 and $value == "true") {
        $server_counter++;
    }
}
?>
<body onload="javascript:documentCompleted();">
<?php
if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
    ?>
        <form name="theform" method="POST">
            <input type="hidden" name="account_number" value="<?=$account_number?>" />
            <input type="hidden" name="command" value="ADD_TO_QUEUE" />
            <input type="hidden" name="group" value="<?=$_REQUEST['group']?>" />
            <input type="hidden" name="reason_number" value="<?=$_REQUEST['reason_number']?>" />
            <input type="hidden" name="continue_hosting" value="<?=$_REQUEST['continue_hosting']?>" />
            <input type="hidden" name="future_hosting_method" value="<?=stripslashes($_REQUEST['future_hosting_method'])?>" />
            <input type="hidden" name="chose_competitor" value="<?=$_REQUEST['chose_competitor']?>" />
            <input type="hidden" name="competitor" value="<?=stripslashes($_REQUEST['competitor'])?>" />
            <input type="hidden" name="future_consideration" value="<?=$_REQUEST['future_consideration']?>" />
            <input type="hidden" name="month" value="<?=$_REQUEST['month']?>" />
            <input type="hidden" name="day" value="<?=$_REQUEST['day']?>" />
            <input type="hidden" name="year" value="<?=$_REQUEST['year']?>" />
            <input type="hidden" name="preserve_primary_ip" value="<?=$_REQUEST['preserve_primary_ip']?>" />
            <input type="hidden" name="preserve_additional_ip" value="<?=$_REQUEST['preserve_additional_ip']?>" />
            <input type="hidden" name="notification_checkbox" value="<?=$_REQUEST['notification_checkbox']?>" />
            <textarea name="reason_info" style="display: none"><?=stripslashes($_REQUEST['reason_info'])?></textarea>
            <textarea name="competitor_benefits" style="display: none"><?=stripslashes($_REQUEST['competitor_benefits'])?></textarea>
            <textarea name="ip_notes" style="display: none"><?=stripslashes($_REQUEST['ip_notes'])?></textarea>
    <?php
}

echo "<h1>Queueing Servers For Cancellation</h1>";
echo "<h2>You must leave this window open until the entire task has completed.</h2>";
echo "<p>";
echo $prepended_message;
$server_index = 0;
foreach ($_REQUEST as $varName => $value) {
    if (strpos($varName, "srvr") === 0 and $value == "true") {
        $server_index++;
        if ($server_index <= $SERVERS_TO_PROCESS_AT_ONCE) {
            $server_number = substr($varName, 4);
            $computer = new RackComputer;
            $computer->Init($account_number, $server_number, $db);
            if (!$computer->IsComputerGood()) {
                echo "#$server_number " . $computer->getData('server_name') . ": <b>Server no longer exists</b><br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <b>Server no longer exists</b><br />";
                }
                continue;
            }
            if (!$computer->customer->IsCustomerGood()) {
                echo "#$server_number " . $computer->getData('server_name') . ": <b>Account no longer exists</b><br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <b>Account no longer exists</b><br />";
                }
                continue;
            }
            $result = $db->SubmitQuery("SELECT t1.oid,t1.* 
                                        FROM queue_cancel_server t1, server_status_all t2
                                        WHERE t1.computer_number = $server_number
                                              AND t1.computer_number = t2.computer_number
                                              AND (t1.completed = 'f' OR t1.completed IS NULL OR t2.status_number = -1)
                                        ORDER BY sec_created DESC LIMIT 1");
            if ($db->NumRows($result) > 0) {
                echo "#$server_number " . $computer->getData('server_name') . ": <i>Previously queued</i><br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <i>Previously queued</i><br />";
                }
                continue;
            } else {
                $already_queued_data['sec_due_offline'] = '';
                $already_queued_data['reason_number'] = '';
                $already_queued_data['reason_info'] = '';
                $already_queued_data['support_incidents'] = '';
                $already_queued_data['chose_competitor'] = '';
                $already_queued_data['competitor'] = '';
                $already_queued_data['competitor_benefits'] = '';
                $already_queued_data['oid'] = '';
                $already_queued_data['reason_number'] = '';
                $already_queued_data['preserve_primary_ip'] = '';
                $already_queued_data['preserve_additional_ip'] = '';
                $already_queued_data['ip_notes'] = '';
                echo '<div style="display: none">';
                $error = handleCancellation($_REQUEST, $computer, false/*not already queued*/, $already_queued_data, $account_number);
                echo '</div>';
                if (!empty($error)) {
                    echo "#$server_number " . $computer->getData('server_name') . ": <b>$error</b><br />";
                    if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                        $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <b>$error</b><br />";
                    }
                    continue;
                }
                echo "#$server_number " . $computer->getData('server_name') . ": Queued<br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": Queued<br />";
                }
            }

        } else { // more than $SERVERS_TO_PROCESS_AT_ONCE servers
            echo "<input type=\"hidden\" name=\"$varName\" value=\"true\" />";
        }
    }
}
echo "</p>";

if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
    ?>
            <textarea style="display: none" name="prepended_message"><?=$prepended_message?></textarea>
        </form>
        <script type="text/javascript">
            function documentCompleted() {
                window.scrollTo(0, 999999);
                window.setTimeout('document.theform.submit();', 500);
            }
        </script>
    <?php
} else {
    ?>
        <h3>Task has completed.</h3>
        <p><a href="/ACCT_main_workspace_page.php?account_number=<?=$account_number?>">Continue...</a></p>
        <script type="text/javascript">
            function documentCompleted() {
                window.scrollTo(0, 999999);
            }
        </script>
	    <?php
}

?>
    </body>
</HTML>
