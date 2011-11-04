<?php 
//////////////////////////////////////////
//
// This probably needs to be pruned
// because I just copied and modified
// set_completion.php3
// -JR
//
/////////////////////////////////////////

require_once("CORE_app.php");

if( empty($customer_number) and !empty($computer_number) ) {
$customer_number = $db->GetVal("
select customer_number
from server
where computer_number = $computer_number");
}

if( empty($customer_number) or empty($computer_number) ) {
    trigger_error("Requires Account Number and Computer Number.  Something must be wrong with referer: '$HTTP_REFERER'", FATAL);
}

$computer=new RackComputer;
$computer->Init($customer_number,$computer_number,$db);
if( !empty($command) and
    $command=="SET_BILLING_NOTES" and
    isset($billing_notes) ) {
    $stamp=time();

    $computer->setServerTableField("billing_notes", $billing_notes);

    $log = "Billing Notes set to: ";
    $log .= $billing_notes;
    $computer->Log($log);
    
    JSForceReload("/ACCT_main_workspace_page.php","content_page=" 
                  . urlencode("/tools/DAT_display_computer.php3") 
                  . "&args=" . urlencode("account_number=$customer_number&customer_number=$customer_number&computer_number=$computer_number"),"workspace");
}
?>
<HTML>
<TITLE>Edit Billing Notes</TITLE>
<?
//SET THE COMPUTER ACTION MENU IN THE NAV FRAME
$action_menu_type = "computer";
$action_menu_arg  = "computer_number=$computer_number";
require_once("tools_body.php");
?>
<TABLE CELLSPACING=0 CELLPADDING=0 VALIGN="TOP" BORDER=0 WIDTH=540>
<!-- end spacer -->

<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=3 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" FACE="Arial"><CENTER>Edit Billing Notes</CENTER></FONT></TD>
</TR>
<!-- spacer -->
<TR>
	<TD>&nbsp;</TD>
</TR>
	<TD BGCOLOR="#C0C0C0"><B>&nbsp;&nbsp;Customer Info:</B></TD>
	<TD>&nbsp;&nbsp;</TD>
</TR>
<TR>
<TD COLSPAN=2 ALIGN="CENTER">
<FORM ACTION="edit_billing_notes.php" METHOD="POST">
<INPUT TYPE=HIDDEN name=command value="SET_BILLING_NOTES">
<INPUT TYPE=HIDDEN name=customer_number value="<?print($customer_number);?>">
<INPUT TYPE=HIDDEN name=computer_number value="<?print($computer_number);?>">
<TABLE>
<TR><TH ALIGN=LEFT>Account Number-Server Numer</TH><TD><?print ($customer_number."-".$computer_number);?></TD></TR>
<TR><TH ALIGN=LEFT>Contact Name</TH><TD>
  <?
    $primaryContact = $computer->account->getPrimaryContact();
    print($primaryContact->individual->getFullName());
  ?>
</TD></TR>
<TR>
	<TD COLSPAN=2><CENTER><blink><Font color=#FF0000>Customers Can See This Information</FONT></BLINK></CENTER></TD>
</TR>

<TR><TH ALIGN=LEFT>Billing Notes:<BR><i>Please use this box to include any special instructions related to billing.</i></TH><TD><TEXTAREA ROWS=6 COLS=50 WRAP=VIRTUAL NAME="billing_notes"><?
	$billing_notes=$computer->getData("billing_notes");
print($billing_notes);
?></TEXTAREA></TD></TR>
<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE=SUBMIT VALUE="Submit Changes">
</TD></TR>
</TABLE>
</FORM></TD></TR></TABLE>

<TABLE WIDTH="540" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=3 HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>
</TABLE>
</BODY>
</HTML>
