<?php require_once("CORE_app.php"); ?>
<?
	
	//This section sets the quick codes for the configurations

	$basic_linux="BKFRHKG10.";
	$adv_linux="BG3HKGH3.C5.";

	$basic_nt="CKFRBKG10.";
	$adv_nt="CJ2HBKGH3.C5.";

	$basic_raq="FBF2KG2.";
	$adv_raq="FB2HKG2.";
?>
<HTML>
<HEAD>
<TITLE>ADD RESERVE SERVER</TITLE>
<META NAME="ROBOTS" CONTENT="None">
</HEAD>
<?require_once("tools_body.php");?>

<?include("form_wrap_begin.php")?>
<TABLE WIDTH="440" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=6 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" ><CENTER>Add Reserve Server</CENTER></FONT></TD>
</TR>

<TR BGCOLOR="#C0C0C0">
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER>Good</TD>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER>Better</TD>
</TR>
<TR>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($basic_linux);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($basic_linux);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($basic_linux);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($basic_linux);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($basic_linux);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($basic_linux);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($basic_linux);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($basic_linux);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($basic_linux);?></TD></TR>
	</TABLE>
	<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$basic_linux\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($adv_linux);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($adv_linux);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($adv_linux);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($adv_linux);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($adv_linux);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($adv_linux);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($adv_linux);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($adv_linux);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($adv_linux);?></TD></TR>
	</TABLE>
		<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$adv_linux\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>
</TR>
<TR>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($basic_nt);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($basic_nt);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($basic_nt);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($basic_nt);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($basic_nt);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($basic_nt);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($basic_nt);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($basic_nt);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($basic_nt);?></TD></TR>
	</TABLE>
		<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$basic_nt\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($adv_nt);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($adv_nt);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($adv_nt);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($adv_nt);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($adv_nt);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($adv_nt);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($adv_nt);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($adv_nt);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($adv_nt);?></TD></TR>
	</TABLE>
		<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$adv_nt\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>

</TR>
<TR>

	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($basic_raq);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($basic_raq);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($basic_raq);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($basic_raq);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($basic_raq);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($basic_raq);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($basic_raq);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($basic_raq);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($basic_raq);?></TD></TR>
	</TABLE>
		<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$basic_raq\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>
	<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
	<TD COLSPAN=2 ALIGN=CENTER VALIGN=TOP>
	<FORM ACTION="create_reserve.php3" METHOD=POST>
	  <INPUT TYPE=HIDDEN NAME="command" VALUE = "CREATE_RESERVE">
	  <TABLE>
		<TR><TH>Quick&nbsp;Code</TH><TD><?print($adv_raq);?><TD></TR>
		<TR><TH>OS</TH><TD><?QCDisplayOs($adv_raq);?></TD></TR>
		<TR><TH>Processor</TH><TD><?QCDisplayProcessor($adv_raq);?></TD></TR>
		<TR><TH>Memory</TH><TD><?QCDisplayMemory($adv_raq);?></TD></TR>
		<TR><TH>Raid</TH><TD><?QCDisplayRaid($adv_raq);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;1</TH><TD><?QCDisplayHardDrive1($adv_raq);?></TD></TR>
		<TR><TH>Hard&nbsp;Drive&nbsp;2</TH><TD><?QCDisplayHardDrive2($adv_raq);?></TD></TR>
		<TR><TH>Bandwidth</TH><TD><?QCDisplayBandwidth($adv_raq);?></TD></TR>
		<TR><TH>Ip</TH><TD><?QCDisplayIp($adv_raq);?></TD></TR>
	</TABLE>
		<?print ("<INPUT TYPE=HIDDEN NAME=\"quick_code\" VALUE=\"$adv_raq\">\n"); ?>
		<INPUT TYPE=SUBMIT VALUE="Create Server">
	 </FORM>
	 </TD>
</TR>
<TR>
	
</TR>
</TABLE>
<?include("form_wrap_end.php");?>


<TABLE WIDTH="440" BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN=3 HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>

</TABLE><BR CLEAR="ALL">
<?$db->CloseConnection();?>
</BODY>
</HTML>
