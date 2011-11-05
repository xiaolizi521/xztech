<!-- Begin Computer Tools -------------------------------------------------- -->
<!-- ----------------------------------------------------------------------- -->
<TABLE class=blueman>
<TR>
	<th class="blueman" colspan="3"> Computer Tools: #<?print($customer_number);?> - <?print($computer_number);?> </th>
</TR>
<TR>
	<TD WIDTH="22">
		<FORM ACTION="display_computer.php3"
		      METHOD="POST">
		<INPUT TYPE=HIDDEN
		       NAME="customer_number"
			   VALUE="<?print($customer_number);?>">
		<INPUT TYPE=HIDDEN
		       NAME="computer_number"
			   VALUE="<?print($computer_number);?>">
		<INPUT TYPE=HIDDEN
		       NAME="command"
			   VALUE="QUICK_COMMENT">
		<INPUT TYPE=IMAGE
		           SRC="/images/button_arrow_off.jpg"
				   WIDTH=20
				   HEIGHT=20
				   BORDER=0 ALT="Go"
				   ALIGN="ABSMIDDLE"></TD>
	<TD NOWRAP VALIGN="middle" colspan=1> QUICK COMMENT </TD>
</TR>
<TR>
    <TD> &nbsp; </TD>
	<TD colspan="2"><TEXTAREA COLS=40
		          ROWS=2
				  WRAP=VIRTUAL
				  NAME="quick_comment"></TEXTAREA>
		</FORM></TD>
</TR>
<TR>
<td> &nbsp; </td>
</TR>
<TR>
    <td colspan="2">
<SCRIPT TYPE="text/javascript" LANGUAGE ="javascript" >
    function ticketCreate()
    {
        option = document.getElementById("create_ticket");
        href = option.options[option.selectedIndex].value;
        makePopUpNamedWinNoClose(href,690,800,'',3,"Create_Ticket");
    }
</SCRIPT>

<a href='#' onclick="ticketCreate();return false;"><IMG SRC="/images/button_arrow_off.jpg" WIDTH=20 HEIGHT=20 BORDER=0 ALT="Go" ALIGN="ABSMIDDLE"/></a>
&nbsp;&nbsp;
CREATE TICKET: <SELECT NAME = "create_ticket" ID = "create_ticket" SIZE = "1" >
<?
$href = "<OPTION SELECTED = 'selected' VALUE = '$py_app_prefix/ticket/new/support.pt?account_number=$customer_number&computer_number=$computer_number' >";
?>
    <?=$href?> Create Support Ticket</OPTION>
<?
$href = "<OPTION VALUE = '$py_app_prefix/ticket/new/dcops.pt?computer_number=$computer_number' >";
    ?>
    <?=$href?> Create DCOps Ticket</OPTION>
<?
$href = "<OPTION VALUE = '$py_app_prefix/ticket/new/managed_backup.pt?account_number=$customer_number&computer_number=$computer_number' >";
?>
    <?=$href?> Create Managed Backup Ticket</OPTION>
<?
$href = "<OPTION VALUE = '$py_app_prefix/ticket/new/managed_backup_restore.pt?account_number=$customer_number&computer_number=$computer_number' >";
?>
    <?=$href?> Create Managed Backup Restore Ticket</OPTION>
<?
$href = "<OPTION VALUE = '$py_app_prefix/ticket/new/network_security.pt?computer_number=$computer_number&account_number=$customer_number' >";
?>
    <?=$href?> Create Network Security Ticket</OPTION>
</SELECT>
    </td>
</TR>
<TR>
<td> &nbsp; </tid>
</TR>
<?
	// is there a build error?
	if(!$has_build_error && !$build_error_free) {
		// if not, check and see if person is in production
		if(in_dept("PRODUCTION")) {
			// if in production, show button
			$show_build_error_button = true;
		} else {
			// else hide button
			$show_build_error_button = false;
		}
	} else {	
		// if so, show button
		$show_build_error_button = true;
	} 
	
	if($show_build_error_button) {
?>
	<tr><td colspan="3">
	<? $href="$py_app_prefix/computer/showBuildErrors.pt?computer_number=$computer_number" ?>
	<? $href="<a href=\"$href\" class=\"text_button\">Document Build Errors</a>" ?>
		<?= $href ?>
	
	<? $href="$py_app_prefix/ticket/serverHistory.pt?status=all&computer_number=$computer_number" ?>
	<? $href="<a href=\"$href\" class=\"text_button\">Show Computer Ticket History</a>" ?>
		<?= $href ?>
    
	</td></tr>
<? } ?>


<?if( $current_status>7 ):?>
<?if ($computer->requiresAudit()&&in_dept("SUPPORT")):?>
<TR>
 	<TD COLSPAN="2">
		<TABLE WIDTH="100%"
		       BORDER="1"
		       CELLSPACING="0"
		       CELLPADDING="0"
		       ALIGN="left">
		<TR>
			<TD><BLINK><B><FONT COLOR=#FF0000>&nbsp; AUDIT THIS COMPUTER </FONT></B></BLINK></TD>
		</TR>
		</TABLE></TD>
</TR>
<TR>
	<TD colspan="2"><A HREF="/tools/display_computer.php3?customer_number=<?php print($customer_number);?>&computer_number=<?print($computer_number);?>&command=MARK_AUDITED" class="text_button">
	    Audit Complete </a> </TD>
</TR>
<?endif;?>
<?endif;?>
<?php
/* Is it Upgrade Restricted? */
if( $computer->IsUpgradeRestricted() ){
    echo "<tr><td colspan=2><blink>";
    echo "<b><font color=\"red\">UPGRADE RESTRICTED COMPUTER</font></b>";
    echo "</blink></td></tr>\n";
}

/* Build Instructions */
echo "<tr><td colspan=2>";
echo "<a href=\"/tools/set_completion.php3?customer_number=$customer_number&computer_number=$computer_number\" class=\"text_button\">";
echo 'Set Build Instructions </a>';
if( $current_status > 7 ){
    echo '<span style="color: red">(Server Already Complete)</span>';
}
echo "</td></tr>\n";


$building_one_hour=0;
$edit_list="customer_number=$customer_number&computer_number=$computer_number";

$is_build_tech = $db->getVal("
          SELECT 1
          FROM build_tech
          WHERE userid='".GetRackSessionUserid()."'
            AND computer_number = $computer_number
            AND customer_number = $customer_number ");

if( $current_status > -1 and
    $current_status < 12 and
    in_dept("PRODUCTION")) {

    if( !$is_build_tech ) {
        echo "<tr><td colspan=2>";
        echo "<a href=\"display_computer.php3?command=MARK_BUILD_TECH&customer_number=$customer_number&computer_number=$computer_number\" class=\"text_button\">";
        echo "Mark Me as Build Tech</a>";
        echo "</td>";
        echo "</tr>";
    }
}

    echo "<tr><td colspan=2>";
    echo "<a href=\"confirm_regenerate_provisioning_alert.php?";
    echo "customer_number=$customer_number&";
    echo "computer_number=$computer_number\" ";
    echo "class=\"text_button\">";
    echo "Re-generate Provisioning Alert</a>";
    echo "</td>";
    echo "</tr>";

?>

<!-- ----------------------------------------------------------------------- -->
<!-- End Computer Tools ---------------------------------------------------- -->

<!-- Begin Computer Status ------------------------------------------------- -->
<!-- ----------------------------------------------------------------------- -->

<TR>
<TD COLSPAN=2>
<table class=blueman style="width: 100%;"><tr><td>
<TABLE class=datatable>
<TR>
<th style="width: 20%;"> Computer Status </TD>
<TD> <FONT COLOR="red"><?=$computer->getData("status")?></FONT>
<?php
if( $computer->requiresAudit() and in_dept("SUPPORT") ){
      echo "<blink><font color=\"red\"> &nbsp; [ AUDIT THIS COMPUTER ]</font><blink>";
}

if( $sec_due_offline and $current_status > -1 ) {
  echo "<br>\n";
  echo "<font color=\"red\"> Scheduled for cancelation: $sec_due_offline</font>";
}

if( $db->getVal("SELECT 1 FROM reserve_customer WHERE customer_number=$customer_number;") ) {
  echo "<BR>\n";
  echo "<FONT SIZE=\"+1\" COLOR=\"red\"> RESERVE CUSTOMER PROFILE </FONT>\n";
}
if( $computer->IsUpgradeRestricted() ){
  echo "<BR>\n";
  echo "<FONT SIZE=\"+1\" COLOR=\"red\"> UPGRADE RESTRICTED COMPUTER </FONT>";
}

//If this is a colo box - we do not bill until the server goes online
//This provides a visual reminder :)
if( $computer->OS() == "Colocation" and $current_status < 12 ) {
  print("<BR><B>Colo - Do Not Bill Until Online/Complete</B>");
}

?>
</td></tr></table>
</TD>
</TR>
</TABLE>
</TD>
</TR>

<?php
   // Display the button to turn the VM off if needed
   // A virtual machine only gets into the above status when there was a failure
   // in automatically suspending it via the VCC's API.  This button will attempt to re-suspend it 
   if ($current_status == STATUS_WAIT_SUSPENDED_VIRTUAL_MACHINE) {
    ?>
	<TR>
		<th> Incomplete Status: </th>
		<td colspan=4 nowrap>    
	    	<div id="vm_suspend">
	         	<a class='text_button' onclick="suspendVM(<?=$computer_number?>)">Turn VM Off</a>
	    	</div>
		</td>
	</TR>
	<?
	}
	?>
	<TR>
		<TD COLSPAN=4>
		    <div id="computerStatus" style="border:0pt; border-color:black">Loading status selection...</div>
		</TD>
		<script>
		loaddiv("/py/computer/computerStatus.pt?computer_number=<?= $computer_number ?>&customer_number=<?= $customer_number ?>", "computerStatus");
		setTimeout("dynamicMenuPositioning()", 3500);
		</script>
	</TR>
	
	</TABLE>


<!-- ----------------------------------------------------------------------- -->
<!-- End Computer Status --------------------------------------------------- -->
