<?php
require_once("CORE_app.php");
require_once("class.mailer.php");
require_once("class.parser.php");
require_once("ticket.php");
require_once("xmlrpc_core_api.php");

if( empty($ONESTEP) ) {
    $ONESTEP = false;
}

$error = '';
if( empty($server_name_std) )   { $server_name_std = ""; }
if( empty($server_name_nostd) ) { $server_name_nostd = ""; }
if( empty($server_name_svc) )   { $server_name_svc = ""; }
if( empty($server_nickname) )   { $server_nickname = ""; }

if ( empty($usestdname) ) {
    $usestdname = 'yes';
}

if (isset($command)&&$command=="ADD_COMPUTER") {
	$computer_number=create_computer($account_number,"","","","",$datacenter_number);
	ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
}

// Create the new computer object.
$Rcomputer= new RackComputer;
$Rcomputer->Init($account_number,$computer_number,$db);

$read_only = false;
$status_number = $Rcomputer->getData("status_number");
$ro_reason = "";

if( $status_number >=3 and $status_number < 12 ) {
        $read_only = true;
        $ro_reason .= "<p>Once a server is marked <b>Received Contract</b>,
it may not have its parts edited until it is marked <b>Online Complete</b>.
</p>";
}
$iskus = $Rcomputer->getInvalidParts();
if( count($iskus) ) {
    $error .= "<p>The following SKUs do not belong in this Datacenter: <ul>\n";
    foreach( $iskus as $sku => $name ) {
        $error .= "<li>$sku: $name</li>";
    }
    $error .= "</ul>";
    $error .= "These parts will be removed if you edit the configuration.";
}

# Set optional form values to an empty string to avoid unset var warnings.
$field_names = array('server_type', 'product_page', 'first_load',
    'back_to_cart');
foreach($field_names as $name) {
    eval("\$is_empty = empty($$name);");
    if ($is_empty) {
        eval("$$name = '';");
    }
}

$ConfigOpt->setMode(ADMIN);
$ConfigOpt->setDataCenterNumber($Rcomputer->getData("datacenter_number"));
$RackCart=new RackCart($db,$account_number,$ConfigOpt,$Rcomputer);
$Configurator = new RackConfigurator($server_type,$db,$ConfigOpt,$product_page,$first_load,$back_to_cart);

if( isset($command) ) {
    if (empty($pricing_mode)) {
        if ($Rcomputer->getData("status_number") < 3) {
            $pricing_mode=RECALC_NEW;
        } else {
            $pricing_mode=RECALC_OLD;
        }
    }
    $ConfigOpt->setPricingMode($pricing_mode);

    if ($command=="CHANGE_TERM") {
        $product_page="";
        $Configurator = new RackConfigurator($Rcomputer->OS(),$db,$ConfigOpt,"",$first_load,$back_to_cart);
        $Configurator->Init($account_number,$computer_number);
        $Configurator->SetTerm($new_contract_term);
        $Configurator->SetupCart($account_number,$computer_number,$product_name,$server_name);
        ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");

    } else if ($command=="SET_SITE_ID") {

        $site_id = trim($site_id);
        if (isset($site_id)) {
            $Rcomputer->SetSiteID($site_id);
            ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
        }

    } else if ($command=="SET_SERVER_NAME") {
        $reload_url = "network_map.php?account_number=$account_number&computer_number=$computer_number&usestdname=$usestdname";

        if ($error = $Rcomputer->EditProfile($info)) {
            ForceReload($reload_url."&show_warning=".urlencode($error) );
        }
        $Rcomputer->Log('Changed Server Name');

        ForceReload($reload_url);

    } else if ($command=="SET_SERVER_NICKNAME") {

        // Set the nickname if it's passed to us.
        $server_nickname = trim($server_nickname);
        if (isset($server_nickname)) {
            $Rcomputer->SetServerNickname($server_nickname);
            ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
        }

    } else if ($command=="SET_USAGE_TYPE") {
        $query = "
        update server
            set \"COMP_val_ServerTypeID\" = $usage_type
        where
            computer_number = $computer_number
        ";

        $result = $db->SubmitQuery($query);

        ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
    } else if($command=="OVERRIDE_PRICE") {
        if( in_dept("SALES|AR|ACCOUNT_EXECUTIVE") or isTeamLeader() ) {
            $old_monthly = GetMoneyAsInt($Rcomputer->getData("final_monthly"));
            $old_setup   = GetMoneyAsInt($Rcomputer->getData("final_setup"));
            if( $old_monthly != $final_monthly OR $old_setup != $final_setup ) {
                $userid = GetRackSessionUserid();
                $mail =& new core_mailer;
                $mail->SetRackFrom( "" );
                $mail->AddRackAddress( "discount" );
                $mail->Subject = "Price Change for server #$computer_number";
                $mail->Body = "
The computer $account_number-$computer_number was repriced by $userid.
Monthly: \$$old_monthly -> \$$final_monthly
  Setup: \$$old_setup -> \$$final_setup

This was an automated message from CORE";
                $Rcomputer->SetMonthly($final_monthly);
                $Rcomputer->SetSetupFee($final_setup);

                # pseudo: SUBJECT: "change of server price"
                # pseudo:+ FROM: noreply@rackspace.com
                # pseudo:+ TO:  <Discount Watcher> "jcantu@rackspace.com"
                # pseudo:+ BODY: # is inline text in code
                # pseudo:+ in_dept("SALES|AR|ACCOUNT_EXECUTIVE") or isTeamLeader()
                # pseudo:+ on $command=="OVERRIDE_PRICE"
                $mail->Send();
                ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
                exit;
            }
        } else {
            ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
            exit;
        }
    } else if ($command=="CHANGE_OS") {
        if( $Rcomputer->IsUpgradeRestricted() ) {
            trigger_error("You are not allowed to change the OS of an upgrade restricted computer", FATAL);
            exit;
        }
        if ( $Rcomputer->isHyperVisor() ) {
            $Rcomputer->removeHyperVisor();
        }
        if ($Rcomputer->isVirtualMachine()) {
            $Rcomputer->removeVirtualMachine();
        }
        if( $status_number <= 2 and $status_number > -1 ) {
            $db->SubmitQuery( "DELETE FROM server_parts WHERE computer_number = $computer_number" );
        }
        if ( $Rcomputer->customer->hasCustomMonitoring() && $os_type == 'Custom Monitoring') {
            $error = "You are only allowed one custom monitor per account";
        } else {
            # if switching from microsoft to linux.  create a ticket in the professional services queue
            if ( ( in_array($os_type, $GLOBALS['MICROSOFT_OS_TYPES']) &&
                   in_array($Rcomputer->OS(), $GLOBALS['LINUX_OS_TYPES']) ) ||
                      (in_array($Rcomputer->OS(), $GLOBALS['MICROSOFT_OS_TYPES']) &&
                      in_array($os_type, $GLOBALS['LINUX_OS_TYPES'] ) )  ) {
                // as per CORE-6734 - remove this message going to the old NetSec queue.
                // added by Nathen Hinson 11-28-07.
                $queue = QUEUE_INTENSIVE_PROFESSIONAL_SERVICES;
                // changed above line to only be INTENSIVE, removed all managed references.
                //if($Rcomputer->account->segment_id == MANAGED_SEGMENT) {
                //    $queue = QUEUE_MANAGED_PROFESSIONAL_SERVICES;
                //}
                //elseif($Rcomputer->account->segment_id == INTENSIVE_SEGMENT) {
                //    $queue = QUEUE_INTENSIVE_PROFESSIONAL_SERVICES;
                //}
                $subject = 'Computer #'.$Rcomputer->computer_number.' changing OS from '.$Rcomputer->OS().' to '.$os_type;
                $message_text = $subject;
                coreCreateInternalTicket( $queue, TICKET_SEVERITY_MODERATE, 54, $subject,$message_text, 1, array(1),  GetRackSessionContactId());
            }
	        $Configurator = new RackConfigurator($Rcomputer->OS(),$db,$ConfigOpt,$product_page,$first_load,$back_to_cart);
		    $Configurator->Init($account_number,$computer_number);
		    $Configurator->ChangeOS($os_type);
		    ForceReload("network_map.php?account_number=$account_number&computer_number=$computer_number");
            exit();
        }
    }
} else {
    $ConfigOpt->setPricingMode( DO_NOTHING );
}
$RackCart->SetTerm( $db->GetVal(" select contract_term from server where computer_number=$computer_number "));

$tree_url = "$py_app_prefix/account/tree.pt?account_number=$account_number&computer_number=$computer_number&current=edit&";



// Set the menu correctly.
if( !empty($computer_number) ) {
    $action_menu_type = "computer";
    $action_menu_arg  = "computer_number=$computer_number";
}
require_once("tools_body.php");


$server_name       = $Rcomputer->getData("server_name");
$server_name_nostd = $server_name;

$sql = 'SELECT DISTINCT "Name" FROM "COMP_val_ServerService"';

$result = $db->SubmitQuery($sql);

$service_type = array();
for ($row = 0; $row < $result->numRows(); $row++) {
    $service_type[] = $result->getResult($row, 0);
}

$service_regexp = implode('|', $service_type);

// Select the services and max number for each standard named server in this account.
$sql = 'SELECT
    SUBSTRING(server_name from \'[0-9]{1,6}-('.$service_regexp.')([0-9]{1,3})\..*\') as svc,
    MAX(SUBSTRING(server_name from \'[0-9]{1,6}-(?:'.$service_regexp.')([0-9]{1,3})\..*\')::int) as seq
    FROM server
    WHERE
        customer_number='.$account_number.' AND
        server_name ~ \'[0-9]{1,6}-('.$service_regexp.')([0-9]{1,3})\..*\'
    GROUP BY
        svc
    ORDER BY
        svc
';

$result = $db->SubmitQuery($sql);

// Take the existing server_name, rip it into little bitty pieces, and bury the pieces in some well formed html.
$regexp = '/^([0-9]{1,6})-('.$service_regexp.')([0-9]{1,3})\.(.*)$/';
if (preg_match( $regexp, $server_name, $regs)) {
    $server_name_sel = $regs[2];
    $server_name_svc = $regs[2].$regs[3];
    $server_name_std = $regs[4];
} else {
    $server_name_sel = '';
    $server_name_svc = '';

    $usestdname = 'no';
    $server_name_std = $server_name;
}

// Make an array of the current counts
$service_count = array();
for ($row = 0; $row < $result->numRows(); $row++) {
    $service_count[$result->getResult($row, 0)] = $result->getResult($row, 1);
}

// Build the array of options for the service select box.
$server_name_option =array();
if ($server_name_svc) {
    $server_name_option[] = $server_name_svc;
}

foreach( $service_type as $service ) {
    if ($service == $server_name_sel) {
        continue;
    } else if (isset($service_count[$service])) {
        $svc = $service.($service_count[$service]+1);
    } else {
        $svc = $service.'1';
    }
    $server_name_option[] = $svc;
}

// End of the standard servername code.

?>
<html id="mainbody">
<head>
    <title>Edit Computer Parts</title>
    <script LANGUAGE="JavaScript1.2" TYPE="text/javascript">
        // Refreshes the tree
        try {
            top.frames["workspace"].frames["left"].document.location.href = "<?=$tree_url?>" + top.frames["workspace"].frames["left"].cleanargs;
        } catch(e) {
            // Do Nothing
        }
    </script>
    <style>
        #computerTable { border: 1px solid #777; }
        #computerTable td.error { color: red; border: 1px solid red; font-size: 16px; text-align: center; }
        table * { font-size: 12px; }
    </style>
</head>
<body>

<table id="computerTable" style="width: 600px;" >
<form action="/py/computer/create.pt" method="POST">
<input type="hidden" name="account_number" value="<?print($account_number);?>">

<?php
if (isset($show_warning)) {
    echo '<tr><td colspan="3" class="error">'.stripslashes($show_warning).'</td></tr>';
}
?>

<tr>
    <td>
        <b>ADD A COMPUTER</b>
    </td>
    <td align="center">
        <select name="datacenter_number">
<?
	//Load up the customer profile and status
	$current_datacenter=$Rcomputer->getData("datacenter_number");
	$datacenter_info=$db->SubmitQuery("SELECT datacenter_number,name from datacenter where datacenter_number > 0  order by datacenter_number asc;");
	$num=$db->NumRows($datacenter_info);
	for ($i=0;$i<$num;$i++)
	{
		if ($datacenter_info->getResult($i,"datacenter_number")==$current_datacenter)
			$selected=" SELECTED ";
		else
			$selected="";
		print ("<OPTION VALUE=\"".$datacenter_info->getResult($i,"datacenter_number")."\" $selected>".$datacenter_info->getResult($i,"name")."\n");

	}
	$db->FreeResult($datacenter_info);
?>
        </select>
    </td>
    <td align="right">
        <input type="image" src="../assets/images/arrow.jpg" width=25 height=25 border=0 ALT="->" align="absmiddle">
    </td>
</tr>
</form>

<TR><TH COLSPAN=3 ALIGN=CENTER>Primary Contact</TH></TR>
<TR>
  <TH ALIGN=LEFT>
    Name:
  </TH>
  <TD colspan="2">
    <?
        $primaryContact = $Rcomputer->account->getPrimaryContact();
        print($primaryContact->individual->getFullName());
    ?>
  </TD>
</TR>
<TR>
  <TH ALIGN=LEFT>
    Customer<BR>Email:
  </TH>
  <TD colspan="2">
    <?
        $primaryEmail = $primaryContact->individual->getPrimaryEmailAddress();
        print($primaryEmail);
    ?></TD></TR>
<?
if ($primaryEmail == ""
        || !ereg("@",$primaryEmail)) {
    print ("<TR><TH ALIGN=CENTER COLSPAN=2>
        <h1><BLINK><FONT COLOR=#FF0000>MISSING CUSTOMER EMAIL
        </FONT></BLINK></H1></TH></TR>\n");
}
?>

<tr>
    <td bgcolor="#FFFFFF">
        Customer Number
    </td>
    <td colspan="2">
        <?= $account_number; ?>
    </td>
</tr>

<? if( $Rcomputer->account->isMerged() ) { ?>
<form name="site_id_form" action="network_map.php" action="post">
    <input type="hidden" name="command"         value="SET_SITE_ID" />
    <input type="hidden" name="account_number"  value="<?print($account_number);?>" />
    <input type="hidden" name="computer_number" value="<?print($computer_number);?>" />
    <tr>
        <td> Site ID </td>
        <td>
            <INPUT TYPE="text" name="site_id" value="<?= $Rcomputer->GetSiteID() ?>" size="10" maxlength="9" /> 
        </td>
        <td valign="top" align="right">
            <input name="submitSiteIDChange" type="submit" value="Change" />
        </td>
    </tr>
</form>
<? } ?>

<form name="server_name_form" action="network_map.php" action="post">
    <input type="hidden" name="command"         value="SET_SERVER_NAME" />
    <input type="hidden" name="account_number"  value="<?print($account_number);?>" />
    <input type="hidden" name="computer_number" value="<?print($computer_number);?>" />
    <tr>
        <td> Server Name </td>
        <td>
            <?=$Rcomputer->ServerNameForm(); ?>
        </td>
        <td valign="top" align="right">
            <input name="submitStandard" type="submit" value="Change" onclick="javascript:return verifyComputerName();"/>
        </td>
    </tr>
</form>

<form action="network_map.php" action="post">
    <input type="hidden" name="command"         value="SET_SERVER_NICKNAME" />
    <input type="hidden" name="account_number"  value="<?print($account_number);?>" />
    <input type="hidden" name="computer_number" value="<?print($computer_number);?>" />

    <tr>
        <td>
            Nickname
        </td>
        <td>
            <INPUT TYPE="text" name="server_nickname" value="<?= $Rcomputer->getData("server_nickname") ?>" size="50" maxlength="32" />
        </td>
        <td align="right">
            <input type="submit" value="Change" />
        </td>
    </tr>
</form>

<form action="network_map.php" action="post">
    <input type=hidden name="command"         value="SET_USAGE_TYPE" />
    <input type=hidden name="account_number"  value="<?print($account_number);?>" />
    <input type=hidden name="computer_number" value="<?print($computer_number);?>" />
<?
    $query = ' select "COMP_val_ServerTypeID", "Name" from "COMP_val_ServerType" order by "Name" ';
    $result = $db->SubmitQuery($query);

    $usage_types = array();

    for ($row = 0; $row < $result->numRows(); $row++) {
        $ut_id = $result->getResult($row, 0);
        $ut_name = $result->getResult($row, 1);
        array_push($usage_types, array($ut_id, $ut_name));
    }

    $current_usage_type = $db->GetVal(" select \"COMP_val_ServerTypeID\" from server where computer_number=$computer_number");
?>
<tr>
    <td>
        Server Usage
    </td>
    <td>
        <select name="usage_type">
<?
            foreach ($usage_types as $ut) {
                if ($current_usage_type == $ut[0]) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value=\"{$ut[0]}\" $selected>{$ut[1]}</option>\n";
            }
?>
        </select>
    </td>
    <td align="right">
        <input type=submit value="Change" />
    </td>
</tr>
</form>

<?php
if( !empty($error) ) {
        echo "<tr><td colspan=3>";
        echo '<font color="#902000">';
        echo "<b> $error</b>";
        echo '</font>';
        echo "</td></tr>\n";
}
if( !empty($read_only) or !empty($core_read_only) ) {
        echo "<tr><td colspan=3>";
        echo '<font color="#902000">';
        echo "This server is currently <b>read only</b>.";
        echo $ro_reason;
        echo '</font>';
        echo "</td></tr>\n";
}
?>
</table>

<br /><br />

<table class="blueman" style="width: 600px">
    <tr>
        <th class=blueman>
            Last Comment
        </th>
    </tr>
    <tr>
        <td>
<?
            print_last_entry($conn, "computer_log", "comments", "customer_number=$customer_number" . " and computer_number=$computer_number");
?>
        </td>
    </tr>
</table>

<?
		$computer=new ConfigComputer($account_number,$computer_number,$db,$ConfigOpt);
?>

<br /><br />

<?
	include("include/server_detail.phinc");
?>

<br /><br clear="all" />
<?= page_stop() ?>
</body>
</html>
<?php
// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
