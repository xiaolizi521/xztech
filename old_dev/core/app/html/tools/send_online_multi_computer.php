<?
require_once("menus.php");
require_once("CORE_app.php");
require_once("class.mailer.php");
require_once("act/ActFactory.php");

$account_number = $_POST["account_number"];
if (isset($_POST["cids"])) {
    $cids = $_POST["cids"];
}

$SERVERS_TO_PROCESS_AT_ONCE = 5;

if (empty($account_number)) {
    DisplayError("account_number required");
    exit();
}
?>
<html>
    <head><title>CORE: Resending Online Messages</title></head>
    <body onload="javascript:documentCompleted();">
<?
// optional text to display (usually previous runs of this page when there are a lot of servers)
$prepended_message = isset($_POST["prepended_message"]) ? $_POST["prepended_message"] : '';

$server_counter = 0;
foreach ($_POST as $varName => $value) {
    if (strpos($varName, "srvr") === 0 && $value == "true") {
        $server_counter++;
    } elseif (strpos($varName, "cont") === 0 && $value == "true") {
        if (isset($cids)) {
            $cids .= ', ' . substr($varName, 4);
        } else {
            $cids = substr($varName, 4);
        }
    }
}

if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
    ?>
        <form name="theform" method="POST">
            <input type="hidden" name="account_number" value="<?=$account_number?>" />
            <input type="hidden" name="cids" value="<?=$cids?>" />
    <?php
}

echo "<h1>Resending Online Messages</h1>";
echo "<h2>You must leave this window open until the entire task has completed.</h2>";
echo "<p>";
echo $prepended_message;
$server_index = 0;
foreach ($_POST as $varName => $value) {
    if (strpos($varName, "srvr") === 0 && $value == "true") {
            $server_index++;
            if ($server_index <= $SERVERS_TO_PROCESS_AT_ONCE) {
                $server_number = substr($varName, 4);
                $server = new RackComputer;
                $server->Init($account_number, $server_number, $db);
                if (!$server->IsComputerGood()) {
                    print "<b>Unable to load any information about server number $server_number.</b> This server may no longer exist.<br />";
                    if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                        $prepended_message .= "<b>Unable to load any information about server number $server_number.</b> This server may no longer exist.<br />";
                    }
                }
                if (in_array($server->WhatOS(), array("Firewall - Cisco ASA", "Firewall - Cisco PIX", "Load-Balancer", "Netscreen"))) {
                    print "<b>Skipping server number $server_number.</b> Firewalls and Load Balancers do not have an Online/Complete message. The security or networking tech is responsible for notifying the customer.<br />";
                    if ($server_counter > $SERVERS_TO_PROCESS_AT_ONCE) {
                        $prepended_message .= "<b>Skipping server number $server_number.</b> Firewalls and Load Balancers do not have an Online/Complete message. The security or networking tech is responsible for notifying the customer.<br />";
                    }
                }
                if (!empty($cids)) {
                    if ( ! is_array( $cids ) ) {
                        $cids = array( $cids );
                    }
                    $server->ResendCustOnlineNotice($cids);
                }
        } else {
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
</html>
