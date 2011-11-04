<?require_once("CORE_app.php");
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
//if( !in_dept("CORE") && in_dept("MONITORING") && ( !isset( $emergency_visit ) ) ) {
    //header( 'Location: http://core.jaywhitebox/py/computer/displayEmergencyInstructions.pt?computer_number=' . $computer_number );
    //header( 'Location: /py/computer/displayEmergencyInstructions.pt?computer_number=' . $computer_number );
    //exit();
//}

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
    $support_blurb = '<img src="/img/minicon/service_sys_man.gif"/> Fanatical Support Plus';
} else {
    $support_blurb = "Fanatical Support";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        <? print("Computer $customer_number-$computer_number");?>
    </TITLE>
    <script language="JavaScript" src="/script/date-picker.js"></script>
    <script language="JavaScript" src="/script/vm_functions.js"></script>
    <script language="JavaScript" src="/script/dnas_functions.js"></script>
    <script language="JavaScript1.2"
            src="/script/popup.js"
            type="text/javascript"></script>
<?php
$no_menu = true;
require_once("tools_body.php");
?>

<!-- Begin Computer Tools & Status -->
<? include_once "DAT_display_computer_tools.php"; ?>
<!-- End Computer Tools & Status -->
