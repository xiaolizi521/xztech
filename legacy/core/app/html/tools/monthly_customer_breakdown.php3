<? 

require_once("CORE_app.php");
require_once("act/ActFactory.php"); 

	$stamp=time();
	if (!isset($month)) {
		$month=date("m",$stamp);
    }
	if (!isset($year)) {
		$year=date("Y",$stamp);
    }
	set_back_link("monthly_customer_breakdown.php3?month=$month&year=$year");
$report_db = getReportDB();
?>
<HTML>
<TITLE>Monthly Account Sales Stats For <?print("$month / $year");?> </TITLE>
<?require_once("tools_body.php");?>
<?back_link();?>
<TABLE WIDTH="540" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" ><CENTER>Monthly  Account Sales Stats For <?print("$month / $year ");?> </CENTER></FONT></TD>
</TR>
<tr><td colspan=6>
<?php
if( empty($REPORTDB_IS_AVAILABLE) ) {
    echo '<p style="font-weight: bolde">All data is current as of now.</p>';
} else {
    echo '<p style="font-weight: bold">All data is current as of yesterday night.</p>';
}
?>
</td></tr>

<TR>
	<TH>Date</TH>
	<TH>Start Total</TH>
	<TH># New Accounts</TH>
	<TH># Lost Accounts </TH>
	<TH>Final Total</TH>
</TR>
<?

	if (checkdate($month,31,$year))
			$last_day=31;
	else if (checkdate($month,30,$year))
			$last_day=30;
	else if (checkdate($month,29,$year))
			$last_day=29;
	else if (checkdate($month,28,$year))
			$last_day=28;
	$begin_mark=mktime(0,0,0,$month,1,$year)-1; //Puts it at 23:59
	$begin_mark=date("m/d/Y",$begin_mark);
	$end_mark="$month/$last_day/$year";
?>
<TR>
<?
//Find total to date
	$pre_totalq=$report_db->SubmitQuery("
        SELECT DISTINCT ON (customer_number)  
            customer_number 
        FROM sales_speed 
        WHERE date(sec_finished_order::abstime) <='".$begin_mark."' 
            AND sec_finished_order>0;
        ");
	$pre_total=$pre_totalq->numRows();
	$pre_totalq->freeResult();
	$new_customers_list=$report_db->SubmitQuery("
        SELECT DISTINCT ON (customer_number)  
            customer_number 
        FROM sales_speed 
        WHERE date(sec_finished_order::abstime) > '".$begin_mark."' 
            AND date(sec_finished_order::abstime) <= '$end_mark' 
        EXCEPT 
        SELECT DISTINCT ON (customer_number) 
            customer_number 
        FROM sales_speed 
        WHERE date(sec_finished_order::abstime) <='".$begin_mark."' 
            AND sec_finished_order>0;
        ");
	$lost_customers_list=$report_db->SubmitQuery("
        SELECT DISTINCT ON (customer_number) 
            customer_number 
        FROM offline_servers t1
        WHERE date(sec_offline::abstime) >'$begin_mark' 
            AND date(sec_offline::abstime) <='$end_mark' 
            AND customer_number not in
                (SELECT customer_number
                 FROM server sub1
                 WHERE t1.customer_number = sub1.customer_number
                    AND status_number >= " . STATUS_ONLINE . "
                 LIMIT 1
                )
        ");
	$change_lost_customers = $lost_customers_list->numRows();
	$lost_customersq = $report_db->SubmitQuery("
        SELECT DISTINCT ON (customer_number) 
            customer_number 
        FROM offline_servers t1
        WHERE date(sec_offline::abstime)<='$end_mark' 
            AND customer_number not in
                (SELECT DISTINCT customer_number
                 FROM server sub1
                 WHERE t1.customer_number = sub1.customer_number
                    AND status_number >= " . STATUS_ONLINE . "
                 LIMIT 1
                )
        ");
	$lost_customers=$lost_customersq->numRows();
	$lost_customersq->freeResult();
	$new_customers=$new_customers_list->numRows();
	$final_total=$pre_total+$new_customers-$lost_customers;
	$tot_new_customers = $new_customers;
	$tot_lost_customers = $lost_customers;
	$start_customers=$pre_total;
	print("<TH>$month/$year</TH>");
print("<TD ALIGN=CENTER>$pre_total</TD>");
print("<TD ALIGN=CENTER>$new_customers</TD>");
print("<TD ALIGN=CENTER>$lost_customers ($change_lost_customers)</TD>");
print("<TD ALIGN=CENTER>$final_total</TD>");
?>
</TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD>&nbsp;</TD>
	<TD>
	</TD>
	<TD>
<?
$i_account = ActFactory::getIAccount();

	//Platform Makeup Start
	$num=$new_customers_list->numRows();
	print("<TABLE><TR><TH>#</TH><TH>Name</TH></TR>\n");
	for ($i=0;$i<$num;$i++)
	{
			$customer_number=$new_customers_list->getResult($i,"customer_number");
		  print("<TR><TD ALIGN=CENTER><A href=\"/py/account/view.pt?account_number=$customer_number\">$customer_number</a></TD>");
        
    $account = $i_account->getAccountByAccountNumber($GLOBAL_db, $customer_number);

		if(!empty($account)) {
      $primaryContact = $account->getPrimaryContact();
			print("<TD>" . $primaryContact->individual->getFullName() . "</TD>");
    }
		else {
			print("<TD>&nbsp;</TD>");
    }
		print("</TR>\n");
	}
	print("</TABLE>");
	$new_customers_list->freeResult();
?>
	</TD>
	<TD VALIGN=TOP>
<?

	$num=$lost_customers_list->numRows();
	print("<TABLE><TR><TH>#</TH><TH>Name</TH></TR>\n");
	for ($i=0;$i<$num;$i++)
	{
		$customer_number=$lost_customers_list->getResult($i,"customer_number");
		print("<TR><TD ALIGN=CENTER><A href=\"/py/account/view.pt?account_number=$customer_number\">$customer_number</a></TD>");
        
    $account = $i_account->getAccountByAccountNumber($GLOBAL_db, $customer_number);
    
    if(!empty($account)) {
        $primaryContact = $account->getPrimaryContact();
        print("<TD>" . $primaryContact->individual->getFullName() . "</TD>");
    }
	else {
        print("<TD>&nbsp;</TD>");
    }
		print("</TR>\n");
	}
	print("</TABLE>");

?>
	</TD>
</TR>
</TABLE>
<TABLE WIDTH="540" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=3 HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>

</TABLE><BR CLEAR="ALL">
<?back_link();?>
<?=page_stop()?>
</HTML>
