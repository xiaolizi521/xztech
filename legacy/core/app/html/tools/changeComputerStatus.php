<?
require_once("CORE_app.php");
require_once("computerStatus.php");
require_once("xmlrpc_core_api.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html id="mainbody">
    <head>
        <title>CORE: Changing Status Of Multiple Servers</title>
    </head>
<?

$account_number = $_POST["account_number"];
$new_status = $_POST["new_status"];
$new_status_text = "";
if (isset($_POST["new_status_text"])) {
    $new_status_text = $_POST["new_status_text"];
} else {
    $new_status_query = $db->SubmitQuery("SELECT status FROM status_options WHERE status_rank = $new_status");
    $num = $new_status_query->numRows();
    for ($i = 0; $i < $num; $i++) {
        $row = $new_status_query->fetchArray($i);
        $new_status_text = $row["status"];
    }
    $new_status_query->freeResult();
}
$reason = isset($_POST["reason"]) ? $_POST["reason"] : '';

// optional existing ticket number
$ticket_num = isset($_POST["ticket_num"]) ? $_POST["ticket_num"] : '';

// optional override from autoassigning IPs
$auto_assign_ip = isset($_POST["auto_assign_ip"]) ? $_POST["auto_assign_ip"] : '';

// optional text to display (usually previous runs of this page when there are a lot of servers)
$prepended_message = isset($_POST["prepended_message"]) ? $_POST["prepended_message"] : '';

$server_counter = 0;
foreach ($_POST as $varName => $value) {
	if (strpos($varName, "srvr") === 0 && $value == "true") {
        $server_counter++;
    }
}

function changeServerStatusViaXMLRPC( $server_num, $new_status, $reason="", $ticket_num="", $migr_days="", $new_migr_server ){
    $format = new xmlrpcmsg('changeComputerStatus', array(new xmlrpcval($server_num, "int"),
                                                          new xmlrpcval($new_status, "int"),
                                                          new xmlrpcval($reason, "string"),
                                                          new xmlrpcval($ticket_num, "string"),
                                                          new xmlrpcval($migr_days, "string"),
                                                          new xmlrpcval($new_migr_server, "string"),
                                                          new xmlrpcval("", "string") /* for offline reason */));

    $session_id = COREAUTH_GetSessionkey();
    $client=new rs_xmlrpc_client("/xmlrpc/Computer/::session_id::$session_id",
                                 XMLRPC_PYTHON_HOST, XMLRPC_PYTHON_PORT);
    $client->setSSLVerifyPeer(0);
    $request=$client->send($format, XMLRPC_PYTHON_TIMEOUT, XMLRPC_PYTHON_SERVER_METHOD);

    if (!$request->faultCode()) {
        $value=$request->value();
        $result = $value->scalarval();
        return $result;
    }
    else {
        error_log("Error trying to change server status. Error=\"".$request->faultString() . '"');
        trigger_error($request->faultString(), E_USER_WARNING);
        return $request->faultString();
    }
}

?><body onload="javascript:documentCompleted();"><?php
if ($server_counter > 5) {
    ?>
        <form name="theform" method="POST">
            <input type="hidden" name="account_number" value="<?=$account_number?>" />
            <input type="hidden" name="new_status" value="<?=$new_status?>" />
            <input type="hidden" name="new_status_text" value="<?=$new_status_text?>" />
            <textarea style="display: none" name="reason"><?=stripslashes($reason)?></textarea>
            <input type="hidden" name="ticket_num" value="<?=$ticket_num?>" />
            <input type="hidden" name="auto_assign_ip" value="<?=$auto_assign_ip?>" />
    <?php
}

echo "<h1>Changing Servers' Status To $new_status_text</h1>";
echo "<h2>You must leave this window open until the entire task has completed.</h2>";
echo "<p>";
echo $prepended_message;
$server_index = 0;
foreach ($_POST as $varName => $value) {
	if (strpos($varName, "srvr") === 0 && $value == "true") {
        $server_index++;
        $server_number = substr($varName, 4);
        // optional params for migration status
        $migr_days = isset($_POST["migr_days$server_number"]) ? $_POST["migr_days$server_number"] : '';
        $migr_new_server = $_POST["migr_new_server$server_number"];
        if ($server_index <= 5) {
            $computer = new RackComputer;
            $computer->Init($account_number, $server_number, $db);
            $restriction = getUpgradeRestriction($computer);
            $current_status = $computer->getData("status_rank");
            $current_status_text = "";
            $current_status_query = $db->SubmitQuery("SELECT status FROM status_options WHERE status_rank = $current_status");
            $num = $current_status_query->numRows();
            for ($i = 0; $i < $num; $i++) {
                $row = $current_status_query->fetchArray($i);
                $current_status_text = $row["status"];
            }

            $current_status_query->freeResult();
            if (!empty($restriction)) {
                echo "#$server_number " . $computer->getData('server_name') . ": Changing status from $current_status_text to $new_status_text: RESTRICTED: $restriction<br />";
                if ($server_counter > 5) {
                    $prepended_message .= "#$server_number " . $computer->getData('server_name') . ": Changing status from $current_status_text to $new_status_text: RESTRICTED: $restriction<br />";
                }
            } else {
            	
                $transition_msg =  "#$server_number " . $computer->getData('server_name') . ": Changing status from $current_status_text to $new_status_text <br /> ";
                $rpc_result = changeServerStatusViaXMLRPC($server_number, $new_status, $reason, $ticket_num, $migr_days, $migr_new_server);

                if (!empty($rpc_result)) {
                    $transition_msg .=  "<font color=red><b>" . $rpc_result . "</b></font><br /><br />";
                }

                echo "$transition_msg";

                if ($server_counter > 5) {
                    $prepended_message .= $transition_msg;
                    $prepended_message .= "<br />";
                }

                
            }
        } else { // more than 5 servers
            echo "<input type=\"hidden\" name=\"$varName\" value=\"true\" />";
            echo "<input type=\"hidden\" name=\"migr_days$server_number\" value=\"$migr_days\" />";
            echo "<input type=\"hidden\" name=\"migr_new_server$varName\" value=\"$migr_new_server\" />";
            
        }
	}
}
echo "</p>";

if ($server_counter > 5) {
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
    updateAttributeCache($account_number, $current_status, $new_status);
}

?>
    </body>
</HTML>

