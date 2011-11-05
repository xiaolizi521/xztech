<?php 
require_once("CORE_app.php");
require_once("act/ActFactory.php");

$computer=new RackComputer;
$computer->Init($customer_number,$computer_number,$db);

if( !$computer->IsComputerGood() ) {
    DisplayError("Unable to load any information about computer number $computer_number This computer may no longer exist.  If you continue to have problems contact the database administrator");
}

if( isset($command) && $command == "EDIT_COMPUTER_INFO") {
    // Here's where we edit the profile.
    $error_message = $computer->EditProfile($info);
    if( empty($error_message) ) {
        ForceReload('DAT_display_computer.php3?computer_number='.$computer_number);
    }
}

$dns = $computer->GetDefaultDNS();

if( count($dns) != 2 ) {
    $dns1 = "UNKNOWN";
    $dns2 = "UNKNOWN";
} else {
    $dns1 = $dns[0];
    $dns2 = $dns[1];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html id="mainbody">
<head>
    <title>
        CORE: Edit Computer Info
    </title>
    <link href="/css/core_ui.css" rel="stylesheet">
<?php

if( !empty($computer_number) ) {
    $action_menu_type = "computer";
    $action_menu_arg  = "computer_number=$computer_number";
}

require_once("tools_body.php");

?>
<form name="server_name_form" action="edit_comp_info.php3" method="post" onsubmit="javascript:return verifyComputerName();">
<table border="1" cellspacing="0" cellpadding="2" align="left">
    <tr>
        <td>
            <input type="hidden" name="command" value="EDIT_COMPUTER_INFO">
            <input type="hidden" name="customer_number" value="<?print($customer_number);?>">
            <input type="hidden" name="computer_number" value="<?print($computer_number);?>">		

    <table border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td bgcolor="#003399" class="HD3REV" colspan="2">
                Edit Computer: #<?print($customer_number);?>-<?print($computer_number);?> 
            </td>
        </tr>	   
<?php
    if (isset($error_message)) {
?>
        <tr>
            <td colspan="2">
                <h3><font color="#FF0000"><?print($error_message);?></font></h3>
            </td>
        </tr>
<?php
    }

    if(in_dept("SALES|AR")) {
?>
        <tr>
            <td bgcolor="#CCCCCC">
                Contract:
            </td>
            <td>
                <select name="info[contract_term]">
<?
                $i=0;
                $contract_termo[$i++]="Monthly";
                $contract_termo[$i++]="3 Month";
                $contract_termo[$i++]="6 Month";
                $contract_termo[$i++]="9 Month";
                $contract_termo[$i++]="12 Month";
                $contract_termo[$i++]="24 Month";
                $contract_termo[$i++]="6 Month Prepay";
                $contract_termo[$i++]="12 Month Prepay";
                $contract_termo[$i++]="24 Month Prepay";
                $contract_termo[$i++]="6 Month Lease-To-Own";
                $contract_termo[$i++]="9 Month Lease-To-Own";
                $contract_termo[$i++]="12 Month Lease-To-Own";
                for ($a=0;$a<$i;$a++)
                {
                    if ($contract_termo[$a]==$computer->getData("contract_term"))
                        print("<OPTION SELECTED >$contract_termo[$a]\n");
                    else
                        print("<OPTION >$contract_termo[$a]\n");
                }
?>
                </select>
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Monthly: 
            </td>
            <td>
                <?= $computer->getCurrencyHTML(); ?>
                <input type="text" size="45" name="info[final_monthly]" value="<?HTprint(GetMoneyAsInt($computer->getData("final_monthly")));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC">
                Set Up Fee: 
            </td>
            <td>
                <?print($computer->getCurrencyHTML());?>
                <input type="text" size="45" name="info[final_setup]" value="<?HTprint(GetMoneyAsInt($computer->getData("final_setup")));?>">
            </td>
        </tr>
<?php
    } else {
?>
        <input type="hidden" name="info[contract_term]" value="<?HTprint($computer->getData("contract_term")); ?>">
        <input type="hidden" name="info[final_monthly]" value="<?HTprint(GetMoneyAsInt($computer->getData("final_monthly")));?>">
        <input type="hidden" name="info[final_setup]"   value="<?HTprint(GetMoneyAsInt($computer->getData("final_setup")));?>">
<?php
    }
?>

<? if( $computer->account->isMerged() ) { ?>
		<tr>
			<td bgcolor="#CCCCCC">
				Site ID:
			</td>
			<td>
				<input type="text" size="10" maxlength="8" name="info[site_id]" value="<?HTprint($computer->getSiteID());?>">
			</td>
		</tr>
<? } ?>

        <tr>
            <td bgcolor="#CCCCCC"> 
                Comments: 
            </td>
            <td>
                <textarea rows="6" cols="45" wrap="virtual" name="info[comments]"><?  HTprint($computer->getData("comments")); ?></textarea>
            </td>
        </tr>
<?php
    if( !$computer->isVirtual() ) {
?>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Server Name (aka Primary Domain or Hostname): 
            </td>
            <td>
                <?= $computer->ServerNameForm(); ?>
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Server Nickname:
            </td>
            <td>
                <input type="text" size="45" name="info[server_nickname]" value="<?HTprint($computer->getData("server_nickname"));?>" maxlength="32">
            </td>
        </tr>
<?php
    }
    if(in_dept("SUPPORT|PRODUCTION|NETWORK")) { ?>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Account Name 
            </td>
            <td>
<?php
                $i_account = ActFactory::getIAccount();
                $account = $i_account->getAccountByAccountNumber($GLOBAL_db, 
                                $customer_number);
		print $account->account_name;
?>
            </td>
        </tr>
<?
        $type=$computer->OS();
        //Now handle not showing this info if it is a RAQ customer
        if ($type=="RAQ/RAQ2"||$type=="RAQ3") {
            $RAQ=true;
        } else {
            $RAQ=false;
        }

        if ($computer->getData("status_number")<7) {

            switch($computer->getOSGroupID()) {
                case 4: #RAQ
?>
        <tr>
            <td bgcolor="#CCCCCC">
                <input type="hidden" name="info[primary_userid]" value="admin" />
                &nsbp;
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC">
                Root/Admin Password:
            </td>
			<td>
                <input type="text" size="45" name="info[root_password]" value="<?HTprint($computer->getData("root_password"));?>" />
            </td>
        </tr>
<?
                    break;
                case 1: #Linux
                case 3: #SUN
                case 5: #BSD
?>
        <tr>
            <td bgcolor="#CCCCCC">
                Primary Userid:
            </td>
            <td>
                <input type="text" size="10" maxlength="8" name="info[primary_userid]" value="<?HTprint($computer->getData("primary_userid"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Primary Userid Password:
            </td>
            <td>
                <input type="text" size=45 name="info[primary_userid_password]" value="<?HTprint($computer->getData("primary_userid_password"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Webmin Password: <i>Should be the same as the one above</i> 
            </td>
            <td>
                <input type="text" size=45 name="info[webmin_password]" value="<?HTprint($computer->getData("webmin_password"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Webmin Port:<i>60000>x>10000</i>  
            </td>
            <td>
                <input type="text" size="10" maxlength="5" name="info[webmin_port]" value="<?HTprint($computer->getData("webmin_port"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Rack Password:  
            </td>
            <td>
                <input type="text" size=45 name="info[rack_password]" value="<?HTprint($computer->getData("rack_password"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC">
                Root/Admin Password:
            </td>
            <td>
                <input type="text" size=45 name="info[root_password]" value="<?HTprint($computer->getData("root_password"));?>">
            </td>
        </tr>
<?
                    break;
                case 2: #Windows
?>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Rack Password:  
            </td>
            <td>
                <input type="text" size=45 name="info[rack_password]" value="<?HTprint($computer->getData("rack_password"));?>">
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Root/Admin Password:  
            </td>
            <td>
                <input type="text" size=45 name="info[root_password]" value="<?HTprint($computer->getData("root_password"));?>">
            </td>
        </tr>
<?
                    break;
        }
    } else {
?>
        <tr>
            <th align="left" colspan="2">
                <A href="edit_account_info.php?computer_number=<?php print($computer_number); ?>"> Edit Password Info</A> 
            </th>
        </tr>
<?php
    }
}

if( !$computer->isVirtual() ) {
    if ($computer->getData("netmask")=="") {
        $netmaskval = '255.255.255.0';
    } else {
        $netmaskval = HTescape($computer->getData("netmask"));
    }
?>
        <tr>
            <td bgcolor="#CCCCCC"> 
                Primary IP :  
            </td>
            <td>
                <?print($computer->getData("primary_ip"));?> 
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC"> 
                NetMask:  
            </td>
            <td>
                <input type="text" size=45 name="info[netmask]" value="<?= $netmaskval; ?>" />
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC">
                Gateway IP: 
            </td>
            <td>
                <input type="text" size=45 name="info[gateway]" value="<?= HTescape($computer->getData("gateway"));?>" />
            </td>
        </tr>
<?php
}

if ( $computer->getData("primary_dns")=="") {
    $primarydnsval = $dns1;
} else {
    $primarydnsval = HTescape($computer->getData("primary_dns"));
}

if ( $computer->getData("secondary_dns")=="" ) {
    $secondarydnsval = $dns2;
} else {
    $secondarydnsval = HTescape($computer->getData("secondary_dns"));
}

?>
        <tr>
            <td bgcolor="#CCCCCC">
                Primary DNS: 
            </td>
            <td>
                <input type="text" size=45 name="info[primary_dns]" value="<?= $primarydnsval; ?>" />
            </td>
        </tr>
        <tr>
            <td bgcolor="#CCCCCC">
                Secondary DNS: 
            </td>
            <td>
                <input type="text" size=45 name="info[secondary_dns]" value="<?= $secondarydnsval; ?>" />
            </td>
        </tr>

?>
        <tr>
            <td bgcolor="#CCCCCC">
                U Location:
            </td>
            <td>
                <input type="text" size="10" maxlength="10" name="info[uspace_location]" value="<?= $computer->getData("uspace_location"); ?>" />
            </td>
        <tr>

<?php

if( $computer->isDell() ) { 

?>
        <tr>
            <td bgcolor="#CCCCCC">
                DELL Service Tag Number:
            </td>
            <td>
                <input type="text" size="10" maxlength="10" name="info[dell_service_tag]" value="<?= $computer->getData("dell_service_tag"); ?>" />
            </td>
        <tr>
<?php 
} 

if( !$computer->isVirtual() ) {

?>
        <tr>
            <td bgcolor="#CCCCCC">
                Datacenter: 
            </td>
            <td>
<?php

    echo "<SELECT name=\"info[datacenter_number]\">\n";
    $centers=$db->SubmitQuery("select 
						datacenter_number, name, datacenter_abbr
						from datacenter 
                        where datacenter_number > 0 and \"Active\" = 't'
						order by datacenter_number;");
    $num=$db->NumRows($centers);
    for ($i=0;$i<$num;$i++) {
        $row = $db->FetchArray($centers, $i);
        if ($computer->getData("datacenter_number") == $row['datacenter_number']) {
            print("<OPTION SELECTED ");
        } else {
            print("<OPTION ");
        }
        printf(" value=\"%s\"> %s\n", $row['datacenter_number'], $row['name'] );
    }
    $db->FreeResult($centers);

    print "</select>\n";

?>
                <p style="color: red">
                    NOTE: Changing the datacenter of a server can break a server, making it impossible to edit parts on the server!
                </p>
            </td>
        </tr>
<?php
} 
?>
        <tr>
            <td colspan="2" align="center">
                <input type="image" src="../images/button_command_save_off.jpg" align="absmiddle" border="0" />
            </td>
        </tr>
    </table>

        </td>
    </tr>
</table>
</form>	
<?php
    EndPage();
    echo page_stop();
?>
</html>
