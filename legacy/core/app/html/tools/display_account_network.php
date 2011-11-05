<?
require_once('network_diagram.php');

//tell the browser not to cache this page
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );

include('CORE_app.php');

$tree_url = "$py_app_prefix/account/tree.pt?account_number=$account_number";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: <? print("Display Account Network $customer_number-$account_number");?>
    </TITLE>
    <script language="JavaScript1.2"
        type="text/javascript">
    try {
        // Refreshes the tree
        top.frames["left"].document.location.href = 
        "<?=$tree_url?>";
    } catch(e) {
        // Do Nothing.
    }
    </script>
    <script language="JavaScript1.2"
            src="/script/popup.js"
            type="text/javascript"></script>     
<?php
$no_menu = true;
require_once("tools_body.php");
?>
    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
        function node_click(num, canBeOrganized) {
            if(canBeOrganized) {
                dest = "/tools/organize_net_device.php?device_number=" + num; 
                window.location.href = dest;
            }
            else {
                alert('Sorry, only devices with status of at least Order Submitted can be organized.');
            }
        }
    </SCRIPT>   
<!-- Begin Account Network Details ------------------------------------------------ -->
<?
if(empty($account_number)) {
    DisplayError("Unable to display this account network because you are missing the account_number");
    exit();
}

$customer_number = $db->GetVal("
    SELECT customer_number
    FROM \"xref_customer_number_Account\" 
    WHERE \"ACCT_AccountID\" = (
       SELECT \"ID\" FROM \"ACCT_Account\" WHERE \"AccountNumber\" = $account_number
    );
    ");

//get a device from the account to seed the process of creating the diagram
$seed_device = $db->GetVal("
     SELECT computer_number
     FROM server
     WHERE customer_number = $customer_number
     LIMIT 1;  
    ");
                  
print("<center>");
print("<br /><font size=\"+2\"><b>Display Account Network</b></font>");
print("<br /><a href=\"#\" onclick=\"window.open('/display_account_network_help.html','help_window','width=500,height=600,scrollbars=yes,status=yes,toolbar=no,menubar=no,location=no');\">Help interpreting this diagram</a>");
print("</center>");  

print("<center>");
if($seed_device == "") {
    print("<br />");
    print("<font size=\"+1\">");
    print("Unable to display this account network because no devices exist for this account.");
    print("</font>");
}
else {
    $diagram = new network_diagram($seed_device, $db, false);
    $diagram->printDiagram();
}
print("</center>");
print("<br />");
?>

<script language="JavaScript" type="text/javascript" src="/script/tooltip.js"></script>
<?= page_stop() ?>
</html>
