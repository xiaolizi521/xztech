<?php
require_once("CORE_app.php"); 
require_once("class.parser.php");
require_once("class.mailer.php");
require_once("cancel_computer_handler.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html id="mainbody">
    <head>
        <title>CORE: Requeueing Multiple Servers From Cancellation</title>
    </head>
<?php
$SCHEDULE_HOUR = 8; # hour when servers show up in queue
$COMMAND_QUEUE_CANCELLATION = "NOT_AN_OPTION_HERE";
$COMMAND_UNQUEUE_CANCELLATION = "NOT_AN_OPTION_HERE";
$COMMAND_OVERRIDE = "REQUEUE_IN_QUEUE";
$COMMAND_CONFIRM_CANCELLATION = "NOT_AN_OPTION_HERE";
$COMMAND_CANCEL_NOW = "NOT_AN_OPTION_HERE";
$MOVING_QUESTION = "moving question";
$MAX_DAYS_IN_ADVANCE = 150; # limit cancellation scheduling
$SERVERS_TO_PROCESS_AT_ONCE = 1;

if ($_REQUEST['command'] != $COMMAND_OVERRIDE) {
    ?><body><p>This path of execution is only for multiple requeueing the cancellation of servers; no command values other than REQUEUE_IN_QUEUE are allowed.</p></body></html><?php
    exit();
}

if (!in_dept("SUPPORT|ACCOUNT_EXECUTIVE|AR|PRODUCTION|AUP")) {
    ?><body><p>You do not have access to this service.</p></body></html><?php
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
            <input type="hidden" name="command" value="REQUEUE_IN_QUEUE" />
            <input type="hidden" name="month" value="<?=$month?>" />
            <input type="hidden" name="day" value="<?=$day?>" />
            <input type="hidden" name="year" value="<?=$year?>" />
    <?php
}

echo "<h1>Requeueing Servers From Cancellation</h1>";
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
            if ($db->NumRows($result) < 1) {
                echo "#$server_number " . $computer->getData('server_name') . ": <i>Not yet queued, so cannot requeue</i><br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <i>Not yet queued, so cannot requeue</i><br />";
                }
                continue;
            } else {
                $already_queued_data = $db->FetchArray($result, 0);
                $formdata['account_number'] = $account_number;
                $formdata['command'] = $COMMAND_OVERRIDE;
                $formdata['computer_number'] = $already_queued_data['computer_number'];
                $formdata['reason_number'] = $already_queued_data['reason_number'];
                $formdata['reason_info'] = addslashes($already_queued_data['reason_info']);
                $formdata['chose_competitor'] = $already_queued_data['chose_competitor'];
                $formdata['competitor_benefits'] = addslashes($already_queued_data['competitor_benefits']);
                $formdata['continue_hosting'] = $already_queued_data['continue_hosting'];
                $formdata['competitor'] = addslashes($already_queued_data['competitor']);
                $formdata['future_consideration'] = $already_queued_data['future_consideration'];
                $formdata['future_hosting_method'] = $already_queued_data['future_hosting_method'];
                $formdata['month'] = $_POST['month'];
                $formdata['day'] = $_POST['day'];
                $formdata['year'] = $_POST['year'];
                $formdata['preserve_primary_ip'] = $already_queued_data['preserve_primary_ip'];
                $formdata['preserve_additional_ip'] = $already_queued_data['preserve_additional_ip'];
                $formdata['ip_notes'] = $already_queued_data['ip_notes'];
                $formdata['notification_checkbox'] = 'true';
                echo '<div style="display: none">';
                $error = handleCancellation($formdata, $computer, true/*already queued*/, $already_queued_data, $account_number);
                echo '</div>';
                if (!empty($error)) {
                    echo "#$server_number " . $computer->getData('server_name') . ": <b>$error</b><br />";
                    if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                        $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": <b>$error</b><br />";
                    }
                    continue;
                }
                echo "#$server_number " . $computer->getData('server_name') . ": Requeued<br />";
                if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": Requeued<br />";
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
