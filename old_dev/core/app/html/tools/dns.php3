<?php
require_once("CORE_app.php");
require_once("menus.php");
if( empty($command) ) { $command = ""; }
if( empty($customer_number) ) { $customer_number = ""; }
if( empty($computer_number) ) { $computer_number = ""; }


if( empty($domains) ) {
	$domains=5;
}
?>

<HTML>
<TITLE>DNS Record submission</TITLE>
<?php
require_once("tools_body.php");
?>
<H1>DNS Record Submission</h1>
<p>
    <h3>
Please note: this page submits tickets to Professional Services.  It does <em>not</em> directly create any DNS records.
    </h3>
</p>
<?php
if( $command=="submit_dns" and
    !empty($customer_number) and
    !empty($computer_number) ) {
	$message="New DNS record\n";
	$message= $message."Customer Number/Server Number:$customer_number-$computer_number\n";
	$message= $message."Entered by ".GetRackSessionUserid()."\n";
	if ($service=="P_S")
		$message=$message."Primary & Secondary entries for all domains \n";
	else if ($service=="P_S_R")
		$message=$message."Primary & Secondary & Reverse entries for all domains \n";
	else if ($service=="R")
		$message=$message."Reverse Only entries for all domains \n";
	else if ($server="S")
		$message=$message."Only Secondary entries for all domains \nPrimary DNS IP is $primary_dns_ip\n";
	$message=$message.$comments."\n";

	$message=$message."\n\n";
	$computer= new RackComputer;
	$computer->Init($customer_number,$computer_number,$db);

	$os_type=$computer->OS();
	if ($os_type=="RAQ/RAQ2"||$os_type=="RAQ3")
		$title="RAQ DNS Entry Request";
	else if ($service=="S")
		$title="Secondary DNS Entry Request";
	else
		$title="DNS Entry Request";

    // ARO - 2005-12-13
    // Altered this to select the INTENSIVE or MANAGED pro srv queue.
    // This replaces the now obsolete IP Administration queue.
    // as per CORE-6734 added check to use the requested subcategory when sending
    // this ticket, and revert to the current subcategory when used by INTENSIVE.
    // added by Nathen Hinson 11-28-07.
    $account_number = $computer->getAccountNumber();
    $i_account = ActFactory::getIAccount();
    $account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);    

    if ($account->segment_id == INTENSIVE_SEGMENT) {
        $queue = QUEUE_INTENSIVE_PROFESSIONAL_SERVICES;
        $subcategory = TICKET_CATEGORY_IP_ADMINISTRATION_DNS;
    } else {
        $queue = QUEUE_MANAGED_PROFESSIONAL_SERVICES;
        $subcategory = TICKET_CATEGORY_MANAGED_NETSEC_IPDNS;
    }

    $ticket_number = $computer->GenTicket2( $queue,
                                            $subcategory,
                                            TICKET_SEVERITY_MODERATE,
                                            $title,
                                            $message );

    $computer->Log($ticket_number."\n".$comments."\n");

	$message=ereg_replace("\n","<p>",$message);
	print($message);
	
	if (isset($page)) { print("<BR>$page"); }
} elseif( !empty($command) and
          ( empty($customer_number) or 
            empty($computer_number)
              )
    ) {
?>
<TITLE>Error</TITLE>
<BODY BGCOLOR=#FFFFFF>
<h1>Error</h1>
<p>You are missing information at the top of the form. Go back and make sure that you filled out the customer number,computer number, and your userid.
<?php
 } else {
?>
<table border=3><tr>
<th bgcolor=black><font size=-1 color=white>Current Field Count
<td><font size="-1"><b><?=$domains?></b></td>
<th bgcolor=black><font size=-1 color=white>Select Domain Name Field Count
<td><font size=-1>
    <a href=dns.php3?<?=getargs(array('domains'=>5))?>>5
<td><font size=-1>
    <a href=dns.php3?<?=getargs(array('domains'=>10))?>>10
<td><font size=-1>
    <a href=dns.php3?<?=getargs(array('domains'=>15))?>>15
<td><font size=-1>
    <a href=dns.php3?<?=getargs(array('domains'=>20))?>>20
</table>
<DIV id="formlayer" style="position:relative; z-index:1; visibility: visible">
<FORM ACTION="dns.php3" METHOD="POST">
<INPUT TYPE=HIDDEN NAME="command" VALUE="submit_dns">

<TABLE>
<TR><TH>Account Number</TH><TD><INPUT TYPE=TEXT NAME="customer_number" value="<?=$customer_number?>">
<SIZE=20></TD></TR> <TR><TH>Server Number</TH><TD><INPUT TYPE=TEXT SIZE=20 name="computer_number" VALUE="<?=$computer_number?>
"></TD></TR>
<!--
<TR><TH>Is this an RAQ?</TH><TD><SELECT name=RAQ>
<OPTION>No
<Option>Yes
</SELECT></TD></TR>
-->
<TR><TH COLSPAN=2>If this is for secondary only put the name of the dns server in the domain name</TH></TR>
<TR><TH>Comments:</TH><TD><TEXTAREA COLS=20 ROWS=2 NAME=comments></TEXTAREA></TD></TR>
<TR><TH>Type of Service</TH><TD><SELECT NAME="service">
<OPTION VALUE="P_S">Primary & Secondary 
<OPTION VALUE="P_S_R">Primary & Secondary  & Reverse
<OPTION VALUE="R">Reverse Only
<OPTION VALUE="S" >Secondary only
</SELECT></TD></TR>
<TR><TH><i>If secondary only<i> IP of PRIMARY DNS Server</TH><TD><INPUT TYPE=TEXT NAME="primary_dns_ip" SIZE=20></TD></TR>

<TR><TH COLSPAN=2>&nbsp;</TH></TR>
<TR><TH COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="SUBMIT"></TH></TR>
</TABLE>
</FORM>
<?php
}
?>
</DIV>
<?= page_stop() ?>
</BODY>
</HTML>
