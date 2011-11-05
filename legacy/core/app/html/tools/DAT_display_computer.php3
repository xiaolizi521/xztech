<?
require_once("CORE_app.php");
require_once("privatenet.php");
require_once("menus.php");
require_once("act/ActFactory.php");
require_once("computerStatus.php");

checkDataOrExit( array( "computer_number" => "Computer Number" ) );

# attempt to fix dns-tool session manager problem
session_unregister("customer_number");
session_unregister("computer_number");
session_unregister("zone");

// Guess what it's for...
session_register("SESSION_last_visited_computer_number");
$SESSION_last_visited_computer_number = $computer_number;

// if this user is in the monitoring group, redirect them to the emergency instructions page
if( !in_dept("CORE") && in_dept("MONITORING") && ( !isset( $emergency_visit ) ) ) {
    //header( 'Location: http://core.jaywhitebox/py/computer/displayEmergencyInstructions.pt?computer_number=' . $computer_number );
    header( 'Location: /py/computer/displayEmergencyInstructions.pt?computer_number=' . $computer_number );
    exit();
}

$computer=new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
if( !$computer->IsComputerGood() ) {
    ?>
    <HTML id="mainbody">
    <HEAD>
    <TITLE>
        CORE: Account Summary
    </TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <? print menu_headers() ?>
    </HEAD>
    <?= page_start() ?>
    <p style="text-align: center; margin-top: 30%;">
    Could not find
        <font color=red>computer #<?= $computer_number ?></font>
    </p>
    <?= page_stop() ?>
    <?
    exit();
}
else {
    $customer_number = $computer->customer_number;
}

/*
$hide_network = false;

switch ($computer->OS()) {
case "Noteworthy":
   $hide_network = true;
   break;
default:
   $hide_network = false;
   break;
}
*/

if (isset($_GET["sla_promise_level"])) {
    $db->submitQuery("delete from \"SRVR_SLAPromiseLevel\" where computer_number = $computer_number;");
    $db->submitQuery("insert into \"SRVR_SLAPromiseLevel\" values ($computer_number, " . $_GET["sla_promise_level"] . ");");
}

if (isset($command) && $command == "updateduedate" && in_dept("PRODUCTION_SUPERVISOR")) {
        if (strcmp(trim($datebox), "") == 0 || strcasecmp(trim($datebox), "n/a") == 0) {
                $db->submitQuery("UPDATE server SET due_date = NULL WHERE computer_number = $computer_number");                              
                $computer->$due_date = '';
        } elseif (count($datevals = explode("/", $datebox)) == 3) {
                $month = intval($datevals[0]);
                $day = intval($datevals[1]);
                $year = intval($datevals[2]);
                if (   $year  >= 1500 || $year  <= 3000
                    || $month >= 1    || $month <= 12
                    || $day   >= 1    || $day   <= 31  ) { // not exactly /full/ date validation here.

                        $prev_hours = 0;
                        $prev_minutes = 0;
                        $prev_seconds = 0;

                        $previous_due_date = $computer->getData("due_date");

                        if (is_numeric($previous_due_date) && ($previous_due_date > 0)){

                          $prev_due_date_array = getdate($previous_due_date);

                          $prev_hours = $prev_due_date_array['hours'];
                          $prev_minutes = $prev_due_date_array['minutes'];
                          $prev_seconds = $prev_due_date_array['seconds'];

                        }                       

                        $due_date = mktime($prev_hours,
                                           $prev_minutes,
                                           $prev_seconds,
                                           $month, $day, $year);
                        if ($due_date !== false) {
                                $db->submitQuery("UPDATE server SET due_date = TIMESTAMPTZ(abstime($due_date)) WHERE computer_number = $computer_number");
                                $computer->setData("due_date", $due_date);
                                // reset computer date after db update
                        }
                }
        }
}

if (isset($command) && $command == "updateduetosupportdate" && in_dept("PRODUCTION_SUPERVISOR")) {
	if (strcmp(trim($datebox), "") == 0 || strcasecmp(trim($datebox), "n/a") == 0) {
		$db->submitQuery("UPDATE server SET due_to_support_date = NULL WHERE computer_number = $computer_number");
		$computer->$due_date = '';
	} elseif (count($datevals = explode("/", $datebox)) == 3) {
		$month = intval($datevals[0]);
		$day = intval($datevals[1]);
		$year = intval($datevals[2]);
		if (   $year  >= 1500 || $year  <= 3000
		    || $month >= 1    || $month <= 12
		    || $day   >= 1    || $day   <= 31  ) { // not exactly /full/ date validation here.

		        $prev_hours = 0;
			$prev_minutes = 0;
			$prev_seconds = 0;

			$previous_due_date = $computer->getData("due_to_support_date");

			if (is_numeric($previous_due_date) && ($previous_due_date > 0)){

			  $prev_due_date_array = getdate($previous_due_date);

			  $prev_hours = $prev_due_date_array['hours'];
			  $prev_minutes = $prev_due_date_array['minutes'];
			  $prev_seconds = $prev_due_date_array['seconds'];

			}

			$due_to_support_date = mktime($prev_hours, 
					   $prev_minutes, 
					   $prev_seconds, 
					   $month, $day, $year);
			if ($due_to_support_date !== false) {
                            $due_date = $computer->getData("due_date");
                            if ($due_to_support_date > $due_date) {
                                $due_to_support_date_error = "Due to support date must be prior to the due date.";
                            } else {
			        $db->submitQuery("UPDATE server SET due_to_support_date = TIMESTAMPTZ(abstime($due_to_support_date)) WHERE computer_number = $computer_number");
			        $computer->setData("due_to_support_date", $due_to_support_date);
				// reset computer date after db update
                            }
			}
		}
	}
}

// Make sure no old systems are hanging around.
$current_status = $computer->getData("status_rank");
$computer->setData("status_number",$current_status);

$sales_rep=$computer->GetRepName();

$sec_due_offline = $computer->getSecDueOffline();
if( !empty( $sec_due_offline ) ) {
    $sec_due_offline = strftime( "%m/%d/%Y", $sec_due_offline );
}
$tree_url = "$py_app_prefix/account/tree.pt?" .
"account_number=$account_number&".
"computer_number=$computer_number&current=view&";

$support_sku = $db->getVal("
select product_sku
from server_parts
where product_sku in (101861,101862,101863)
  and computer_number = $computer_number;
");

$usage_type =  $db->getVal("
select st.\"Name\"
from \"COMP_val_ServerType\" st, server s
where s.\"COMP_val_ServerTypeID\" = st.\"COMP_val_ServerTypeID\"
and s.computer_number = $computer_number ;
");

// sql for build error
$has_build_error = $db->getVal("SELECT 1 FROM \"COMP_BuildError\" WHERE computer_number = $computer_number");
$build_error_free_query = $db->getVal("SELECT error_free FROM server WHERE computer_number = $computer_number");

if(is_null($build_error_free_query)) {
    $build_error_free = false;
} else {
    $build_error_free = ($build_error_free_query == "t") ? True : False;
}

$expedite=$computer->getData('expedite');


if( $support_sku == 101863 ) {
    $support_blurb = '<img src="/img/minicon/service_sys_man.gif"/> Fanatical System Management';
} elseif( $support_sku == 101862 ) {
    $support_blurb = '<img src="/img/minicon/service_plus.gif"/> Fanatical Support Plus';
} else {
    $support_blurb = "Fanatical Support";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: <? print("Display Computer $customer_number-$computer_number");?>
    </TITLE>
    <script language="JavaScript" src="/script/date-picker.js"></script>
    <script language="JavaScript" src="/script/vm_functions.js"></script>
    <script language="JavaScript" src="/script/dnas_functions.js"></script>
    <LINK href="/ui/v2/rui/resources/css/rack-all.css" type="text/css" rel="stylesheet">
    <script language="JavaScript1.2"
        type="text/javascript">
    try {
        // Refreshes the tree
        top.frames["left"].document.location.href =
        "<?=$tree_url?>" +
        top.frames["left"].cleanargs;
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
<!-- Begin Computer Details ------------------------------------------------ -->
	<TABLE BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
	       ALIGN="left">
	<TR>
		<TD>
			<TABLE class=blueman>
            <tr>
                <th class=blueman>Computer Details:
				#<?print($customer_number);?>-<?print($computer_number);?>:
				<?php
                    print $computer->account->account_name;
                    print getImageForServer($computer_number);
				?>
				[<?=$computer->account->segment_name ?>]
                <?php
                if( !empty($rack_test_system) ) {
                    echo ' <span style="font-weight: bold; color: red">NOT LIVE</span>';
                }
                ?>
                </th>
            </tr>
<?php
    if ( $computer->account->IsHighProfile() ) {
        echo '<tr><td><table style="background: #ffa800; border: 1px solid #666666" width="100%"><tr><td><b style="color: #ff0000">HIGH PROFILE CUSTOMER</b></td></tr></table></td></tr>';
    }
?>
<!-- Begin Computer Status ------------------------------------------------- -->
			<TR>
				<TD>
							<TABLE class=datatable>
							<TR>
								<th width="20%"> Computer Status: </th>
								<TD>
								<FONT COLOR=#FF0000> <?print($computer->getData("status"));?> </FONT>
								<?
    if( $current_status == STATUS_MIGRATION_SERVER ) {
        $migr_date_str = "New server not yet online.";
        $migr_new_server = $computer->getMigrationNewServer();
        $migr_days = $computer->getMigrationDays();
        if ($migr_new_server) {
            $new_computer=new RackComputer;
            $new_computer->Init($customer_number,$migr_new_server,$db);
            $migr_online = $new_computer->getData("sec_finished_order");
            if( $migr_online>0 ) {
                $migr_seconds = $migr_days * 24 * 60 * 60;
                
                $migr_date_str = strftime("%b-%d-%Y", $migr_online + $migr_seconds );
            }
        } else {
            $migr_date_str = "No migration server listed.";
        }

        echo "Free Migration Ends: $migr_date_str";
    }
                                ?>
<BR>
                                <?
                                    $psoft_actor = $computer->customer->sentToPsoft();
                                    if ($psoft_actor):?>
								<FONT SIZE=+1 COLOR=#FF0000>
								Customer goes directly to PSOFT for H-Sphere Support (<?= $psoft_actor ?>)<BR>
								<?endif;?>
								<?if ($computer->account->isDelinquent()):?>
								<FONT SIZE=+1 COLOR=#FF0000>
								Pending A/R - DO NOT APPROVE UPGRADES <BR>
								<?endif;?>
								<? if($has_build_error): ?>
								<FONT COLOR=#FF0000>BUILD ERROR DOCUMENTED</FONT><br>
								<? endif; ?>
								<?if ($computer->account->isClosed()):?>
                                <strong>
							     Account is Closed. <BR>
                                </strong>
								<?endif;?>
								<?if ($db->TestExist("select customer_number from reserve_customer where customer_number=$customer_number;")):?>
								<FONT SIZE=+1 COLOR=#FF0000>
								RESERVE CUSTOMER PROFILE <BR>
								<?endif;?>
								<?if ($computer->requiresAudit()&&in_dept("SUPPORT")):?>
									<BLINK><FONT COLOR=#FF0000> AUDIT THIS COMPUTER </FONT></BLINK> <BR>
								<?endif;?>
								<?
                             if( $sec_due_offline and
                                 $current_status > -1 ) {
                                 echo "<font color=\"red\"> Scheduled for cancellation: $sec_due_offline</font>";
                                 echo "<br>\n";
                             }
								?>
								<?if ($db->TestExist("select customer_number from reserve_customer where customer_number=$customer_number;")):?>
									<FONT COLOR=#FF0000> RESERVE CUSTOMER PROFILE </FONT><BR>
								<?endif;?>
								<?if ($computer->IsUpgradeRestricted()):?>
									<FONT COLOR=#FF0000> UPGRADE RESTRICTED COMPUTER </FONT><BR>
								<?endif;?>
								<?
									//If this is a colo box - we do not bill until the server goes online
									//This provides a visual reminder :)
									if( $computer->OS() == "Colocation" and
                                        $current_status < 12 ) {
										print("COLO - Do Not Bill Until Online/Complete</B><BR>");
									}
								?>
                                <?
                                    if ($expedite == 't')
                                        print ("<FONT COLOR=#FF0000> *EXPEDITE*</FONT>");
                                ?>
                                <? if($build_error_free): ?>
									<span style="color: black">Build Error Free</span><br>
								<? endif; ?>
								</TD>
							</TR>
							</TABLE>
				</TD>
			</TR>
<!-- End Computer Status --------------------------------------------------- -->
<!-- Begin Internal Contacts -------------------------------------------------->
			<TR>
				<th class=blueman> Internal Contacts </th>
			</TR>
			<TR>
				<TD>
							<TABLE class=datatable>
							<TR>
								<th width="10%"> Support: </th>
								<? print("<TD>".$computer->account->getSupportTeamName()."</TD>"); ?>
								<th width="10%"> Sales Rep: </th>
								<TD>
								<?php
if( empty($sales_rep) ) {
    echo "<BLINK><Font color=\"red\">Missing Sales Rep</FONT><BLINK>";
} else {
    echo $sales_rep;
}
								?> </TD>
								<th width="10%"> Account Executive: </th>
								<TD>
								<?
                    $account_executive_contact = $computer->account->getAccountExecutive();
                    if(!empty($account_executive_contact)) {
                        print($account_executive_contact->getFullName());
                    }
                ?>
                </TD>
							</TR>
							</TABLE>
<!-- End Internal Contacts ---------------------------------------------------->
				</TD>
			</TR>
			<TR>

                                <th class=blueman COLSPAN=8> Sales/Marketing Info </th>
                         </TR>
                         <TR>
                                 <TD>
 <!-- Begin Sales/Marketing ------------------------------------------------  -->
                                                         <TABLE class=datatable>
                                                         <TR>
                                                                 <?if ($computer->getData("sec_finished_order")>0) {
                                     $print_online_status = strftime("%m/%d/%Y %r",$computer->getData("sec_finished_order"));
                                 } else {
                                     $print_online_status = "N/A";
                                 }
                                 ?>
                                                                 <th> Online: </th>
                                                                 <TD> <?=$print_online_status?></TD>
                                                                 <?if ($computer->getData("sec_placed_order")>0){
                                     $print_order_submitted = strftime("%m/%d/%Y %r",$computer->getData("sec_placed_order"));
                                 } else {
                                     $print_order_submitted = "N/A";
                                 }?>
                                                                 <th width="10%"> Order Submitted: </th>
                                                                 <TD><?=$print_order_submitted?></TD>
                                                         </TR>
                                                         <TR>
                                                                 <?
                                                                 // This needs to be removed ~10 days
                                 // after the code goes live
                                 $due_date = $computer->getData('due_date');
                                 if ( $due_date > 0) {
                                     $print_due_date = strftime("%m/%d/%Y", $due_date);
                                                                 } else {
                                     $print_due_date = "N/A";
                                 }?>
                                 <th>Due Date: </th>
                                 <? if (in_dept("PRODUCTION_SUPERVISOR")) { ?>
                                    <TD>
                                        <form name="calform" action="DAT_display_computer.php3">
                                        <input type="hidden" name="command" value="updateduedate" />
                                        <input type="hidden" name="computer_number" value="<?=$computer_number?>" />
                                        <input type="text" name="datebox" size="10" value="<?=$print_due_date?>" />
                                        <a href="javascript:show_calendar('calform.datebox');" onmouseover="window.status='Date Picker';return true;" onmouseout="window.status='';return true;">
                                            <img style="vertical-align: bottom; margin-left: 2ex;" src="/images/show-calendar.gif" width="24" height="22" border="0" /></a>
                                        <a class="text_button" style="font-size: 12px;" href="javascript:calform.submit();">Apply</a>
                                        </form>
                                    </TD>
                                 <? } else { ?>
                                                                 <TD><?=$print_due_date?></TD>
                                 <? } ?>
                                                                 <?if ($computer->getData("sec_contract_received")>0) {
                                     $print_contract_received = strftime("%m/%d/%Y %r",$computer->getData("sec_contract_received"));
                                 } else {
                                     $print_contract_received = "N/A";
                                 }?>
                                                                 <th> Contract Received: </th>
                                                                 <TD><?=$print_contract_received?></TD>

								<?
    if( $current_status == STATUS_MIGRATION_SERVER ) {
        $migr_days = $computer->getMigrationDays();
        $href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/popupComputerMigrationDays.pt?computer_number=$computer_number',230,380,'',3,'MigrationDays')";
        $href = "<a href=\"$href\" class=\"text_button\">Edit</a>";
                                ?>
                                                                <th> Free Migration Days: </th>
                                                                <td><?= $migr_days ?>
                                                                    <?= $href?>
                                                                </td>
                                <? } ?>
                                                         </TR>
                             <tr>
                                <?
                                 $due_to_support_date = $computer->getData('due_to_support_date');
                                 if ( $due_to_support_date > 0) {
                                     $print_due_to_support_date = strftime("%m/%d/%Y", $due_to_support_date);
                                 } else {
                                     $print_due_to_support_date = "N/A";
                                 }
                                ?>
                                 <th>Due To Support Date: </th>
                                 <? if (in_dept("PRODUCTION_SUPERVISOR")) { ?>
                                    <TD>
                                        <form name="caltosupportform" action="DAT_display_computer.php3">
                                        <input type="hidden" name="command" value="updateduetosupportdate" />
                                        <input type="hidden" name="computer_number" value="<?=$computer_number?>" />
                                        <input type="text" name="datebox" size="10" value="<?=$print_due_to_support_date?>" />
                                        <a href="javascript:show_calendar('caltosupportform.datebox');" onmouseover="window.status='Date Picker';return true;" onmouseout="window.status='';return true;">
                                            <img style="vertical-align: bottom; margin-left: 2ex;" src="/images/show-calendar.gif" width="24" height="22" border="0" /></a>
                                        <a class="text_button" style="font-size: 12px;" href="javascript:caltosupportform.submit();">Apply</a>
                                        </form>
                                    <? if (isset($due_to_support_date_error)) {?>
                                        <br/><span style="color: red"><?=$due_to_support_date_error?></span>
                                    <? } ?>
                                    </TD>
                                 <? } else { ?>
                                                                 <TD><?=$print_due_to_support_date?></TD>
                                 <? } ?>
                                                                 <th> Contract Term: </th>
                                                                 <TD><?print($computer->getData("contract_term"));?></TD>
                                                                 <th> Server Usage: </th>
                                                                 <TD><?print($usage_type);?></TD>
                             </tr>
                             <tr>       
                                 <th>Config Built By:</th>
                                 <td colspan=5>
                                    <?=$computer->GetConfigBuiltByName() ?>
                                </td>   
                             </tr>
                             <tr>
                                 <th>Billing Notes:</th>
                                 <td>
                                 <?
                                 $billing_notes = $computer->getData("billing_notes");
                                 if ( empty($billing_notes) ) {
                                     $button_name = "Set"; }
                                 else $button_name = "Edit";
                                 $href =
                                 "javascript:makePopUpNamedWin('$py_app_prefix/computer/popupComputerBillingNotes.esp?computer_number=$computer_number',230,380,'',3,'BillingNotes')";
                                 $href = "<a href=\"$href\" class=\"text_button\">$button_name</a>";
                                 print $href . "&nbsp;" . htmlentities( $billing_notes );
                                 ?>
                                 </td>
                            </tr>
                            <tr>
                                 <th>Contract:</th>
                                 <td>
                                 <?
                                 $contract_id = $computer->getContractID();
                                 $href = "/py/account/viewContractTabs.pt?account_number=$account_number&contract_id=$contract_id";
                                 $href = "<a href=\"$href\" >$contract_id</a>";
                                 print $href;
                                 ?>
                                 </td>
                            </tr>
                                                         </TABLE>
 <!-- End Sales/Marketing --------------------------------------------------- -->
                                 </TD>
                         </TR>
                         <TR>


				<TD>
<!-- Begin Technical Info -------------------------------------------------- -->
<?php
$hide_network = false;

switch ($computer->OS()) {
case "Noteworthy":
   $hide_network = true;
   break;
default:
   $hide_network = false;
   break;
}
?>

							<TABLE class=datatable>
							<TR>
								<TD BGCOLOR="#003399" COLSPAN=6 class="shdrev"> Technical Info </TD>
							</TR>
<?php
$aei = $computer->account->emergency_instructions;
if( !empty($aei) ):
$aei = htmlentities( $aei );
?>
							<TR>
								<TD colspan="4">
                                <table class="emergency">
                                <tr>
                                <th> Account Management Guidelines </th>
                                </tr>
                                <tr>
                                <td><pre class="emergency"><?=$aei?></pre></td>
                                </tr>
                                </table>
                                </TD>
							</TR>
<?php
endif;

$ei = $computer->getData("emergency_instructions");
if( !empty($ei) ):
$ei = htmlentities( $ei );
?>
							<TR>
								<TD colspan="4">
                                <table class="emergency">
                                <tr>
                                <th> Device Management Guidelines </th>
                                </tr>
                                <tr>
                                <td><pre class="emergency"><?=$ei?></pre></td>
                                </tr>
                                </table>
                                </TD>
							</TR>
<?php
endif;

if (!$hide_network) {
?>
							<TR>
								<th width="10%"> Support Level: </th>
								<TD><?=$support_blurb?></TD>
								<th> Build Tech(s): </th>
								<TD COLSPAN=3><?
									$build_techs=$db->SubmitQuery("select userid from build_tech where computer_number=$computer_number;");
									$num=$db->NumRows($build_techs);
									print("<TABLE>\n");
									for ($i=0;$i<$num;$i++)
									{
										print("<TR><TD>".$db->GetResult($build_techs,$i,0)."</TD></TR>\n");
									}
									print("</TABLE>\n");
									$db->FreeResult($build_techs);
								?>
								</TD>
							</TR>
<?php } ?>
							<TR>
                            <th> Platform: </th>
								<TD><FONT COLOR="#FF0000">
                                <?=strIMG( getIconForOS($computer->OS()) )?>
                                           <?=$computer->OS()?></FONT>
                                <?php

                                if( $computer->isDell() ) {
                                    print '<span style="font-size: 12px;">';
                                    print "<br>Service Tag: ";
                                    print $computer->getData('dell_service_tag');
                                    print "</span>\n";
                                }
                                if( $computer->isPrevenTierRequired() ) {
                                    print ' &nbsp; ';
                                    print '<span style="color: black; background: #CCF">';
                                    print "PrevenTier is Required\n";
                                    print "</span>\n";
                                }

                                ?>
                                </TD>
<?php if ($hide_network) { ?>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
<?php } else { ?>
								<th> Data Center: </th>
								<TD><?print($computer->GetDataCenter());?></TD>
<?php } ?>
							</TR>
							<TR>
								<th>
                                    Server Name: <br />
                                    Nickname:
                                </th>
								<TD>
								<?
                                if ($computer->getData("server_name") == "") {
                                    if( $computer->isNetworked() or
                                        !$computer->hasOS() or
                                        $computer->isCluster()
                                        ) {
                                        print ("<BLINK><FONT COLOR=#FF0000>MISSING SERVER NAME</FONT></BLINK>\n");
                                    } else {
                                        echo "Not Needed";
                                    }
                                } else {
                                    $server_name = $computer->getData("server_name");
                                    echo "<a href=\"http://$server_name\" ";
                                    echo "target=\"_blank\">$server_name</a>&nbsp;<br />\n";
                                    echo $computer->getData("server_nickname");
								}
                                ?>
								&nbsp;<br />
                                </TD>

                                <?if( $computer->isNetworked() and !$computer->isPix501() or !$computer->hasOS() ) { 
				    $is_it_a_virtual_machine = $computer->isVirtualMachine();
				    if ($is_it_a_virtual_machine){
				      ?><th> Switch[Port]s <br />of Hypervisor: </th><TD> <?
				    }else{
                                      ?><th> Switch[Port]s: </th><TD> <?
				    }
		       
 				    $locations = array();
                                    if ($is_it_a_virtual_machine) {
				      $locations = $computer->getHypervisorLocations();
				    }else{
				      $locations = $computer->getLocations();
				    }
				    
                                    foreach($locations as $location) { ?>

                                            <TT><?=$location?></TT>
                                            <BR />
                                <?  }  ?>
                                        </TD>

                                <?} else { ?>

                                    <th> Location: </th>
                                    <TD>   Not Needed</TD>

                                <?} ?>

							</TR>
							<TR>
<?php if (!$hide_network) { ?>
								<th> Primary IP: </th>
								<TD><FONT color=green>
								<?
                                $primary_ip = $computer->getData("primary_ip");
                                $is_primary_ip_requested = $computer->ip->isRequested();
								?>
                                    <A href="telnet://<?print $primary_ip;?>"><?print($computer->getData("primary_ip"));?></A>
                                <?
                                if ($computer->ip->isRequested()) {
                                    print ' IP Requested';
                                }
                                if ($computer->ip->isRefreshing()) {
                                    print ' (refreshing)';
                                }
                                ?>

									<A target="_new" href="<? print( $computer->getPingUrl() ); ?>" class="text_button" style="font-size: 12px;">PING</A>
<?php
    if( !empty($primary_ip) ) {
        print "<a target=\"_new\" href=\"".$GLOBALS['ipspace_base']."info/server?device_number=".$computer_number."&account_number=".$account_number."\" class=\"text_button\" style=\"font-size: 12px;\">IP Information</a>";
        print "<a target=\"_new\" href=\"".$GLOBALS['ipspace_base']."ip_blocks/addresses?ip=".$primary_ip."\" class=\"text_button\" style=\"font-size: 12px;\">Display Block</a>";
    } else {
        if (in_dept("PROFESSIONAL_SERVICES|PERM_IP_ADMIN|PRODUCTION_SUPERVISOR|NETWORK") ) {
            print "<a target=\"_new\" href=\"".$GLOBALS['ipspace_base']."\" class=\"text_button\" style=\"font-size: 12px;\">Assign Primary IP</a>";
        }
        if (in_dept("PROFESSIONAL_SERVICES|PERM_IP_ADMIN|PRODUCTION_SUPERVISOR|NETWORK|PRODUCTION") ) {
            print "<a target=\"content\" href=\"/py/computer/assignPrimaryIP.pt?computer_number=$computer_number\" class=\"text_button\" style=\"font-size: 12px;\">Request Auto Assigned IP</a>";
        }
    }
?>
                                </TD>
<?php }  ?>
<? // Show View Password Button ?>
                                <th> U Location: </th>
                                <td><?print($computer->getData("uspace_location"));?></td>
                                </tr>
                                <tr >

<?php if (!$hide_network) { ?>

							<TR>
								<th> Gateway IP: </th>
								<?
                                // Netmask is calculated from the gateway
								$netmask = $computer->getData("netmask");
								$gateway = $computer->getData("gateway");
                                if( !empty($gateway) ) {
                                    $gateway_status = "(Manually Entered)";
                                } else {
                                    $gateway_status = "";
                                }

                                $netmask = $computer->ip->netmask;
                                $gateway = $computer->ip->gateway;

								?>
								<TD><?=$gateway?> <?=$gateway_status?></TD>
								<th> Passwords: </th>
                                <TD>
                                    <A
    href="javascript:makePopUpNamedWin('<?="$py_app_prefix/computer/summary.pt?computer_number=$computer_number&show_secrets=1"?>',550,500,'',3,'ComputerSummary<?=$computer_number?>')"
    class="text_button">
        View
</a>
&nbsp;
                                    <A
    href="javascript:makePopUpNamedWin('<?="/tools/reset_passwords.php?computer_number=$computer_number&account_number=$account_number"?>',200,600,'',3,'ResetComputerPasswords<?=$computer_number?>')"
    class="text_button">
        Reset
</a>
&nbsp;

<a href="edit_account_info.php?computer_number=<?=$computer_number?>" class="text_button">Edit</a>
                                </TD>
<? // End view password button ?>
								
							</TR>
							<TR>
								<th> Netmask: </th>
								<TD><?=$netmask?></TD>
								<th> Primary DNS: </th>
								<TD><?print($computer->getData("primary_dns"));?></TD>
							</TR>
							<tr>
							<th></th><td></td>
							<th width="10%"> Secondary DNS: </th>
                            <TD><?print($computer->getData("secondary_dns"));?></TD>
                            </TR>
<?php //Start Private Net ------------------------------------------------- ?>
<?php
$comp_dc = $computer->GetDataCenterNumber();
if ($comp_dc == 1 || $comp_dc == 5 || $comp_dc == 7 || $comp_dc == 6 || $comp_dc == 13 || $comp_dc == 14) {  // do special PrivateNet/ManagedBackup mojo for DFW1/SAT4/LON2/LON3/HKG1
    // if they are in Dulles, give them the options for both
    // PrivateNet and Managed Backup.

    /*
    So...
    1.  get skus
    2.  get pnet ip
    */
    $pnet = $computer->HasPrivateNet();
    $lnet = $computer->HasLocalNet();
    $mb = $computer->HasManagedBackupClient();
    $pnnetwork = $computer->getPrivateNetHost();
    
    // TODO add HasServiceNet
    // TODO add HasAggExNet

    // we may change the logic for displaying mb and pn
    // based on SKUs down the road, hence the array
    $display_options = array();

    if ( $pnet ) {
        $display_options[] = 'pn';
    }
    if ( $lnet ) {
        $display_options[] = 'ln';
    }
    if ( $mb ) {
        $display_options[] = 'mb';
    }

    if ($computer->HasComplexManaged()) {
        ?>
        <TR>
            <TH> ComplexManaged: </TH>
            <td colspan="2">
                <?
                $cvlan = $computer->getComplexVLAN();
                $vlist = array();
                // PJ - if already associated with a VLAN
                if(count($cvlan) > 0) {
                    ?>
                    <table class="datatable">
                            <?
                            foreach ($cvlan as $v) {
                                $vname=$v["name"];
                                $vlist[] = $vname;
                                $vid=$v["id"];
                            ?>
                                <tr>
                                    <td>
                                        <?=$vname?>
                                    </td>
                                    <td>
                                        <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                                            <a href="javascript:makePopUpNamedWin('<?="/tools/popupRemoveComplexNet.php?computer_number=$computer_number&vid=$vid"?>',200,600,'',3,'ResetComputerPasswords<?=$computer_number?>')" class="text_button">Remove <?=$vname?></a>
                                        <? } else { ?>
                                            If you need to remove the configuration, please contact Networking.
                                        <? } ?>
                                    </td>
                                </tr>
                            <? } ?>
                    </table>
                <? }
                if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                    ?>
                    <form action="none">
                        <p>
                <select id="complexman_vlan">
                    <?php
                    $cman_list = getCMANList($customer_number, $computer->computer_number);
                    foreach($cman_list as $vlan) {
                        if (!in_array($vlan["name"], $vlist)) {
                            print "<option value=\"$vlan[number]\">
                                $vlan[name] </option>\n";
                        }
                    }
                    ?>
                    <option value="new"> New VLAN &hellip; </option>
                </select>
                    <a href="javascript:makePopUpNamedWin('<?="/tools/popupConfigureComplexNet.php?customer_number=$customer_number&computer_number=$computer_number&selected="?>'+document.getElementById('complexman_vlan').value,400,600,'',3,'ConfigureComplexMan<?=$computer_number?>')" class="text_button">Configure</a>
                        </p>
                    </form>
                    <?
                } else {
                    print "<p> You do not have permissions to configure complex managed settings. DC Ops or Networking will set this up for you. </p>\n";
                }
                ?>
            </td>
        </TR>
    <?
    }

    if ($computer->hasAggExNetSku()) {
        ?>
        <TR>
            <TH> AggExNet: </TH>
            <td colspan="2">
                <?
                $cvlan = $computer->getAggExNetVLANs();
                $vlist = array();
                if(count($cvlan) > 0) {
                    ?>
                    <table class="datatable">
                            <?
                            foreach ($cvlan as $v) {
                                $vname=$v["name"];
                                $vlist[] = $vname;
                                $vid=$v["id"];
                            ?>
                                <tr>
                                    <td>
                                        <?=$vname?>
                                    </td>
                                    <td>
                                        <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                                            <a href="javascript:makePopUpNamedWin('<?="/tools/popupRemoveAggExNet.php?computer_number=$computer_number&vid=$vid"?>',200,600,'',3,'ResetComputerPasswords<?=$computer_number?>')" class="text_button">Remove <?=$vname?></a>
                                        <? } else { ?>
                                            If you need to remove the configuration, please contact Networking.
                                        <? } ?>
                                    </td>
                                </tr>
                            <? } ?>
                    </table>
                <? }
                if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                    ?>
                    <form action="none">
                        <p>
                <select id="aggexnet_vlan">
                    <?php
                    $aggex_list = $computer->getCustomerAggExNetList();
                    foreach($aggex_list as $vlan) {
                        if (!in_array($vlan["name"], $vlist)) {
                            print "<option value=\"$vlan[id]\">
                                $vlan[name] </option>\n";
                        }
                    }
                    ?>
                    <option value="new"> New VLAN &hellip; </option>
                </select>
                    <a href="javascript:makePopUpNamedWin('<?="/tools/popupConfigureAggExNet.php?customer_number=$customer_number&computer_number=$computer_number&selected="?>'+document.getElementById('aggexnet_vlan').value,400,600,'',3,'ConfigureAggExNet<?=$computer_number?>')" class="text_button">Configure</a>
                        </p>
                    </form>
                    <?
                } else {
                    print "<p> You do not have permissions to configure aggregated exnet settings. DC Ops or Networking will set this up for you. </p>\n";
                }
                ?>
            </td>
        </TR>
    <?
    }

    if (in_array('pn', $display_options)) {
        $pnet_type = PNET_TYPE_PRIVATENET;
        // show the private net config
        ?>
        <TR>
            <TH> PrivateNet: </TH>
            <td colspan="3">
                <?
                $pnnetwork = $computer->getPrivateNetHost($pnet_type);
                if( !empty( $pnnetwork ) ) {
                    $private_net_ip = $computer->getPrivateNetIP($pnet_type);
                    $pnrouter = $computer->getPrivateNetManagedBackupGW($pnet_type);
                    $pnnetmask = $computer->getPrivateNetMask($pnet_type);
                    $pnvlan = $computer->getPrivateNetVLAN($pnet_type);
                    ?>
                    <table class="datatable">
                        <tr><th> PrivateNet VLAN: </th><td><?=$pnvlan?></td></tr>
                        <? if ($private_net_ip) { ?>
                            <tr><th> PrivateNet IP: </th><td><?=$private_net_ip?></td></tr>
                        <? } ?>
                        <tr><th> PrivateNet Network: </th><td> <?=$pnnetwork?> / <?=$pnnetmask?></td></tr>
                    </table>
                    <p>
                        <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                            <?
                            if ($private_net_ip) {
                                $change_ip_label = "Change IP";
                            } else {
                                $change_ip_label = "Set IP";
                            }
                            ?>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button"><? print $change_ip_label ?></a>
                            <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove Private Net Config</a>
                            <? } else { ?>
                                If you need to remove the configuration, please contact Networking.
                            <? } ?>
                        <? } ?>
                    </p>
                    <?
                } else {
                    if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                        // Unconfigured Private Net
                        ?>
                        <form action="none">
                            <p>
                    <select id="private_net_vlan">
                        <option> -- configure private net -- </option>
                        <?php
                        $vlan_list = getVLANList(
                            $customer_number,
                            $computer->getDatacenterNumber(),
                            $pnet_type);
                        foreach($vlan_list as $vlan) {
                            print "<option value=\"$vlan[number]\">
                                $vlan[name] </option>\n";
                        }
                        ?>
                        <option value="new"> New VLAN &hellip; </option>
                        <option value="used"> Other Account&#39;s VLAN &hellip;</option>
                    </select>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupConfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&selected='+document.getElementById('private_net_vlan').value,300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Configure</a>
                            </p>
                        </form>
                        <?
                    } else {
                        print "<p> You do not have permissions to configure private net. DC Ops or Networking will set this up for you. </p>\n";
                    }
                }
                ?>
            </td>
        </TR>
    <?
    }
    
    if (in_array('mb', $display_options)) {
        $pnet_type = PNET_TYPE_MANAGED_BACKUP;
        
	$client_label = "(NOT SET)";
	$comm_cell_ip = $computer->getClientBKHostIP();
	$comm_cell_name = '';
	if ($comm_cell_ip !== 0){
	    $client_label = $comm_cell_ip;
	    $comm_cell_name = $computer->getClientBKHostName();
	}
        // =======================================================================
        // show the managed backup config

        ?>
        <? if ($computer->HasManagedBackupClientCommVault()) { ?>
            <TR>
            <TH> Backup Client Host: </TH>
	    <td colspan="2"><?=$client_label?> <?=$comm_cell_name?> 
            <? if ( in_dept("PRODUCTION") ) { ?>
                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/commcell/popupChangeBKHost.pt?computer_number=<?=$computer_number?>',300,500,'',3,'ChangeBKHost<?=$computer_number?>')" class="text_button">Change</a>
            <? } ?>
            </td>
            </TR>
        <? } ?>
        <TR>
            <TH>  BackupNet: </TH>
            <td colspan="2">
                <?
                $pnnetwork = $computer->getPrivateNetHost($pnet_type);
                if( !empty( $pnnetwork ) ) {
                    $private_net_ip = $computer->getPrivateNetIP($pnet_type);
                    $pnrouter = $computer->getPrivateNetManagedBackupGW($pnet_type);
                    $pnnetmask = $computer->getPrivateNetMask($pnet_type);
                    $pnvlan = $computer->getPrivateNetVLAN($pnet_type);
                    ?>
                    <table class="datatable">
                        <tr><th> BackupNet VLAN: </th><td><?=$pnvlan?></td></tr>
                        <? if ($private_net_ip) { ?>
                            <tr><th> BackupNet IP: </th><td><?=$private_net_ip?></td></tr>
                        <? } ?>
                        <tr><th> BackupNet Network: </th><td> <?=$pnnetwork?> / <?=$pnnetmask?></td></tr>
                    </table>
                    <p>
                        <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                            <?
                            if ($private_net_ip) {
                                $change_ip_label = "Change IP";
                            } else {
                                $change_ip_label = "Set IP";
                            }
                            ?>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button"><? print $change_ip_label ?></a>
                            <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove Private Net Config</a>
                            <? } else { ?>
                                If you need to remove the configuration, please contact Networking.
                            <? } ?>
                        <? } ?>
                    </p>
                <?
                    if ($computer->HasManagedBackupClient()) {
                        $os = $computer->WhatOS();
                        $mgd_bu_network = $computer->getPrivateNetManagedBackupNetwork(PNET_TYPE_MANAGED_BACKUP);
                        $bits = split("/", $mgd_bu_network);
                        $mgd_bu_host = $bits[0];
                        $mgd_bu_netmask = $computer->getPrivateNetManagedBackupNetmask(PNET_TYPE_MANAGED_BACKUP);
                        $mgd_bu_router = $computer->getPrivateNetManagedBackupGW(PNET_TYPE_MANAGED_BACKUP);

                        // The nework for the Managed Backup server is SAT2-1000
                        // 10.225.225.0/255.255.255.190
                        // The network for Managed Backup in London is
                        // 10.2230.225.0/255.255.255.190
                        print "<p style=\"width: 50ex; display: table; margin: auto; font-size: 12px;\">In order for this server to reach the managed backup device, a static route must be installed. Please add a route for $mgd_bu_host/$mgd_bu_netmask with a next hop of $pnrouter.</p>";
                        print "<pre style=\"padding-left: 4ex; background: #EEE\">Example for ";
                        if( eregi( "win", $os ) or $os == "NT" ) {
                            print "Microsoft Windows: (this is permanent)\n";
                            print "   route add -p $mgd_bu_host MASK $mgd_bu_netmask $pnrouter";
                        } else {
                            print "Linux: (this needs to be added to a configuration file)\n";
                            print "    echo \"any net $mgd_bu_network gw $mgd_bu_router\" >> /etc/sysconfig/static-routes\n";
                            //Commented out per CORE-7760
                            //print "    /etc/rc.d/init.d/network restart\n";
                            //print "   route add -net $mgd_bu_network $mgd_bu_netmask gw $mgd_bu_router";
                        }
                        print "</pre>\n";
                    }
                } else {
                    if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                        // Unconfigured Managed Backup
                        ?>
                        <form action="none">
                            <p>
                    <select id="backup_net_vlan">
                        <option> -- configure backup net -- </option>
                        <?php
                        $vlan_list = getVLANList(
                            $customer_number,
                            $computer->getDatacenterNumber(),
                            $pnet_type);
                        foreach($vlan_list as $vlan) {
                            print "<option value=\"$vlan[number]\">
                                $vlan[name] </option>\n";
                        }
                        ?>
                        <option value="new"> New VLAN &hellip; </option>
                        <option value="used"> Other Account&#39;s VLAN &hellip;</option>
                    </select>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupConfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&selected='+document.getElementById('backup_net_vlan').value,300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Configure</a>
                            </p>
                        </form>
                        <?
                    } else {
                        print "<p> You do not have permissions to configure BackupNet. DC Ops or Networking will set this up for you. </p>\n";
                    }
                }
                ?>
            </td>
        </TR>
    <?
    }


} elseif (($computer->HasPrivateNet()) OR ($computer->HasManagedBackupClient())) {

	$client_label = "(NOT SET)";

	$comm_cell_ip = $computer->getClientBKHostIP();
        $comm_cell_name = '';
	if ($comm_cell_ip !== 0){
	    $client_label = $comm_cell_ip;
	    $comm_cell_name = $computer->getClientBKHostName();
	}


?>
    <? if ($computer->HasManagedBackupClientCommVault()) { ?>
        <TR>
            <TH> Backup Client Host: </TH>
            <td colspan="2"><?=$client_label?> <?=$comm_cell_name?>
            <? if ( in_dept("PRODUCTION") ) { ?>
                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/commcell/popupChangeBKHost.pt?computer_number=<?=$computer_number?>',300,500,'',3,'ChangeBKHost<?=$computer_number?>')" class="text_button">Change</a>
            <? } ?>
            </td>
        </TR>
    <? } ?>

    <TR>
        <TH> PrivateNet: </TH>
        <td colspan="2">
            <?
            $pnnetwork = $computer->getPrivateNetHost();
            if( !empty( $pnnetwork ) ) {
                $private_net_ip = $computer->getPrivateNetIP();
                $pnrouter = $computer->getPrivateNetManagedBackupGW();
                $pnnetmask = $computer->getPrivateNetMask();
                $pnvlan = $computer->getPrivateNetVLAN();
                ?>
                <table class="datatable">
                    <tr><th> PrivateNet VLAN: </th><td><?=$pnvlan?></td></tr>
                    <? if ($private_net_ip) { ?>
                        <tr><th> PrivateNet IP: </th><td><?=$private_net_ip?></td></tr>
                    <? } ?>
                    <tr><th> PrivateNet Network: </th><td> <?=$pnnetwork?> / <?=$pnnetmask?></td></tr>
                </table>
                <p>
                    <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                        <?
                        if ($private_net_ip) {
                            $change_ip_label = "Change IP";
                        } else {
                            $change_ip_label = "Set IP";
                        }
                        ?>
                        <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button"><? print $change_ip_label ?></a>
                        <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove Private Net Config</a>
                        <? } else { ?>
                            If you need to remove the configuration, please contact Networking.
                        <? } ?>
                    <? } ?>
                </p>
                <?
                if ($computer->HasManagedBackupClient()) {
                    $os = $computer->WhatOS();
                    $mgd_bu_network = $computer->getPrivateNetManagedBackupNetwork();
                    $mgd_bu_netmask = $computer->getPrivateNetManagedBackupNetmask();
                    $bits = split("/", $mgd_bu_network);
                    $mgd_bu_host = $bits[0];
                    $mgd_bu_router = $computer->getPrivateNetManagedBackupGW();

                    // The nework for the Managed Backup server is SAT2-1000
                    // 10.225.225.0/255.255.255.190
                    // The network for Managed Backup in London is
                    // 10.2230.225.0/255.255.255.190
                    print "<p style=\"width: 50ex; display: table; margin: auto; font-size: 12px;\">In order for this server to reach the managed backup device, a static route must be installed. Please add a route for $mgd_bu_host/$mgd_bu_netmask with a next hop of $pnrouter.</p>";
                    print "<pre style=\"padding-left: 4ex; background: #EEE\">Example for ";
                    if( eregi( "win", $os ) or $os == "NT" ) {
                        print "Microsoft Windows: (this is permanent)\n";
                        print "   route add -p $mgd_bu_host MASK $mgd_bu_netmask $mgd_bu_router";

                    } else {
                        print "Linux: (this needs to be added to a configuration file)\n";
                        print "    echo \"any net $mgd_bu_network gw $mgd_bu_router\" >> /etc/sysconfig/static-routes\n";
                        // js removed per DCE-65
                        //print "    /etc/rc.d/init.d/network restart\n";
                        "   route add -net $mgd_bu_network $mgd_bu_netmask gw $mgd_bu_router";
                    }
                    print "</pre>\n";
                }
            } else {
                if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                    // Unconfigured Private Net
                    ?>
                    <form action="none">
                        <p>
                <select id="private_net_vlan">
                    <option> -- configure private net -- </option>
                    <?php
                    $pnet_type = PNET_TYPE_PRIVATENET;
                    if ( $computer->getDatacenterNumber() == 8 ) {
                        $dc = 2;
                    } else {
                        $dc = $computer->getDatacenterNumber();
                    }
                    $vlan_list = getVLANList(
                        $customer_number,
                        $dc,
                        $pnet_type);
                    foreach($vlan_list as $vlan) {
                        print "<option value=\"$vlan[number]\">
                            $vlan[name] </option>\n";
                    }
                    ?>
                    <option value="new"> New VLAN &hellip; </option>
                    <option value="used"> Other Account's VLAN &hellip;
                        </option>
<?php #'
$comp_dc = $computer->getDatacenterNumber();
if( $comp_dc <= 4 || $comp_dc == 8 ) { # SAT1/LON1/SAT2/IAD1/LONB
?>
                    <option value="legacy-new">
                        Legacy -- New VLAN &hellip;
                        </option>
                    <option value="legacy-used">
                        Legacy -- Other Act&rsquo;s VLAN &hellip;
                        </option>
<?php
}
?>
                </select>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupConfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&selected='+document.getElementById('private_net_vlan').value,300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Configure</a>
                        </p>
                    </form>
                    <?
                } else {
                    print "<p> You do not have permissions to configure private net. DC Ops or Networking will set this up for you. </p>\n";
                }
            }
            ?>
        </td>
    </TR>
<?
}


// Localnet

    if ($computer->HasLocalNet()) {
        $pnet_type = PNET_TYPE_LOCALNET;
        // show the private net config
        ?>
        <TR>
            <TH> LocalNet: </TH>
            <td colspan="2">
                <?
                $pnnetwork = $computer->getPrivateNetHost($pnet_type);
                if( !empty( $pnnetwork ) ) {
                    $private_net_ip = $computer->getPrivateNetIP($pnet_type);
                    $pnrouter = $computer->getPrivateNetManagedBackupGW($pnet_type);
                    $pnnetmask = $computer->getPrivateNetMask($pnet_type);
                    $pnvlan = $computer->getPrivateNetVLAN($pnet_type);
                    ?>
                    <table class="datatable">
                        <tr><th> LocalNet VLAN: </th><td><?=$pnvlan?></td></tr>
                        <? if ($private_net_ip) { ?>
                            <tr><th> LocalNet IP: </th><td><?=$private_net_ip?></td></tr>
                        <? } ?>
                        <tr><th> LocalNet Network: </th><td> <?=$pnnetwork?> / <?=$pnnetmask?></td></tr>
                    </table>
                    <p>
                        <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                            <?
                            if ($private_net_ip) {
                                $change_ip_label = "Change IP";
                            } else {
                                $change_ip_label = "Set IP";
                            }
                            ?>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button"><? print $change_ip_label ?></a>
                            <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove Private Net Config</a>
                            <? } else { ?>
                                If you need to remove the configuration, please contact Networking.
                            <? } ?>
                        <? } ?>
                    </p>
                    <?
                } else {
                    if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                        // Unconfigured Private Net
                        ?>
                        <form action="none">
                            <p>
                    <select id="local_net_vlan">
                        <option> -- configure local net -- </option>
                        <?php
                        $vlan_list = getVLANList(
                            $customer_number,
                            $computer->getDatacenterNumber(),
                            $pnet_type);
                        foreach($vlan_list as $vlan) {
                            print "<option value=\"$vlan[number]\">
                                $vlan[name] </option>\n";
                        }
                        ?>
                        <option value="new"> New VLAN &hellip; </option>
                        <option value="used"> Other Account&#39;s VLAN &hellip;</option>
                    </select>
                                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupConfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&selected='+document.getElementById('local_net_vlan').value,300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Configure</a>
                            </p>
                        </form>
                        <?
                    } else {
                        print "<p> You do not have permissions to configure localnet. DC Ops or Networking will set this up for you. </p>\n";
                    }
                }
                ?>
            </td>
        </TR>
    <?
    }


// end of localnet

// ServiceNet
    if ($computer->hasServiceNetIp()) {
        // show the service net config
        ?>
        <TR>
            <TH> ServiceNet: </TH>
            <td colspan="2">
                <?
                $ip = $computer->ip->getServiceNetIPs();
                if( !empty( $ip ) ) {
                    $service_net_ip = $ip['IPAddress'];
                    $service_net_netmask = '255.255.192.0';//$ip['Netmask'];
                    $service_net_gateway = $ip['Gateway'];
                    ?>
                    <table class="datatable">
                        <? if ($service_net_ip) { ?>
                            <tr><th> ServiceNet IP: </th><td><?=$service_net_ip?></td></tr>
                            <tr><th> ServiceNet Netmask: </th><td><?=$service_net_netmask?></td></tr>
                            <tr><th> ServiceNet Gateway: </th><td><?=$service_net_gateway?></td></tr>
                        <? } ?>
                        <?
                        print "<tr><th></th><td><p style=\"width: 50ex; display: table; margin: auto; font-size: 12px;\">";
                        print "In order for this server to reach the Services Network, a static route must be installed. ";
                        print "Please add a route for 10.191.192.0/18 with a next hop of $service_net_gateway.</p>";
                        print "<pre style=\"padding-left: 4ex; background: #EEE\">Example for ";
                        if( eregi( "win", $os ) or $os == "NT" ) {
                            print "Microsoft Windows: (this is permanent)\n";
                            print "   route add -p 10.191.192.0 MASK $service_net_netmask $service_net_gateway";

                         } else {
                            print "Linux: (this needs to be added to a configuration file)\n";
                            print "    echo \"any net 10.191.192.0/18 gw $service_net_gateway\" >> /etc/sysconfig/static-routes\n";
                             "   route add -net 10.191.192.0/18 $service_net_netmask gw $service_net_gateway";
                            }
                        print "</pre></td></tr>\n"; ?>
                    </table>
               <? } ?>
            </td>
        </TR>
    <?
    }

// end of ServiceNet

///////////////////////
/// THE VMNET STUFF ///
///////////////////////

if ($computer->isHyperVisor() && !$computer->isCustomerActiveHyperVisor()) {
  //  that is a check to see if the hypervisor has active customer access
  
  $vmnet_ip = '';
  $vmnet_gateway = '';
  $vmnet_netmask = '';
  $vmnet_primary_dns = '';
  $vmnet_secondary_dns = '';
  $vmnet_kernal_ip = '';
  $vmnet_cab_panel = '';
  $vmnet_agg_panel = '';

  $pnet_type = PNET_TYPE_VMNET;

  $vmnet_vlan_label = 
      "<a href=\"javascript:makePopUpNamedWin(" . 
      "'$py_app_prefix/computer/" . 
      "popupConfigurePrivateNet.esp?" . 
      "computer_number=$computer_number" . 
      "&pnet_type=$pnet_type&selected=new_vmnet'," . 
      "360,500,'',3," . 
      "'ConfigurePrivateNet$computer_number')\" class=\"text_button\">" . 
      "Assign VLAN</a>";

  $vmnet_vlan = $computer->getPrivateNetVLAN($pnet_type);
  $vmnet_display_buttons = false;

  if ($vmnet_vlan != ''){
      $vmnet_vlan_label = $vmnet_vlan;
      $vmnet_ip = $computer->getPrivateNetIP($pnet_type);
      $vmnet_gateway = $computer->getPrivateNetManagedBackupGW($pnet_type);
      $vmnet_netmask = $computer->getPrivateNetMask($pnet_type);      

      // get the VMNet specific info,  this function takes parameters by reference
      $computer->getVmNetPrivateNetInfo($vmnet_kernal_ip, 
					$vmnet_primary_dns, 
					$vmnet_secondary_dns,
					$vmnet_cab_panel,
					$vmnet_agg_panel);

      $vmnet_cab_div = $vmnet_cab_panel . "&nbsp&nbsp" .
	                "<a href='#' onclick='makeEditVMNetPanels($computer_number,\"vmnet_cab_panel\",\"$vmnet_cab_panel\");return false;'>" . 
	                '<img src="/ui/v2/rui/resources/images/icons/famfam/computer_edit.png" title="Edit Cabinet Panel"></a>';

      $vmnet_agg_div = $vmnet_agg_panel . "&nbsp&nbsp" .
	                "<a href='#' onclick='makeEditVMNetPanels($computer_number,\"vmnet_agg_panel\",\"$vmnet_agg_panel\");return false;'>" . 
	                '<img src="/ui/v2/rui/resources/images/icons/famfam/computer_edit.png" title="Edit Aggregate Panel"></a>';


      $vmnet_display_buttons = true;
  }

?>

    <TR>
        <TH> VM Net: </TH>
        <TD>
        <table class="datatable">
            <tr><th> VM Net VLAN: </th><td><?=$vmnet_vlan_label?></td></tr>
            <tr><th> VM Net IP: </th><td><?=$vmnet_ip?></td></tr>
            <tr><th> VM Net Gateway: </th><td><?=$vmnet_gateway?></td></tr>
            <tr><th> VM Net Netmask: </th><td><?=$vmnet_netmask?></td></tr>
            <tr><th> VM Net Primary DNS: </th><td><?=$vmnet_primary_dns?></td></tr>
            <tr><th> VM Net Secondary DNS: </th><td><?=$vmnet_secondary_dns?></td></tr>
            <tr><th> VM Net Kernal IP: </th><td><?=$vmnet_kernal_ip?></td></tr>
            <tr><th> Cabinet Panel: </th><td><div id="vmnet_cab_panel"><?=$vmnet_cab_div?></div></td></tr>
            <tr><th> Aggregation Panel: </th><td><div id="vmnet_agg_panel"><?=$vmnet_agg_div?></div></td></tr>
        </table>
        <? if ($vmnet_display_buttons) { ?>
	<p>
        <!--this is the label for the vmnet IP popup -->
        <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Edit IP</a>
        <!--this is the label for the vmnet Kernal IP popup -->
        <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&vmnet_ip_type=kernal_ip',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Edit Kernal IP</a>
        <!--this is the label for the remove vmnet -->
<a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove VM Net Config</a>
        </p>
        <? }// to the if display buttons 
?>

</TR>

<?
} // to the if hypervisor

///////////////////////
/// VMNET END  ////////
///////////////////////

if( $computer->account->segment_id == PLATFORM_HOSTING_SEGMENT ) {
    $drac = $computer->getPlatHostDRACInfo();
?>
    <TR>
       <TH> DRAC / iLo </TH>
        <td colspan="2">
                <table class="datatable">
                 <tr><th> DRAC/iLo IP Address </th><td><?=$drac['ip_address']?></td></tr>
                 <tr><th> DRAC/iLo Network </th><td><?=$drac['network']?></td></tr>
                 <tr><th> DRAC/iLo Gateway </th><td><?=$drac['gateway']?></td></tr>
                </table>
                                    <A
    href="javascript:makePopUpNamedWin('<?="$py_app_prefix/computer/editPlatHostDRAC.pt?computer_number=$computer_number"?>',400,400,'',3,'DRAC<?=$computer_number?>')"
    class="text_button">
        Edit DRAC/iLo Configuration
</a>
&nbsp;
                                    <A
    href="javascript:makePopUpNamedWin('<?="$py_app_prefix/computer/removePlatHostDRAC.pt?computer_number=$computer_number"?>',400,400,'',3,'DRAC<?=$computer_number?>')"
    class="text_button">
        Remove DRAC/iLo Configuration
</a>
&nbsp;
        </td>
    </TR>

<?

}
# DRACNet


if ($computer->HasDRACNet() ) { //and ($computer->account->segment_id != PLATFORM_HOSTING_SEGMENT) ) {
    $pnet_type = PNET_TYPE_DRACNET;
    $pnet_name = "DRACNet";
    // show the private net config
    ?>
    <TR>
       <TH> <?= $pnet_name ?>: </TH>
        <td colspan="2">
            <?
            $pnnetwork = $computer->getPrivateNetHost($pnet_type);
            if( !empty( $pnnetwork ) ) {
                #$private_net_ip = $computer->getPrivateNetIP($pnet_type);
                $private_net_ips = $computer->getPrivateNetIPList($pnet_type);
                $pnrouter = $computer->getPrivateNetManagedBackupGW($pnet_type);
                $pnnetmask = $computer->getPrivateNetMask($pnet_type);
                $pnvlan = $computer->getPrivateNetVLAN($pnet_type);

                #this method is somewhat misnamed. It gets the first IP of a privatenet block,
                #which is used for the gateway on many things *including* managed backup (and dracnet, and... )
                $pngw = $computer->getPrivateNetManagedBackupGW($pnet_type);

                ?>
                <table class="datatable">
                 <tr><th> <?=$pnet_name?> VLAN: </th><td><?=$pnvlan?></td></tr>
                 <? if( sizeof($private_net_ips) > 0) {
                         foreach($private_net_ips as $private_net_ip){
                    ?>
                <tr><th> <?=$pnet_name?> IP:
                    <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                <a style="font-size: 12px;"  href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?>&old_ip=<?=$private_net_ip ?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Change IP</a>
                        <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                    <a style="font-size: 12px;" href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&ip_address=<?=$private_net_ip ?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove IP</a>
                        <? } ?>
                    <? } ?></th><td><a href="https://<?=$private_net_ip?>/"><?=$private_net_ip?></a>
                    </td></tr>
                    <?
                         }
                     } ?>
                <tr><th> <?=$pnet_name?> Network: </th><td> <?=$pnnetwork?> / <?=$pnnetmask?></td></tr>
                <tr><th> <?=$pnet_name?> Gateway: </th><td> <?=$pngw?> </td></tr>
                </table>
                <p>
                    <? if( sizeof($private_net_ips) > 0) {
                            $add = "&add=1";
                       }else{
                            $add = "";
                       } ?>
                    <? if (in_dept( "PRODUCTION|NETWORK|SUPPORT")) { ?>
                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupReconfigurePrivateNet.esp?computer_number=<?=$computer_number?><?= $add ?>&pnet_type=<?=$pnet_type?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Add IP</a>
                <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupDRACPasswords.pt?computer_number=<?=$computer_number?>',300,500,'',3,'ViewDRACPasswords<?=$computer_number?>')" class="text_button">DRAC Login Info</a>

                        <? if (in_dept("NETWORK|PROFESSIONAL_SERVICES|SUPPORT")) { ?>
                    <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupRemovePrivateNet.esp?pnet_type=<?=$pnet_type?>&computer_number=<?=$computer_number?>',300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Remove <?=$pnet_name?> Config</a>
                        <? } else { ?>
                            If you need to remove the configuration, please contact Networking.
                        <? } ?>
                    <? } ?>
                </p>
            <? if( sizeof($private_net_ips) > 0) { ?>
            <p style="width: 50ex; display: table; margin: auto; font-size: 12px;">
             To configure the DRACs properly, run the <i>racadm</i> command as follows:
            <pre style="padding-left: 4ex; background: #EEE">Example:
<? foreach($private_net_ips as $private_net_ip){ ?>racadm setniccfg -s <?= $private_net_ip ?> <?= $pnnetmask ?> <?=$pngw?>
<? } ?>

racadm racreset
</pre></p><? } ?>
                <?
            } else {
                if( in_dept( "PRODUCTION|NETWORK|SUPPORT" ) ) {
                    // Unconfigured Private Net
                    ?>
                    <form action="none">
                        <p>
                <select id="drac_net_vlan">
                        <option> -- configure drac net -- </option>
                    <?php
                    $vlan_list = getVLANList(
                        $customer_number,
                        $computer->getDatacenterNumber(),
                        $pnet_type);
                    foreach($vlan_list as $vlan) {
                        print "<option value=\"$vlan[number]\">
                            $vlan[name] </option>\n";
                    }
                    ?>
                    <option value="new"> New VLAN &hellip; </option>
                    <option value="used"> Other Account&#39;s VLAN &hellip;</option>
                </select>
                            <a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupConfigurePrivateNet.esp?computer_number=<?=$computer_number?>&pnet_type=<?=$pnet_type?>&selected='+document.getElementById('drac_net_vlan').value,300,500,'',3,'ConfigurePrivateNet<?=$computer_number?>')" class="text_button">Configure</a>
                        </p>
                    </form>
                    <?
                } else {
                print "<p> You do not have permissions to configure <?=$pnet_name?>. DC Ops or Networking will set this up for you. </p>\n";
                }
            }
            ?>
        </td>
    </TR>
<?
}
?>
<?//End Private Net -------------------------------------------------------- ?>
<?//Start FW Config info----------------------------------------------------- ?>
<?

$i_account = ActFactory::getIAccount();
$acct_obj = $i_account->getAccountByAccountNumber($db, $customer_number);

if ($computer->isAssociatedWithSkunit(ALL_FIREWALL_PLATFORMS_SKUNIT_ID) == 1 and $acct_obj->segment_id  == MANAGED_SEGMENT   ){  ?>
        <tr>
            <th> Firewall Configuration: </th>
            <td>
        <a href = "javascript:makePopUpNamedWin('/py/computer/popupFirewallInfo.pt?computer_number=<?=$computer_number?>',600,1024,'',3,'DisplayFirewallConfiguration')"
                class = "text_button">View Firewall Config</a>
            </td>
       </tr>
<? } ?>
<?//End FW Config info -------------------------------------------------------- ?>
<?//Start Leasedline info -------------------------------------------------------?>
<? if($computer->isLeasedLine()) {
    $query = "select c.\"Name\",
                     i.circuit_id,
                     m.\"Name\"
              from \"COMP_LeasedLineInfo\" i,
                   \"COMP_val_LeasedLineMedium\" m,
                   \"COMP_val_LeasedLineCarrier\" c
              where i.computer_number = $computer_number
                    and m.\"COMP_val_LeasedLineMediumID\" = i.medium
                    and c.\"COMP_val_LeasedLineCarrierID\" = i.carrier";
    $ll_query = $db->SubmitQuery($query);
    $num = $db->NumRows($ll_query);
    $ll_carrier = "";
    $ll_circuit_id="";
    $ll_medium="";
    if ($num > 0){
        $ll_carrier = $db->GetResult($ll_query, 0, 0);
        $ll_circuit_id = $db->GetResult($ll_query, 0, 1);
        $ll_medium = $db->GetResult($ll_query, 0, 2);
    }

?>
    <tr>
        <th>LeasedLine Info: </th>
        <td>
    <? if ($num >0){ ?>
        <table>
    <tr><th>Carrier: </th><td> <?= $ll_carrier ?></td></tr>
        <tr><th>Circuit ID: </th><td> <?= $ll_circuit_id ?></td></tr>
        <tr><th>Medium:</th><td> <?= $ll_medium  ?></td></tr>
    <tr><td colspan="2"><a href="javascript:makePopUpNamedWin('/py/computer/edit_leasedline.pt?computer_number=<?= $computer_number?>',200,510,'',3,'EditLeasedLineInfo')" class="text_button">Edit LeasedLine Info</a></td></tr>
        </table>
    <? }else{ ?>
          <a href="javascript:makePopUpNamedWin('/py/computer/edit_leasedline.pt?computer_number=<?= $computer_number?>',200,510,'',3,'AddLeasedLineInfo')" class="text_button">Add LeasedLine Info</a>
    <? } ?>
        </td>
    </tr>
<? } ?>
<?//End Leasedline info -------------------------------------------------------?>
<?//Start NAT Comments ----------------------------------------------------- ?>
<?php
  // we do not want to predicate this on having PrivateNet;
  // get the criteria from sromines
        $query = "select * from \"ADDR_NATComment\" where server=$computer_number;";
        $nat_query = $db->SubmitQuery($query);

        $num = $db->NumRows($nat_query);
        ?>
        <TR>
            <th> NAT Comments: </th>
            <td>
                <TABLE class=datatable>
                <? if ( $num > 0 ): ?>
                <tr align="center">
                    <th>Primary</th>
                    <th>NAT 1</th>
                    <th>NAT 2</th>
                </tr>
                <? endif; ?>
                <tr>
                    <td>
                <?
                $primary_ip = $computer->getData("primary_ip");
                    for ( $i = 0; $i < $num; $i++)
                    {
                        $nat1 = $db->GetResult($nat_query, $i, 1);
                        $nat2 = $db->GetResult($nat_query, $i, 2);
                        print("<tr><td>$primary_ip</td><td>$nat1</td><td>$nat2</td></tr>\n");
                    }
                ?></td>
                <?if(in_dept("PROFESSIONAL_SERVICES|PERM_IP_ADMIN|SUPPORT")):?>
                <td>
                <?
                $href = "javascript:makePopUpNamedWin('$py_app_prefix/ipaddress/admin/popupPrivateNetNotes.esp?account_number=$customer_number&computer_number=$computer_number',600,510,'',3,'CreatePrivateNetComment')";
                $href = "<a href=\"$href\" class=\"text_button\">";
                print $href;
                print 'Edit NAT Comments';
                print "</a>";
                ?>
                </td>
                <?endif; // Edit NAT ?>
            </tr>
            </table>
        </td>
        </tr>

<?//End NAT Comments -------------------------------------------------------- ?>
<?//Start Unison ------------------------------------------------------------ ?>
							<?if ($computer->HasUnison()):?>
							<TR>
							<th> Unison Info: </th>
							<TD colspan=3>
							<TABLE>
							<?
								for ($i=0;$i<$computer->GetNumParts();$i++)
								{
										$part_info=$computer->GetPartArray($i);
										if ($part_info["val_add"]=="t"&&$part_info["val_add_obj"]!=""&&ereg("unison",$part_info["product_label"]))
										{
											$product_label=$part_info["product_label"];
											$product_sku=$part_info["product_sku"];
											$val_add_obj=BuildValAddObj($db,$computer->computer_number(),$product_label,$product_sku,ADMIN);


											$product_description=$RackPartLabels[$product_label]." - ".$part_info["product_description"];

								?>
									  <tr  align="center">
										<TH BGCOLOR=#FFFFFF>
										<?if ($val_add_obj->isFullyConfigured()):?>
										<IMG SRC="assets/images/box_checked.gif" width="26" height="29">
										<?else:?>
										<?$unconfigured_products=true;?>
										<IMG SRC="assets/images/box_unchecked.gif" width="26" height="29">
										<?endif;?>
										</TH>
										<td colspan="1" bgcolor="#E7E3D6"><font face="Arial, Helvetica, sans-serif" size="-1"><?print($product_description);?></font>
											<font face="Arial, Helvetica, sans-serif" size="-1">
											<BR>
										   <FORM ACTION="view_product_configuration.php" METHOD="POST">
										   <INPUT TYPE=HIDDEN NAME="computer_number" VALUE="<?print($computer_number);?>">
										   <INPUT TYPE=HIDDEN NAME="product_description" VALUE="<?print($product_description);?>">
										   <INPUT TYPE=HIDDEN NAME="product_label" VALUE="<?print($product_label);?>">
										   <INPUT TYPE=HIDDEN NAME="product_sku" VALUE="<?print($product_sku);?>">
										   <INPUT TYPE=IMAGE SRC="assets/images/view_txt_bttn.gif" height=22 width=44 BORDER=0 VALIGN=middle>
										   </FORM>
											</font>
										</td>

									  </tr>
							<?
									}

							}?>
							</TABLE></TD>
							</TR>
							<?endif;?>
<?//End Unison -------------------------------------------------------------- ?>





<?//Begin DAS --------------------------------------------------------------- ?>
							<?if ($computer->HasDAS()):?>
							<TR>
							<th> DAS Info: </th>
							<TD colspan="3">
							<TABLE>
							<?
								for ($i=0;$i<$computer->GetNumParts();$i++)
								{
										$part_info=$computer->GetPartArray($i);
										if ($part_info["val_add"]=="t"&&$part_info["val_add_obj"]!=""&&ereg("uses_das",$part_info["product_label"]))
										{
											$product_label=$part_info["product_label"];
											$product_sku=$part_info["product_sku"];
											$val_add_obj=BuildValAddObj($db,$computer->computer_number(),$product_label,$product_sku,ADMIN);


											$product_description=$RackPartLabels[$product_label]." - ".$part_info["product_description"];

								?>
									  <tr  align="center">
										<TH BGCOLOR=#FFFFFF>
										<?if ($val_add_obj->isFullyConfigured()):?>
										<IMG SRC="assets/images/box_checked.gif" width="26" height="29">
										<?else:?>
										<?$unconfigured_products=true;?>
										<IMG SRC="assets/images/box_unchecked.gif" width="26" height="29">
										<?endif;?>
										</TH>
										<td colspan="1" bgcolor="#E7E3D6"><font face="Arial, Helvetica, sans-serif" size="-1"><?print($product_description);?></font>
											<font face="Arial, Helvetica, sans-serif" size="-1">
											<BR>
										   <FORM ACTION="view_product_configuration.php" METHOD="POST">
										   <INPUT TYPE=HIDDEN NAME="computer_number" VALUE="<?print($computer_number);?>">
										   <INPUT TYPE=HIDDEN NAME="product_description" VALUE="<?print($product_description);?>">
										   <INPUT TYPE=HIDDEN NAME="product_label" VALUE="<?print($product_label);?>">
										   <INPUT TYPE=HIDDEN NAME="product_sku" VALUE="<?print($product_sku);?>">
										   <INPUT TYPE=IMAGE SRC="assets/images/view_txt_bttn.gif" height=22 width=44 BORDER=0 VALIGN=middle>
										   </FORM>
											</font>
										</td>

									  </tr>
							<?
									}

							}?>
							</TABLE>
							</TD></TR>
							<?endif;?>
<?//End DAS ----------------------------------------------------------------- ?>
<?//Begin Routes ------------------------------------------------------------ ?>
            <TR><th> Routes: </th>
            <TD COLSPAN="3">
            <TT>
                <?
                //Now display the current routes assigned to this customer
                $routes=$db->SubmitQuery("
                SELECT ip_block, num_ips_reserved,
                    plain_host(ip_block),
                    host(netmask(ip_block)) as netmask,
                    masklen(ip_block), active
                FROM route_table
                WHERE computer_number=$computer_number
                ORDER BY ip_block
                ");
                print "<table cellspacing='0'>\n";
                for ($i=0;$i<$routes->numRows();$i++)
                {
                    $row = $routes->fetchArray($i);
                    $sent_to_router = $row['active'];
                    $ips_in_block = pow(2, (32 - $row['masklen']));
                    $octets = explode('.', $row['plain_host']);
                    $first_suffix_octet = $octets[3];
                    $base_network = substr($row['ip_block'], 0,
                        strrpos($row['ip_block'], '.'));

                    printf("<tr><td>#% 2d.</td>", ($i + 1));
                    if(in_dept("NETWORK|PRODUCTION_SUPERVISOR")) {
                        print("<td><a target=\"new\" href=\""
                            .$GLOBALS['ipspace_base']
                            ."ip_blocks/addresses?ip=".$base_network
                            .".".$first_suffix_octet."\">"
                            ."$row[plain_host]</a></td>");
                    } else {
                        print("<td>".$row['plain_host']."</td>");
                    }

                    // IPSPACE - DONE - Removed display of Number of Reserved IPs
                    if( $sent_to_router == 'f' ) {
                        print "<td>(was not sent to router)</td>";
                    }
                    print "</tr>\n";
                }
                $routes->freeResult();
                print "</table>\n";
                ?>
            </TT>
            </TD>
            </TR>
<?//End Routes -------------------------------------------------------------- ?>



<?php } ?>
<?//Begin Addt'l IPS ------------------------------------------------------- ?>
			<?
				//Now display the ips they have assigned

                // IPSPACE - DONE - Get a list of additional IPs from the IP object
                $ips = $computer->ip->getNonPrimaryIPs();
                $num = count($ips);
                // We only want to show the first 10 ips
                $ips = array_slice($ips, 0, 10);
                if ($num) {
					print("<TR><th> Additional IPs: </th><TD>");

                    $os = $computer->WhatOS();
                    if( $os == 'NT' or
                        $os == 'Netra' or
                        $os == 'Sun' or
                        $os == 'SunWG' or
                        preg_match('/win2k/i', $os) ) {
                        // We only show the real netmask for Windows
                        // and Solaris servers because they can't handle
                        // handle 255.255.255.255 for additional IPs.
                        // They need a netmask of 8 ips or more.
                    } else {
                        // This default netmask is used for all
                        // people who check old servers and find that the
                        // netmask does not match.
                        $default_netmask = '255.255.255.255';
                    }

                    print "<table>\n";
					print "<tr>\n<th> IP(s) </th></tr>\n";
                    foreach ($ips as $ip) {
						$ip_bits=explode(".", $ip);
						$ip_block="$ip_bits[0].$ip_bits[1].$ip_bits[2]/24";
                        print "<tr>";
                        if (in_dept("NETWORK|PRODUCTION_SUPERVISOR")) {
                            // IPSPACE - Fix the link to display_ips.php?ip_block
                            print "<td><tt><a target=\"new\" href=\"".$GLOBALS['ipspace_base']."ip_blocks/addresses?ip=".$ip."\">";
							print "$ip</A></tt></td>\n";
                        } else {
                            print "<td><tt>$ip</tt></td>\n";
                        }
                    }
                    print ("</tr>\n");
                    print "</table>\n";
                    print "<font style='font-weight: bold'>";
                    print "Total: $num IP Addresses<br>\n";
                    print "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupSecondaryIPList.pt?computer_number=$computer_number', 450, 450, '', 3, 'SecondaryIP$computer_number')\">Show All Additional IPs & Netmasks</a></font>";
                }
                $db->FreeResult($ips);

			?>
<?//End Addt'l IPs --------------------------------------------------------- ?>


<!-- Start MySQL Network subscription -->
<?php 
if ($computer->hasMySQLNetwork()) { 
?>
<tr>
    <th>MySQL Network Subscription:</th>
    <td colspan="3">
<?php
    //should only be one of these
    $order = $db->SubmitQuery("
        select \"Info\"
        from \"COMP_ExternalProductInfo\"
        where computer_number = ".$computer->computer_number."
        and product_sku = '102894'
    ");

    if ($order->numRows() > 0) {
        $order_info = $order->getCell(0, 0);
        print "<div>Order #$order_info</div>\n";
    } else { 
?>
        <a href="javascript:makePopUpNamedWin('/mysql_net_order_form.php?computer_number=<?=$computer_number?>&account_number=<?=$account_number?>',170,350,'',3,'PlaceMySQLNetworkOrder<?=$computer_number?>')" class="text_button">Place Subscription Order</a>
<?php 
    }
?>
    </td>
</tr>
<?php 
} 
?>
<!-- END MySQL Network subscription -->
<!-- Start UNAS -->
<?php 
$os = $computer->WhatOS();
if ( $os == "UNAS" ) {
    print "<tr><th bgcolor=\"#cccccc\">\n";
    print "UNAS<br>Configuration:\n";
    print "</th><td colspan=\"3\">";
    $result = $db->submitQuery("select ss.share_name, ss.storage_unit, ss.hard_cap, ss.share_size, ssp.name, ss.alert_threshold, ss.id from shared_storage ss join shared_storage_protocol ssp on (ss.protocol = ssp.id) where computer_number = $computer_number");
    if ( $result->numRows() ) {
        ?>
        <table width="100%">
            <tr><th>Share Name</th><th>Storage Unit</th><th>Hard Cap</th><th>Share Size</th><th>Proto</th><th>Alert Threshold</th><th>Hosts</th><th>Edit</th><th>Delete</th></tr>
        <?
        for ( $i = 0; $i < $result->numRows(); $i++ ) {
            $share_id = $result->getCell($i, 6);
            
            if ($i%2 == 0) {
                print "<tr bgcolor=\"#ececec\" valign=\"top\">";
            }
            else {
                print "<tr valign=\"top\">";
            }
            for ( $j = 0; $j < 6; $j++ ) {
                print "<td>" . $result->getCell($i, $j) . "</td>";
            }
            
            print "<td>";
            $hosts = $db->submitQuery("select ssh.computer_number, ssh.ip_address from shared_storage_host_computer ssh where ssh.shared_storage_id = " . $result->getCell($i, $j));
            for ( $x = 0; $x < $hosts->numRows(); $x++ ) {
                $svr_num = $hosts->getCell($x, 0);
                $svr_img = getImageForServer($svr_num);
                print "<a href=\"/tools/DAT_display_computer.php3?computer_number=$svr_num\">$svr_img $svr_num</a> - " . $hosts->getCell($x, 1) . "<br/>";
            }
            print "</td>";
            
            $edit_href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/configureSharedStorage.pt?computer_number=$computer_number&mode=edit&share_id=$share_id',500,600,'',3,'ConfigureSharedStorage$computer_number')";
            $edit_href = "<a href=\"$edit_href\" border=\"0\">";
            $del_href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/configureSharedStorage.pt?computer_number=$computer_number&mode=delete&share_id=$share_id',500,600,'',3,'ConfigureSharedStorage$computer_number')";
            $del_href = "<a href=\"$del_href\" border=\"0\">";
            if ( in_dept("NAS_ENGINEER") ) {
                print "<td>$edit_href<img src=\"/img/icon_edit.gif\"</a></td>";
                print "<td>$del_href<img src=\"/img/icon_delete.gif\"</a></td>";
            }
            else {
                print "<td></td><td></td>";
            }
            print "</tr>";
            print "</tr>";
        }
        print "</table>";
        
        if ( in_dept("NAS_ENGINEER") ) {
                    $href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/configureSharedStorage.pt?computer_number=$computer_number&mode=new',500,600,'',3,'ConfigureSharedStorage$computer_number')";
                    $href = "<a href=\"$href\" class=\"text_button\">";
                    print "$href Add Share </a>\n";
                }

        print "</td></tr>\n";
        print "<tr><th bgcolor=\"#cccccc\">\n";
        print "UNAS Usage Report:";
        print "</th><td colspan\"2\">";
        $unas_report_href = "http://my.rackspace.com/unas-storage/" . $computer->computer_number;
        print "<a href=\"$unas_report_href\" class=\"text_button\"> Report </a>";
        print "</td></tr>\n";
    }
    else if ( in_dept("NAS_ENGINEER") ) {
        $href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/configureSharedStorage.pt?computer_number=$computer_number&mode=new',500,600,'',3,'ConfigureSharedStorage$computer_number')";
        $href = "<a href=\"$href\" class=\"text_button\">";
        print "$href Configure </a>\n";
        print "</td></tr>\n";
    }
    else {
        print "No Shares Configured";
        print "</td></tr>\n";
    }

}
if ($computer->isAssociatedWithSkunit(446) == 1) {
    print "<tr><th bgcolor=\"#cccccc\">\n";
    print "UNAS<br>Shares:\n";
    print "</th><td colspan=\"3\">";
    $shares = $computer->getSharedStorageShares();
    foreach ($shares as $share) {
        $svr_img = getImageForServer($share);
        print "<a href=\"/tools/DAT_display_computer.php3?computer_number=$share\">$svr_img $share</a><br/>";
    }
    print "</td></tr>";
}
?>
<!-- End UNAS -->
<!-- Start Managed Storage -->
<?php
$os = $computer->WhatOS();
if ($os == "Managed Storage") {
?>
<tr>
    <th bgcolor="#cccccc;">Managed Storage<br>Configuration:</th>
    <td colspan="2">
<?php
    $result = $db->submitQuery("
        select lun_id, raid_level, storage_group_name, storage_array, host_name, capacity
        from managed_storage
        where computer_number = $computer_number
        order by lun_id
    ");
    $count = $result->numRows();
    if ($count) {
?>
        <table>
            <tr>
                <th>LUN ID</th>
                <th>RAID Level</th>
                <th>Storage Group</th>
                <th>Storage Array</th>
                <th>Host</th>
                <th>Capacity</th>
            </tr>
<?php
        for ($i = 0; $i < $count; $i++) {
            $row = $result->fetchArray($i);
?>
            <tr>
                <td><?= $row['lun_id']; ?></td>
                <td><?= $row['raid_level']; ?></td>
                <td><?= $row['storage_group_name']; ?></td>
                <td><?= $row['storage_array']; ?></td>
                <td><?= $row['host_name']; ?></td>
                <td><?= $row['capacity']; ?></td>
            </tr>
<?php
        }
?>
        </table>
<?php 
    } else { 
?>
Currently unconfigured
<?php 
    }
    
    if(in_dept("PROFESSIONAL_SERVICES|PRODUCTION|PRODUCTION_SUPERVISOR")) {
        $href = "javascript:makePopUpNamedWin('$py_app_prefix/computer/configureManagedStorage.esp?computer_number=$computer_number',600,510,'',3,'ConfigureManagedStorage$computer_number')";
        print "<br><a href=\"$href\" class=\"text_button\">Configure</a>\n";
    }
?>
    </td>
</tr>
<?php
}
?>
<!-- End Managed Storage -->

<?php
// Display firewalled or load-balanced servers

//this displays a managed backup virtual device on the server page.
if ( $os != "Managed Backup" ) {
    //get the Managed Backup Agent
    $results = $db->SubmitQuery("
        SELECT p.product_description
        FROM sku p
            JOIN server_parts sp on (p.product_sku = sp.product_sku)
        WHERE sp.computer_number = " . $computer->computer_number . "
            AND p.product_description ilike 'Managed Backup Agent -%'
        ");
    $backup_system = preg_replace("/Managed Backup Agent - (.*)/i", "$1", $results->getCell(0,0));

    //we have the agent used to backup (usually CommVault or Legato)
    //will not show up unless the virtual device has a sku listed with the name of the agent in it.
    //ie: "Managed Backup Agent - CommVault" on the server is going to need
    //"CommVault" in some sku on the MBU virtual device.
    if ($backup_system) {
        $results = $db->SubmitQuery("
        SELECT s.computer_number,
             co.os,
             p.product_description
        FROM server s
            JOIN computer_os co on (co.computer_number = s.computer_number)
            JOIN server_parts sp on (s.computer_number = sp.computer_number)
            JOIN sku p on (sp.product_sku = p.product_sku)
        WHERE s.customer_number = $customer_number
            AND co.os ilike '%Managed%Backup%'
            AND s.status_number >= ".STATUS_SENT_CONTRACT."
            AND s.datacenter_number = ".$computer->GetDataCenterNumber()."
            AND s.computer_number IN (
                SELECT s.computer_number
                FROM server s
                    JOIN computer_os co on (co.computer_number = s.computer_number)
                    JOIN server_parts sp on (s.computer_number = sp.computer_number)
                    JOIN sku p on (sp.product_sku = p.product_sku)
                WHERE s.customer_number = $customer_number
                    AND co.os ilike '%Managed%Backup%'
                    AND s.status_number >= ".STATUS_SENT_CONTRACT."
                    AND s.datacenter_number = ".$computer->GetDataCenterNumber()."
                    AND p.product_name = 'Backup Server Software'
                    AND p.product_description ilike '" . $backup_system . "%'
                )
            ");
        $num = $results->numRows();
        //print out the server links and retention data
        if( !empty($num) ) {
            print "<tr><th>\n";
            print "Managed Backup:\n";
            print "</th><td colspan=\"2\">";
            print '<table>';
            for( $i=0; $i<$num; $i++ ) {
                $cn = $results->getCell($i,0);
                $cos = $results->getCell($i,1);
                $cpart = $results->getCell($i,2);
                print "\n<tr><td style=\"text-align: right\">";
                print "<a href=\"DAT_display_computer.php3?computer_number=$cn\">$cn</a>\n";
                print "</td><td>";
                print '<img src="';
                print getIconForOs( $cos );
                print '"> ';
                print "</td><td>";
                print "$cpart\n";
                print "</td></tr>\n";
            }
            print "</table></td></tr>\n";
        }
    }
}

//this displays a server with managed backup on the managed backup virtual device.
if ( $os == "Managed Backup" ) {
    //need to see which backup system the MB server is running.
    $results = $db->SubmitQuery("
        SELECT p.product_description
        FROM sku p
            JOIN server_parts sp on (p.product_sku = sp.product_sku)
        WHERE sp.computer_number = " . $computer->computer_number . "
            AND p.product_description ilike '% Server'
        ");
    //parse it out of the product name.  This is only really important for CommVault servers
    $backup_system = preg_replace("/(.*) Server/i", "$1", $results->getCell(0,0));

    //if no backup system is present, assume it is Legato
    if (!$backup_system) {
        $backup_system = "Legato";
    }

    //apply the backup_system to the query to get the specific systems with that MB Agent
    $results = $db->SubmitQuery("
    SELECT computer_number,
           (select os from computer_os where computer_number = server.computer_number) as os,
           (select product_description from sku where product_sku = server_parts.product_sku) as part
    FROM server join server_parts using (computer_number)
                join sku using (product_sku)
    WHERE product_description like 'Managed Backup Agent%'
      AND customer_number = $customer_number
      AND status_number >= ".STATUS_SENT_CONTRACT."
      AND datacenter_number = ".$computer->GetDataCenterNumber()."
    ");
    $num = $results->numRows();
    if( !empty($num) ) {
        print "<tr><th>\n";
        print "Servers using <br> Managed Backup:\n";
        print "</th><td colspan=\"2\">";
        print '<table>';
        for( $i=0; $i<$num; $i++ ) {
            $cn = $results->getCell($i,0);
            $cos = $results->getCell($i,1);
            $cpart = $results->getCell($i,2);
            print "\n<tr><td style=\"text-align: right\">";
            print "<a href=\"DAT_display_computer.php3?computer_number=$cn\">$cn</a>\n";
            print "</td><td>";
            print '<img src="';
            print getIconForOs( $cos );
            print '"> ';
            print "</td><td>";
            print "$cpart\n";
            print "</td></tr>\n";
        }
        print "</table></td></tr>\n";
    }
// Display firewalled or load-balanced servers
}
elseif ( $os == 'Custom Monitoring' ) {
    $monitored_list = $db->submitQuery("
        select
            sla_promise,
            name,
            points,
            server_xref_custom_monitor.description,
            notes,
            server_xref_custom_monitor_id
        from
            server_xref_custom_monitor
        join
            custom_monitor
        using
            (custom_monitor_id)
        where
            parent_monitor = $computer_number
        order by
            server_xref_custom_monitor.description;");
    $num_rows = $monitored_list->numRows();
    print "<tr><th>\n";
    print "<br> Custom Monitored<BR> Servers:\n";
    print "</th><td colspan=\"5\">";
    print '<table>';
        print "\n<tr><td style=\"text-align: right\" colspan=\"2\">";
    if ( $num_rows > 0 ) {
        print "<table>";
        $total_points_used = 0;
            print "<tr><th> Actions </th>";
            print "<th> Monitored<BR> Service </th>";
            print "<th> SLA<BR> Promise </th>";
            print "<th> Monitored URL </th>";
            print "<th> Match Text Notes </th></tr>";
        for($i=0; $i < $monitored_list->numRows(); $i++ ) {
            $sla_promise = $monitored_list->getResult($i, 'sla_promise');
            $monitor_name = $monitored_list->getResult($i, 'name');
            $monitor_points = $monitored_list->getResult($i, 'points');
            if ($monitor_points > 0) {
                $total_points_used += (int) $monitor_points;
            }
            $description = $monitored_list->getResult($i, 'description');
            $notes = $monitored_list->getResult($i, 'notes');
            $id = $monitored_list->getResult($i, 'server_xref_custom_monitor_id');
                print "<tr>";
                print "<td>";
                print "<a onclick=\"window.open('/server_monitor_view.php?id=".$id."','newwin','width=600,height=300,resizable,scrollbars'); return false;\" href=\"/server_monitor_view.php?id=".$id." \"target=\"_new\"><img src=\"/img/icon_view.gif\"></a>";
                print "<a onclick=\"window.open('/server_monitor_edit.php?id=".$id."','newwin','width=800,height=800,resizable,scrollbars'); return false;\" href=\"/server_monitor_edit.php?id=".$id." \"target=\"_new\"><img src=\"/img/icon_edit.gif\"></a>";
                print "<a onclick=\"window.open('/server_monitor_delete.php?id=".$id."','newwin','width=600,height=300,resizable,scrollbars'); return false;\" href=\"/server_monitor_delete.php?id=".$id." \"target=\"_new\"><img src=\"/img/icon_delete.gif\"></a>";
                print "</td><td>";
                print $monitor_name;
                print "</td><td>";
                if ($sla_promise == 't') {
                    print "Yes";
                } else {
                    print "&nbsp;";
                }
                print "</td><td>";
                print htmlentities(substr($description,0,30));
                print "<a onclick=\"window.open('/server_monitor_description_view.php?id=".$id."','newwin','width=600,height=300,resizable,scrollbars'); return false;\" href=\"/server_monitor_description_view.php?id=".$id." \"target=\"_new\"><img src=\"/img/icon_view.gif\"></a>";
                print "</td><td>";
                print "<a onclick=\"window.open('/server_monitor_note_view.php?id=".$id."','newwin','width=600,height=300,resizable,scrollbars'); return false;\" href=\"/server_monitor_note_view.php?id=".$id." \"target=\"_new\"><img src=\"/img/icon_view.gif\"></a>";
                #print htmlentities(substr($notes,0,20));
                print "</td></tr>\n";
        }
        print "<tr><th></th>";
        print "<th></th>";
        print "<th></th>";
        print "<th>$total_points_used</th>";
        print "<th></th>";
        print "</tr>";
        print "</table>";
    }
    print "</td></tr>";
    print "<tr><td>";
    print '<a href="/py/computer/computerMonitorAdd.pt?parent_monitor='.$computer_number.'&customer_number='.$customer_number.'">Add Monitoring Target'."</a>";
    print "</td>";
    print "<td align=\"right\">";
    $sla_promise_level = $db->submitQuery("
        select
            percentage
        from
            \"SRVR_SLAPromiseLevel\"
        where
            computer_number = $computer_number");
    print '<script type="text/javascript">';
    print ' function setSLAPromiseLevel(value) {';
    print "  window.location=\"DAT_display_computer.php3?computer_number=$computer_number&sla_promise_level=\" + value;";
    print ' }';
    print '</script>';
    $sla_promise_level = $sla_promise_level->GetCell(0, 0);
    print 'SLA Promise:<select name="overall_sla" id="overall_sla" onChange="setSLAPromiseLevel(this.value)">';
    print '<option value="100"';
    if ($sla_promise_level == 100) {
        print " selected";
    }
    print '>100%</option>';
    print '<option value="99.95"';
    if ($sla_promise_level == 99.95) {
        print " selected";
    }
    print '>99.95%</option>';
    print '<option value="99.9"';
    if ($sla_promise_level == 99.9) {
        print " selected";
    }
    print '>99.9%</option>';
    print '<option value="99"';
    if ($sla_promise_level == 99) {
        print " selected";
    }
    print '>99%</option>';
    print '<option value="0"';
    if ($sla_promise_level == 0 || $sla_promise_level == '') {
        print " selected";
    }
    print '>Not Participating</option>';
    print '</select>';
    print "</td></tr>";
    print "</table></td></tr>\n";
}
// Display firewalled or load-balanced servers
elseif ( $os == "PrevenTier" ) {
    $results = $db->SubmitQuery("
    SELECT computer_number,
           (select os from computer_os where computer_number = server.computer_number) as os,
           (select product_description from sku where product_sku = server_parts.product_sku) as part
    FROM server join server_parts using (computer_number)
    WHERE product_sku = 102151
      AND customer_number = $customer_number
      AND status_number >= ".STATUS_SENT_CONTRACT."
    ");
    $num = $results->numRows();
    if( !empty($num) ) {
        print "<tr><th>\n";
        print "Servers using <br> PrevenTier:\n";
        print "</th><td colspan=\"2\">";
        print '<table>';
        for( $i=0; $i<$num; $i++ ) {
            $cn = $results->getCell($i,0);
            $cos = $results->getCell($i,1);
            $cpart = $results->getCell($i,2);
            print "\n<tr><td style=\"text-align: right\">";
            print "<a href=\"DAT_display_computer.php3?computer_number=$cn\">$cn</a>\n";
            print "</td><td>";
            print '<img src="';
            print getIconForOs( $cos );
            print '"> ';
            print "</td><td>";
            print "PrevenTier Required\n";
            print "</td></tr>\n";
        }
        print '<tr><td style="text-align: left; color: black; background: #CCF" colspan="3">';
        print "Licenses: ";
        if( $i == 0 ) {
            print "no servers";
        } elseif( $i == 1 ) {
            print "1 server";
        } else {
            print "$i servers";
        }
        print " out of ";
        $c = $computer->availablePrevenTierLicenseCount();
        if( $c == 0 ) {
            print "no liceneses";
        } elseif( $c == 1 ) {
            print "1 license";
        } else {
            print "$c licenses";
        }
        print "</td></tr>";
        print "</table></td></tr>\n";
    }
}

// We now allow computers to be places behind other computers so this code is no longer firewall specific.
$connected_computers = $computer->getConnectedComputerNumbers();
if (sizeof($connected_computers) != 0) {
    if ($os == "Virtual Server Cluster") {
        print "<tr><th>Servers In Cluster:</th><td>\n";
    } else {
        print "<tr><th>Servers Behind Device:</th><td>\n";
    }

    foreach($connected_computers as $cnum) {
        print '<a href="/tools/DAT_display_computer.php3?computer_number='.$cnum.'">';
        print getImageForServer( $cnum );
        print " $cnum</a> ";
        if( hasPrevenTierRequired( $cnum ) ) {
            print " (";
            print strIMG( getIconForOS( 'PrevenTier' ) );
            print ")";
        }
        print "<BR>\n";
    }
    print "</td></tr>\n";
}
?>

<!-- Start Connected to Net Device -->
<?php
if ($computer->isBehindNetworkDevice()) {
?>
<tr>
    <th>Connected to Net Device:</th>
    <td>
    <?php
    $devices = $computer->getNetworkDeviceNumbers();
    foreach($devices as $dnum) {
        $dnum_img = getImageForServer($dnum);
        print "<a href=\"/tools/DAT_display_computer.php3?computer_number=$dnum\">$dnum_img $dnum</a><br>";
    }
    ?>
    </td>
</tr>
<?php
}
?>
<!-- End Connected to Net Device -->

<?//Begin  Failback Row ---------------------------------------------------- ?>

<? 

if($computer->canHaveFailback()) 
{
  // display Failback row if applicable to this device

  print "<tr><th>Failover Device:</th><td colspan = 3>";
  $dnum = $computer->hasFailback();	
  
  if($dnum)
  {	
    // if a failback is associated with this device display it along with  a 'modify' button 		
    print "<a href=\"/tools/DAT_display_computer.php3?computer_number=".$dnum."\">";
    print getImageForServer( $dnum );
    print " ".$dnum."</a>";
    print "<a href=\"javascript:makePopUpNamedWin('$py_app_prefix/computer/popupFailbackAssign.pt?computer_number=$computer_number',300,700,'',3,'AssignFailback$computer_number')\" class=\"text_button\">Edit Failover Assignment</a></td></tr>";
  }
  else
  {
    // if no failback is assigned display a button to the screen to make assignments
    print "<a href=\"javascript:makePopUpNamedWin('$py_app_prefix/computer/popupFailbackAssign.pt?computer_number=$computer_number',300,700,'',3,'AssignFailback$computer_number')\" class=\"text_button\">Assign Failover Device</a>";
  }

  print "</td></tr>";
}

?>

<?//End Failback Row ------------------------------------------------------ ?>



							</TABLE>
<!-- End Technical Info ---------------------------------------------------  -->
				</TD>
			</TR>
<? if ( $computer->isSQLSageDevice() ) { ?>
<!-- Begin SQL Sage Section -------------------------------------------------->
            <tr>
                <th class=blueman> SQL Sage MS SQL Servers: </th>
            </tr>
            <tr>
                <td colspan=2>
    			<TABLE WIDTH="100%"
    			       BORDER="0"
    			       CELLSPACING="2"
    			       CELLPADDING="2">
    			<TR>
    				<TD>
                    <? foreach($computer->getSQLSageDeviceNumbers()  as $cnum) {
                        print '<a href="/tools/DAT_display_computer.php3?computer_number='.$cnum.'">';
                        print getImageForServer( $cnum );
                        print " $cnum</a> ";
                        print "<BR>\n";
                    }
                    ?>
    				&nbsp; </TD>
    			</TR>
    			</TABLE>
                </td>
            </tr>
<? } ?>
<!-- End SQL Sage ---------------------------------------------------->


<?//Begin DNAS --------------------------------------------------------------- ?>
 
<?if ($computer->isDNAS()):?>
<tr><th class=blueman>Dedicated NAS</th></tr>
<tr><td>

<div align = right><table width="98%" BORDER="0" CELLSPACING="0" CELLPADDING="0">


<tr><th class=datatable>Storage</th></tr>
<tr><td><div id="dnas_storage_info"></div><script>retrieveDNASinfo(<?=$computer_number?>);</script></td></tr>


<?if (in_dept("NAS_ENGINEER")):?>
<tr><td><a href="javascript:makePopUpNamedWin('<?=$py_app_prefix?>/computer/popupDNASstorage.pt?computer_num=<?=$computer_number?>',300,600,'',3,'AddDnasStorage<?=$computer_number?>')" class="text_button">Add</a></td></tr><?endif;?>


</table></div>

</td></tr>

<?endif;?>

<?//End DNAS ----------------------------------------------------------------- ?>



<!-- Begin Virtualization ------------------------------------------->

<? if ($computer->isVirtualMachine() || $computer->isHypervisor()) { ?>

<TR><th class=blueman> Virtualization </th></TR>

<tr> 
   <td> 
   <table width="100%" border="0" cellspacing="2" cellpadding="2">

<? 

if ($computer->isVirtualMachine()) { 


$discoverlnk = $GLOBALS['pylons_url'].'/virt/vmactions/getVmInfo/'.$computer_number

?>
<tr><td colspan=2 align=center><div id="getvccinfo"><a class='text_button' href="#" onclick="retrieveVmInfo('<?=$discoverlnk?>');return false;";>retrieve virtual center information</a></div></td></tr>
<? } ?>

<? if (($computer->isVirtualMachine() && $computer->getHypervisorNumber() != 0) || $computer->isHypervisor()) { ?>
<TR>
<th>UUID:</th><td nowrap> 
<? 
$uuid = $computer->getUuid();
if (is_null($uuid)) 
   { 
     echo "<b>CORE Error Communicating with Virtualization Service</b>"; 
   } 
   else if ($uuid == '')
   {
     echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupVccUuid.pt?computer_number=$computer_number', 210, 460, '', 3, 'AssignUuid')\">Set</a>"; 
   } 
   else 
   { 
     echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupVccUuid.pt?computer_number=$computer_number', 210, 460, '', 3, 'Edit Uuid')\">Edit</a>&nbsp;$uuid"; 
   } 
?></td></tr> <? } 
if ($computer->isVirtualMachine()) { ?>

<TR><th> HyperVisor: </th><td nowrap> 
<? 
list($status_code, $vm) = $computer->getVMInfo(); 
if ($status_code == 404 || $vm == NULL) 
{ echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupSelectHyperVisor.pt?computer_number=$computer_number', 450, 450, '', 3, 'AddHV')\">Set</a>"; } 
else if ($status_code == 200) 
{ 
  $hv = $vm['hypervisor']; 
  if ($hv != NULL) 
  { ?><? echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupSelectHyperVisor.pt?computer_number=$computer_number', 450, 450, '', 3, 'AddHV')\">Edit</a>&nbsp;" ?><img src='/img/minicon/product/tree_hypervisor.gif'/><a href="/tools/DAT_display_computer.php3?computer_number=<?=$hv['computer_number']?>"><?= $hv['computer_number']?>-<?= $hv['name'] ?></a><? 
  }
}

  else { 
     echo "<b>CORE Error Communicating with Virtualization Service</b>"; 
   } 
   ?>
</td></tr> <? } 
  else if ($computer->isHypervisor() and !$computer->isCustomerActiveHypervisor()) 
  { 
    list($status_code, $hv) = $computer->getHVInfo(); ?> 
    <TR><th> Virtual Console: </th><td nowrap>
    <?  //if hypervisor exists, and it has a VCC, list that.

    if ($status_code == 200 and $hv['vcc'] != Null) 
    { echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupSelectVCC.pt?computer_number=$computer_number', 450, 450, '', 3, 'SetVCC')\">Edit</a>&nbsp;" ?><?= $hv['vcc']['name'] ?><i><?= $hv['vcc']['ip'] ?></i> <? }
    else 
    { echo "<a class='text_button' onclick=\"makePopUpNamedWin('/py/computer/popupSelectVCC.pt?computer_number=$computer_number', 450, 450, '', 3, 'SetVCC')\">Set</a>"; } ?>
 
</td>
</tr>
                <TR>
                    <th> Virtual Machines: </th>
                    <td nowrap>
                                <?
                                        if (sizeof($hv['vms']) > 0) {
                                                foreach ($hv['vms'] as $vm) {
                                                        ?><a href="/tools/DAT_display_computer.php3?computer_number=<?=$vm['computer_number']?>"><?= $vm['computer_number']?>-<?= $vm['name'] ?></a><br/><?
                                                }
                                        }
                                        else {
                                                ?> No Virtual Machines Assigned <?
                                        }
                                ?>
                        </td>
                </tr>
                <?
        }
?>
</table>

</td>
</tr>
<? } ?>

<!-- End Virtualization --------------------------------------------->

<!-- Begin Inventory ---------------------------------------------------------->
			<TR>
				<th class=blueman> Computer Inventory </th>
			</TR>
			<TR>
			    <TD COLSPAN=2 ALIGN="CENTER" valign="top">
							<TABLE WIDTH="100%"
							       BORDER="0"
							       CELLSPACING="2"
							       CELLPADDING="2">
                                <?if ($expedite == 't'):?>
                                <TR>
                                    <td colspan=2><FONT COLOR=#FF0000> *EXPEDITE*</FONT></td>
                                </TR>
                                <?endif;?>
                    			<?if ($computer->getData("pre_build_message")!=""):?>
                				<TR>
                					<td colspan=2><span style="color: red">Pre-Production Instructions (Hardware):</span><br>
                					<p style="border: thin red solid; padding: 0.5ex 0.4ex"><?print($computer->getData("pre_build_message"));?></p></TD>
                				</TR>
            				    <?endif;?>
            				    <?if ($computer->getData("completion_message")!=""):?>
            				    <TR>
            					    <TD colspan="2"> Support Instructions (Software): <br>
            					    <p style="border: thin black solid;padding: 0.5ex 0.4ex">
                                    <?print($computer->getData("completion_message"));?></p></TD>
            				    </TR>
            				    <?endif;?>

        					<TR>
								<TD WIDTH=70% valign="top">
<?//Begin Inventory --------------------------------------------------------- ?>
								<? print $computer->InventoryList(); ?></TD>
<?//End Inventory ----------------------------------------------------------- ?>
								
							</TR>
							</TABLE>
<!-- End Inventory ------------------------------------------------------------>
                            </td>
                    </tr>
<!-- Begin Customer Comments -------------------------------------------------->
            <tr>
                <th class=blueman> Customer Comments: </th>
            </tr>
            <tr>
                <td colspan=2>
    			<TABLE WIDTH="100%"
    			       BORDER="0"
    			       CELLSPACING="2"
    			       CELLPADDING="2">
    			<TR>
    				<TD>
    				<?if (empty($short)):?>
    				<?print(return_formatted($computer->getData("comments")));?>
    				<?endif;?>
    				&nbsp; </TD>
    			</TR>
    			</TABLE>
                </td>
            </tr>
<!-- End Customer Comments ---------------------------------------------------->
<!-- Begin Staff Comments -->
            <tr>
                <th class=blueman> Rackspace Comments </th>
            </tr>
            <tr>
                <td colspan=2>
                    <TABLE class="datatable">
        			<TR>
        				<TD>
        					<?if ($db->TestExist("select computer_number from offline_servers where computer_number=$computer_number and customer_number=$customer_number;")):?>
        					<TABLE class=blueman style="width: 100%;">
        					<?
        						//This computer was taken offline at some point - show when - and why
        						$info=$db->SubmitQuery("select date(sec_offline::abstime) as sec_offline,w2.reason_category,comments from offline_servers w1, offline_reasons w2 where computer_number=$computer_number and customer_number=$customer_number and w1.reason_number=w2.reason_number;");

        						print("<TR>");
        						print("<TD><FONT COLOR=#FF0000> Offline Date ".$db->GetResult($info,0,"sec_offline")."</FONT>.&nbsp; - &nbsp;");
        						print($db->GetResult($info,0,"reason_category")."&nbsp; &nbsp;");
        						print(return_formatted($db->GetResult($info,0,"comments")));
        						print("</TD></TR>");

        					?>
        					</TABLE>
        					<?endif;?>
                                       <TABLE class="datatable">
        					<?if (empty($short)):?>
        					<?if (isset($full_log) and $full_log == 1):?>
        					<TR>
        						<TD VALIGN=TOP>
                                            <A TARGET=content
                                               HREF="DAT_display_computer.php3?computer_number=<?print($computer_number);?>&customer_number=<?print($customer_number);?>&full_log=0"
                                               class="text_button">
        						Hide All</A></TD>
        						<TD><? display_log_reverse($conn,"computer_log","comments","customer_number=$customer_number and computer_number=$computer_number");?></TD>
        					</TR>
        					<?else:?>
        					<TR>
        						<TD VALIGN=TOP>
                                            <A TARGET=content
                                               HREF="DAT_display_computer.php3?computer_number=<?print($computer_number);?>&customer_number=<?print($customer_number);?>&full_log=1"
                                               class="text_button">
        						Show All </A></TD>
        						<TD><? print_last_entry($conn,"computer_log","comments","customer_number=$customer_number and computer_number=$computer_number");?></TD>
        					</TR>
        					<?endif;?>
        					</TABLE>
        					<?endif;?>
        				</TD>
        			</TR>
        			</TABLE>
		        </TD>
        	</TR>
<!-- End Staff Comments ---------------------------------------------------- -->
	</TABLE>
<!-- End Computer Details -------------------------------------------------- -->
<!-- Begin Tickets ----------------------------------------------------------->
<?
$args = "computer_number=" . $computer_number;
?>
<div id="tickets">Loading Tickets...</div>

<script>
loaddiv("/py/ticket/computerHistory.pt?<? echo $args ?>", "tickets");
setTimeout("dynamicMenuPositioning()", 3500);
</script>
<!-- End Tickets ------------------------------------------------------------->

<!-- Begin Computer Tools & Status Upgrade -->
<? require_once "DAT_display_computer_tools.php"; ?>
<!-- End Computer Tools & Status Upgrade -->

<?= page_stop() ?>
</HTML>
