<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Monthly Account Sales Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Monthly Account Sales Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?php
require_once("tools_body.php");
include("wait_window_layer.php");
$report_db = getReportDB();
?>
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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Monthly Account Sales Stats
                  &nbsp; &nbsp; &nbsp; <?=strftime("%m/%d/%Y")?> </TD>
         		</TR>
               <TR>
                  <TD> 
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<?php
if( empty($REPORTDB_IS_AVAILABLE) ) {
    echo '<p style="font-weight: bolde">All data is current as of now.</p>';
} else {
    echo '<p style="font-weight: bold">All data is current as of yesterday night.</p>';
}
?>

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE class=datatable>
<TR> 
    <th>&nbsp;</th>
	<TH> Date </TH>
	<TH> Start Total </TH>
	<TH> # New Accounts </TH>
	<TH> # Lost Accounts </TH>
	<TH> Final Total </TH>
</TR>
<?
	$tot_new_customers=0;
	$tot_lost_customers=0;
	$tot_final_total=0;
	$stamp=time();
	$curr_month=date("m",$stamp);
	$curr_year=date("Y",$stamp);
	$ctr=0;
	for ($year=$curr_year;$year>$curr_year-1;$year--)
	{
		if ($year==$curr_year)
				$start=$curr_month;
		else
				$start=12;
		for ($month=$start;$month>0;$month--)
		{
?>
<?

	if (checkdate($month,31,$year))
			$last_day=31;
	else if (checkdate($month,30,$year))
			$last_day=30;
	else if (checkdate($month,29,$year))
			$last_day=29;
	else if (checkdate($month,28,$year))
			$last_day=28;
	$begin_mark = mktime(0,0,0,$month,1,$year); 
	$end_mark = mktime(0,0,0, $month, $last_day, $year) + 24 * 3600;
?>
<?
    if ($ctr%2==0)
        print("<TR class=even>\n");
    else
        print("<TR class=odd>\n");
//Find total to date
	$pre_totalq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number  
        FROM sales_speed t1, server inv
        WHERE sec_finished_order < $begin_mark
            AND sec_finished_order>0
            AND t1.computer_number = inv.computer_number
        ");
	$pre_total=$pre_totalq->numRows();
	$pre_totalq->freeResult();
	$new_customersq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number  
        FROM sales_speed t1, server inv
        WHERE sec_finished_order >= $begin_mark
            AND sec_finished_order < $end_mark
            AND t1.computer_number = inv.computer_number
            AND inv.customer_number not in (
                SELECT DISTINCT sub2.customer_number 
                FROM sales_speed sub1, server sub2 
                WHERE t1.customer_number = sub2.customer_number
                    AND sub1.computer_number = sub2.computer_number
                    AND sec_finished_order <= $begin_mark
                    AND sec_finished_order>0

                )
        ");
	$new_customers=$new_customersq->numRows();
	$new_customersq->freeResult();
	$change_lost_customersq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number 
        FROM offline_servers t1, server inv
        WHERE sec_offline >= $begin_mark 
            AND sec_offline < $end_mark
            AND t1.computer_number = inv.computer_number
            AND inv.customer_number not in (
                SELECT sub2.customer_number 
                FROM server sub2
                WHERE t1.customer_number = sub2.customer_number
                    AND status_number >= 12
                )
        ");
	$change_lost_customers=$change_lost_customersq->numRows();
	$change_lost_customersq->freeResult();
	$lost_customersq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number 
        FROM offline_servers t1, server inv
        WHERE sec_offline < $end_mark 
            AND t1.computer_number = inv.computer_number
            AND inv.customer_number not in
                (
                SELECT DISTINCT sub2.customer_number 
                FROM server sub2
                WHERE t1.customer_number = sub2.customer_number
                    AND status_number >= 12
                )
        ");
	$lost_customers=$lost_customersq->numRows();
	$lost_customersq->freeResult();
	$final_total=$pre_total+$new_customers-$lost_customers;
	$tot_new_customers+=$new_customers;
	$tot_lost_customers+=$lost_customers;
	$start_customers=$pre_total;
	print "\t<Td ALIGN=LEFT><A HREF=\"monthly_customer_breakdown.php3?month=$month&year=$year\">";
   print "<IMG SRC='/images/button_nav_next_tiny.gif'
               ALT='Go'
               WIDTH='13'
               HEIGHT='13'
               BORDER='0'></A></td><td class=counter>$month/$year</TH>\n";
   print("\t<TD ALIGN=right> $pre_total </TD>\n");
   print("\t<TD ALIGN=right> $new_customers </TD>\n");
   print("\t<TD ALIGN=right> $lost_customers ($change_lost_customers) </TD>\n");
   print("\t<TD ALIGN=right> $final_total </TD>\n");
?>
</TR>
<?
			$ctr++;
		}
	}
?>
<TR>
	<TH> &nbsp;</TH>
	<TH> &nbsp; </TH>
    <th>&nbsp;</th>
	<TH> # New Accounts </TH>
	<TH> # Lost Accounts </TH>
	<TH> Final Total </TH>
</TR>
<TR>
<?
	$tot_lost_customersq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number 
        FROM sales_speed t1, server inv
        WHERE sec_finished_order>0 
            AND t1.computer_number = inv.computer_number
            AND inv.customer_number not in (
                SELECT DISTINCT sub2.customer_number
                FROM server sub2
                    WHERE t1.customer_number = sub2.customer_number
                        AND status_number >= 12
                )
        ");
	$tot_lost_customers=$tot_lost_customersq->numRows();
	$tot_lost_customersq->freeResult();
	$tot_new_customersq=$report_db->SubmitQuery("
        SELECT DISTINCT inv.customer_number 
        FROM sales_speed t1, server inv
        WHERE sec_finished_order>0
            AND t1.computer_number = inv.computer_number
        ");
	$tot_new_customers=$tot_new_customersq->numRows();
	$tot_new_customersq->freeResult();
   $tot_final_total=$tot_new_customers-$tot_lost_customers;
   print("\t<Th colspan=2> Sum Totals </Th>\n");
   print("\t<TD ALIGN=right> </TD>\n");
   print("\t<TD ALIGN=right> $tot_new_customers </TD>\n");
   print("\t<TD ALIGN=right> $tot_lost_customers </TD>\n");
   print("\t<TD ALIGN=right> $tot_final_total </TD>\n");
?>
</TR>
</TABLE>

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
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>
