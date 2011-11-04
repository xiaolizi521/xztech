<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

$report_db = getReportDB();
$now = time();

if (empty($limiting_date_field)) {
    // substitued in query. could also be sec_due_offline
    $limiting_date_field = 'sec_created';
}

if( empty($COMMAND) ) {
    $COMMAND = "";
}

$stamp=time();
$curr_year =date("Y",$stamp);
$curr_month=date("m",$stamp);
$curr_mday =date("d",$stamp);

function getRemainingServers($customer_number, $sec_due_offline) {
    global $report_db;
    $remaining_query = "
        SELECT count(computer_number)
        FROM server t1
        WHERE customer_number=$customer_number
            AND status_number >= " . STATUS_ONLINE . "
            AND computer_number NOT IN
                (SELECT computer_number
                    FROM queue_cancel_server sub1
                    WHERE t1.computer_number = sub1.computer_number
                    AND sub1.sec_due_offline <= $sec_due_offline
                    AND sub1.completed = 'f'
                )
        ";

    return $report_db->GetVal($remaining_query);
}

if( $COMMAND == "EXCELCOMMENTS" ) {
    $comment_select = ",\n    o.reason_info, o.competitor_benefits";
} else {
    $comment_select = "";
}

$lost_server_query = <<< SQL
SELECT MAX(o.sec_created),
    o.computer_number,
    s.customer_number,
    o.sec_due_offline,
    DATE(o.sec_due_offline::abstime),
    DATE(o.sec_created::abstime) as submitted_date,
    r.reason_category,
    category_group_name,
    s.sec_finished_order as sec_contract_received,
    DATE(s.sec_finished_order::abstime) AS online_date,
    server.final_monthly,
    server.final_setup,

     determine_os(computer_number) AS os
     $comment_select
FROM queue_cancel_server o
    JOIN sales_speed s 
        USING (computer_number)
    JOIN server 
        USING (computer_number)

    LEFT JOIN offline_reasons r 
        USING (reason_number)
    LEFT JOIN offline_reason_groups 
        USING (category_group)


WHERE o.$limiting_date_field >= '%s'
    AND o.$limiting_date_field <= '%s'
    AND (o.completed = 'f' or server.status_number < 0)
    AND s.sec_finished_order > 0
GROUP BY
    o.computer_number,
    s.customer_number,
    o.sec_due_offline,
    o.sec_due_offline,
    o.sec_created,
    r.reason_category,
    category_group_name,
    sec_contract_received,
    s.sec_finished_order,
    os,
    server.final_monthly,
    server.final_setup
    $comment_select
ORDER BY o.$limiting_date_field DESC, customer_number, computer_number
SQL;

if( $COMMAND=="EXCEL" or $COMMAND == "EXCELCOMMENTS" ) {
    Header("Pragma:");
    Header("Content-type: application/vnd.ms-excel name='Lost_Servers'");
    if ($limiting_date_field == 'sec_created') {
        Header("Content-Disposition: inline; "
               . "filename=report_platform_cancellation_submitted.xls");
    }
    else {
        Header("Content-Disposition: inline; "
               . "filename=report_platform_due_offline.xls");
    }
    Header("Content-Description: Admin Tool Generated Data");
    flush(1);

    if( !empty($mday) ) {
        $stamp_start = mktime(0,0,0,$month,$mday,$year);
        $stamp_end   = mktime(23,59,59,$month,$mday,$year);
        if( $year == $curr_year and
            $month == $curr_month and
            $mday == $curr_mday ) {
            $color = $bkcolors[1];
            $fluxwarn = true;
        } else { 
            $color = $bkcolors[0];
        }
    } else {
        if( empty($year) ) {
            $year = $curr_year;
        }
        if( empty($month) ) {
            $month = $curr_month;
        }
        for( $last_day = 31 ; $last_day >= 28 ; $last_day-- ) {
            if( checkdate($month,$last_day,$year) ) {
                break;
            }
        }
        $stamp_start = mktime(0,0,0,$month,1,$year);
        $stamp_end   = mktime(23,59,59,$month,$last_day,$year);
    }
    
    $query = sprintf($lost_server_query, $stamp_start, $stamp_end);
    // Create temp_lost_server_list table.
    $lost_servers_list = $report_db->SubmitQuery( $query );
    $lost_servers = $report_db->NumRows($lost_servers_list);

    //print column headers
    $headers = array( "Due Offline Date",
                      "Submitted Date",
                      "Cust#",
                      "Comp#",
                      "Name",
                      "Company",
                      "Account Manager",
                      "Reason Group",
                      "Offline Reason",
                      "Platform",
                      "Data Center",
                      "Monthly Fee",
                      "Setup Fee",
                      "Remaining Servers",
                      "Online Date",
                      "Tenure",
                      "Division",
                      "Support Team" );
    if( $COMMAND == "EXCELCOMMENTS" ) {
        array_push($headers, "Competitor Benefits" );
        array_push($headers, "Comments");
    }
    
    print '"' . join( "\"\t\"", $headers ) . "\"\n";

    $i_account = ActFactory::getIAccount();

    for( $row = 0; $row < $lost_servers ; $row++ ) {
            $array = $report_db->FetchArray( $lost_servers_list, $row );
            $computer=new RackComputer;
            $computer->Init($array['customer_number'],$array['computer_number'],$report_db);
            echo "\"$array[date]\"\t";
            echo "\"$array[submitted_date]\"\t";
            echo "\"$array[customer_number]\"\t";
            echo "\"$array[computer_number]\"\t";

            $primaryContact = $computer->account->getPrimaryContact();
            $fullname = $primaryContact->individual->getFullName();        
            if( $fullname == " " ) {
                $fullname = "\"No Name in DB\"\t";
            }
            echo "\"$fullname\"\t";
            $company_name = $primaryContact->primaryCompanyName;        
            echo "\"$company_name\"\t";
            //removing this because this report is going away anyway. $account_manager = $i_account->getAccountManager($report_db, $array['customer_number']);        
            echo "\"$account_manager\"\t";
            echo "\"".$array['category_group_name']."\"\t";
            echo "\"".$array['reason_category']."\"\t";
            echo "\"$array[os]\"\t";
            $datacenter = $computer->GetDataCenter();
            echo "\"$datacenter\"\t";
            echo "\"$array[final_monthly]\"\t";	
            echo "\"$array[final_setup]\"\t";	
            $customer_number=$array['customer_number'];
            $total_servers = getRemainingServers($customer_number, 
                                                 $array['sec_due_offline']);
            
            echo "\"$total_servers\"\t";
            echo "\"$array[online_date]\"\t";
            $tenure = 0;
            $tenure= round(($array['sec_due_offline'] - $array['sec_contract_received']) / 2592000,1);   //(Date Offline- Date Online/seconds in 30 days
            echo "\"$tenure\"\t";         
            $division = $computer->account->segment_name;        
            echo "\"$division\"\t";
            $support_team = $computer->account->getSupportTeamName();        
            echo "\"$support_team\"";
            if( $COMMAND == "EXCELCOMMENTS" ) {
                $comment = $array['reason_info'];
                $comment = preg_replace('/[\n\r]/'," ",$comment);
                $comment = preg_replace('/"/',"''",$comment);
                $cbeni = $array['competitor_benefits'];
                $cbeni = preg_replace('/[\n\r]/'," ",$cbeni);
                $cbeni = preg_replace('/"/',"''",$cbeni);
                echo "\t\"$cbeni\"\t\"$comment\"";
            }
            echo "\n";
        }
    exit;
}
?>
<? if ($COMMAND==""):?>
<HTML id="mainbody">
   <head>
   <TITLE>CORE: Lost Server Stats</TITLE>
<?require_once("tools_body.php");?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function clearform(which) {
   if (which.value!="") {
      which.value='';
   }
}
//-->
</SCRIPT>
<?include("form_wrap_begin.php")?>
<FORM ACTION="report_lost_servers.php3" METHOD="post">
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Report Lost Servers </TD>
         		</TR>
               <TR>
                  <TD> 
<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2"
       VALIGN="TOP">
<TR>
   <TH COLSPAN=4 ALIGN=LEFT> Select Options: </TH>
</TR>
<TR>
	 <TH> Month: </TH> 
    <TD>
      <INPUT TYPE="text"
        NAME="month"
        VALUE="<?print $curr_month?>"
        SIZE="3"
        MAXLENGTH="2"
        onFocus="clearform(this)"></TD>
      <TH> Year: </TH> 
      <TD>
      <INPUT TYPE="text"
        NAME="year"
        VALUE="<?print $curr_year?>"
        SIZE="5"
        MAXLENGTH="4"
        onFocus="clearform(this)"> </TD>
</TR>
<TR>        
   <TH> Format: </TH>
   <TD COLSPAN=3>
      <SELECT NAME="COMMAND">
	      <OPTION VALUE="VIEW_REPORT"> HTML </OPTION>
	      <OPTION VALUE="EXCEL"> Excel </OPTION>
	      <OPTION VALUE="EXCELCOMMENTS"> Excel with Comments </OPTION>
      </SELECT></TD>
</TR>
<TR>  
    <TH> Limit servers by </TH>
    <TD COLSPAN="3">
      <SELECT NAME="limiting_date_field">
         <OPTION VALUE="sec_created"> Date of Cancellation Request Submission </OPTION>
         <OPTION VALUE="sec_due_offline"> Due Offline Date </OPTION>
      </SELECT></TD>
</TR>      
<TR>
   <TD COLSPAN=4 ALIGN=CENTER> <INPUT TYPE="submit" VALUE="GENERATE REPORT"> </TD> 
</TR>
</TABLE>

<?include("form_wrap_end.php")?>

<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
</FORM>
<font color=red size="+1">
Warning: This report only includes data from before midnight.
</font>
<?endif;?>
<? if ($COMMAND=="VIEW_REPORT"):?>
<?
require_once("tools_body.php");
$stamp=time();
$curr_year =date("Y",$stamp);
$curr_month=date("m",$stamp);
$curr_mday =date("d",$stamp);

if ( empty($month) ) {
  $month = $curr_month;
}

if ( empty($year) ) {
  $year = $curr_year;
}

if ( empty($mday) ) {
  $mday = 0;
  $mdayget = "";
  $datestring = "$month-$year";
} else {
  $mdayget = "&mday=$mday";
  $datestring = "$mday-$month-$year";
}

// new Stuff

$bkcolors = array ( '#999999', '#e6e6e6' );
$colors   = array ( '#FFFFFF', '#E6E6E6' );

if( !empty($mday) ) {
  $stamp_start = mktime(0,0,0,$month,$mday,$year);
  $stamp_end   = mktime(23,59,59,$month,$mday,$year);
  if( $year == $curr_year and
      $month == $curr_month and
      $mday == $curr_mday ) {
    $color = $bkcolors[1];
    $fluxwarn = true;
  } else { 
    $color = $bkcolors[0];
  }
} else {
  for( $last_day = 31 ; $last_day >= 28 ; $last_day-- ) {
    if( checkdate($month,$last_day,$year) ) {
      break;
    }
  }
  $stamp_start = mktime(0,0,0,$month,1,$year);
  $stamp_end   = mktime(23,59,59,$month,$last_day,$year);

  if( $year == $curr_year and
      $month == $curr_month ) {
    $color = $bkcolors[1];
    $fluxwarn = true;
  } else { 
    $color = $bkcolors[0];
  }
}

$baseurl = "report_lost_servers.php3";

/* Previous Day/Month */
$thisstamp = $stamp_start - 60 * 60;
$prevurl = $baseurl .
"?month=" . date("m", $thisstamp) .
"&year="  . date("Y", $thisstamp) .
"&COMMAND=VIEW_REPORT&limiting_date_field=$limiting_date_field";
if( !empty($mday) ) {
  $prevurl .= "&mday="  . date("d", $thisstamp);
}

/* Next Day/Month */
$thisstamp = $stamp_end + 60 * 60;
$nexturl = $baseurl .
"?month=" . date("m", $thisstamp) .
"&year="  . date("Y", $thisstamp) .
"&COMMAND=VIEW_REPORT&limiting_date_field=$limiting_date_field";
if( !empty($mday) ) {
  $nexturl .= "&mday="  . date("d", $thisstamp);
}

/* Create the links */
$otherlink = "";
$otherlink .= "[ <a href='$prevurl'><-- Prev</a> ] ";
if( !empty($mday) ) {
  $otherlink .= "[ <a href='$baseurl?COMMAND=VIEW_REPORT&month=$month&year=$year'>Month View</a> ] ";
} else {
  $otherlink .= "";
}
if( !($year == $curr_year and
      $month == $curr_month and
      ( empty($mday) or $mday == $curr_mday )) ) {
  $otherlink .= "[ <a href='$nexturl'>Next --></a> ] ";
}

?>

<HTML>
   <TITLE>REPORT: Lost Servers For <?php echo $datestring;?> 
<?
if ($limiting_date_field == 'sec_created') {
    print " by Cancellation Submission Date";
}
else {
    print " by Due Offline Date";
}

?>
</TITLE>
<?include("tools_body.php");?>
<?include("wait_window_layer.php")?>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>

<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> 
                  Lost Servers For <?php echo $datestring; ?>  </TD>
         		</TR>
               <TR>
                  <TD> 

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2"
       VALIGN="TOP">
<TR>
   <TD><BR><center><?php echo $otherlink; ?></center><BR></TD>
</TR>
<TR>
   <TD>
<?php
if ($rack_test_system or in_dept('CORE')) {
    print "TEST INFO: start:$stamp_start end:$stamp_end<br>\n";
}
$query = sprintf($lost_server_query, $stamp_start, $stamp_end);

// Create temp_lost_server_list table.
$lost_servers_list = $report_db->SubmitQuery( $query );
$lost_servers = $report_db->NumRows($lost_servers_list);

  /* Print tables */
echo "<TABLE BGCOLOR='$color' BORDER='0' CELLPADDING='2'>\n",
  "<TR>",
  "<TD ALIGN='center' colspan='2'>",
  "<center>Lost servers this ",
  (empty($mday))?"month":"day",
  ": $lost_servers</center>";
if( !empty($fluxwarn) ) {
  echo "<small>Note: This data is still in flux</small>";
}
echo "</TD></TR>",
  "<TR VALIGN='top'><TD>\n";

/* Print Lost Server Table */
echo "<TABLE CELLSPACING='1' CELLPADDING='2' BORDER=0>";
echo "<TR BGCOLOR='#E6E6E6'>",
  "<TH> Due Offline Date </TH>",
  "<TH> Submitted Date </TH>",
  "<TH> Cust# </TH>",
  "<TH> Comp# </TH>",
  "<TH> Name </TH>",
  "<TH> Company </TH>",
  "<TH> Account Manager </TH>",
  "<TH> Reason Group </TH>",
  "<TH> Offline Reason </TH>",
  "<TH> Platform </TH>",
  "<TH> Data Center </TH>",
  "<TH> Monthly Fee </TH>",
  "<TH> Setup Fee </TH>",
  "<TH> Remaining Servers </TH>",
  "<TH> Online Date </TH>",
  "<TH> Tenure </TH>",
  "<TH> Division </TH>",
  "<TH> Support Team </TH>",
  "</TR>\n";

$i_account = ActFactory::getIAccount();
$last_mday = 0;
for( $row = 0; $row < $lost_servers ; $row++ ) {
  $array = $report_db->FetchArray( $lost_servers_list, $row );

  $color = $colors[$row % 2 ];
  echo "<TR BGCOLOR='$color'>";
  echo "<TD ALIGN='center'>";
    $temp_mday = substr($array['date'], 8);
    if( $temp_mday != $last_mday ) {
        echo "<A href='sales_lost_breakout.php?",
        "year=$year&month=$month&mday=$temp_mday'>",
        "$array[date]</a>";
        $last_mday = $temp_mday;
    } else {
        echo "<small>$array[date]</small>";
    }
  echo "</TD>\n";
  echo "<TD>$array[submitted_date]</TD>";
    echo "<TD>",
    "$array[customer_number]",
    "</TD>";
    echo "<TD>",
    "<A href='display_computer.php3?",
    "customer_number=$array[customer_number]",
    "&computer_number=$array[computer_number]",
    "'>",
    "$array[computer_number]",
    "</a></TD>";
   
    $account = $i_account->getAccountByAccountNumber($report_db, $array["customer_number"]);
    $primaryContact = $account->getPrimaryContact();     
    $fullname = $primaryContact->individual->getFullName();
    
  if( $fullname == " " ) {
    $fullname = "<em>No Name in DB</em>";
  }
  echo "<TD>",
    "<A href='display_customer.php3?",
    "customer_number=$array[customer_number]",
    "'>",
    $fullname,
    "</a></TD>";
    $company_name = $primaryContact->primaryCompanyName; 
  echo "<TD>$company_name</TD>\n";
    $account_manager = $account->getAccountExecutive(); 
    echo "<TD>" . $account_manager->getFullName() . "</TD>\n";
  echo "<TD>$array[category_group_name]</TD>\n";
  echo "<TD>",
    "<em><A href=display_cancel_queue.php3?",
	"computer_number=$array[computer_number]>$array[reason_category]</A></em>",
    "</TD>";
  	$computer=new RackComputer;
	$computer->Init($array['customer_number'],$array['computer_number'],$report_db);
	$os=$computer->OS();
	$datacenter=$computer->GetDataCenter();
	$final_monthly=GetMoneyAsInt($computer->getData("final_monthly"));
	$final_setup=GetMoneyAsInt($computer->getData("final_setup"));
  echo "<TD>",
    "$os",
    "</TD>";
  echo "<TD>$datacenter</TD>";
  echo "<TD>",
    "$final_monthly",
    "</TD>";	
  echo "<TD>",
    "$final_setup",
    "</TD>";	
	
	$customer_number=$array['customer_number'];
	$total_servers = getRemainingServers($customer_number, 
        $array['sec_due_offline']);
	
  echo "<TD>",
    "$total_servers",
    "</TD>";
  echo "<TD>",
    $array['online_date'],
    "</TD>";	
	$tenure= round(($array['sec_due_offline'] - $array['sec_contract_received']) / 2592000,1);   //(Date Offline- Date Online/seconds in 30 days
  echo "<TD>",
    "$tenure",
    "</TD>";	
    $division = $computer->account->segment_name; 
    echo "<TD>$division</TD>\n";
    $support_team = $computer->account->getSupportTeamName(); 
    echo "<TD>$support_team</TD>\n";
  echo "</TR>\n";
}
print "</TABLE>";


/* Clean up */
echo "</TD></TR></TABLE>\n";

?>
</TD></TR>
<TR>
   <TD><BR><center><?php echo $otherlink; ?></center><BR></TD>
</TR>
<TR ALIGN="center" VALIGN="top">
	<TD>
    <?
    $link = "<A HREF=\"report_lost_servers.php3?COMMAND=EXCEL\"";
    if (isset($month)) { $link .= "&month=$month"; }
    if (isset($day)) { $link .= "&day=$day"; }
    if (isset($year)) { $link .= "&year=$year"; }
    if (isset($limiting_date_field)) { $link .= "&limiting_date_field=$limiting_date_field"; }
    $link .= '\">';
    print $link;
    ?>
    <IMG SRC="assets/images/i-msexcel-32.gif" 
        ALT="Export to Excel" BORDER="0"></a>
   ( 
    <?
    $link = "<A HREF=\"report_lost_servers.php3?COMMAND=EXCELCOMMENTS\"";
    if (isset($month)) { $link .= "&month=$month"; }
    if (isset($day)) { $link .= "&day=$day"; }
    if (isset($year)) { $link .= "&year=$year"; }
    if (isset($limiting_date_field)) { $link .= "&limiting_date_field=$limiting_date_field"; }
    $link .= '\">';
    print $link;
    ?>
    Excel with Comments</a> )
    </FORM></TD>
</TR>
</TABLE><BR CLEAR="ALL">

Current as of: <?php echo gmdate( "Y M d H:i" ); ?> GMT.<BR>
<?
$elapsed_time = time() - $now;
print "Elapsed time: ${elapsed_time}s\n";
?>

<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>

<?endif;?>

<?= page_stop() ?>
</HTML>
