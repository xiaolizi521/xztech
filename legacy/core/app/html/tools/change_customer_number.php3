<?php require_once("CORE_app.php"); ?>
<?	

	if(!empty($command) && $command=="CHANGE_CUSTOMER_NUMBER")
	{
		$stamp=time();
		if ( $new_customer_number>0 )
		{
			//Now need to change over a lot of stuff. 
			change_customer_number($customer_number,$new_customer_number);
			$db->CloseConnection();
			Header("Location: /py/account/view.pt?account_number=$new_customer_number\n\n");
			exit;
		}
	}
?>
<HTML>
<HEAD>
	<TITLE> CORE: Edit Customer Number </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("account_action_menu.php");?>
</HEAD>
<?require_once("tools_body.php");?>
<?include("form_wrap_begin.php")?>
<FORM ACTION="change_customer_number.php3" METHOD="POST">
<INPUT TYPE=HIDDEN name=command value="CHANGE_CUSTOMER_NUMBER">
<INPUT TYPE=HIDDEN name=customer_number value="<?print($customer_number);?>">
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
		<TABLE BORDER="0"
		       CELLSPACING="2"
		       CELLPADDING="2">
		<TR>
			<TD BGCOLOR="#003399"
			    CLASS="hd3rev"> Edit Customer#: #<?print($customer_number);?> </TD>
		</TR>
		<TR>
			<TD> This change is permanent - some information about 
			this customer may be lost.  Information will default to whatever 
			is current for the number you are changing it to. </TD>
		</TR>
		<TR>
		<TD>
		<TABLE>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> Current Customer # </TD>
			<TD> <?print($customer_number);?> </TD>
		</TR>
		<TR>
			<TD BGCOLOR="#CCCCCC" CLASS="label"> New Customer # </TD>
			<TD> <INPUT TYPE=text name=new_customer_number size="5" maxlength="5"> </TD>
		</TR>
		</TABLE>
		</TD>
		</TR>
		<TR>
			<TD ALIGN="center"><INPUT TYPE="image"
			                          SRC="/images/button_command_save_off.jpg"
			                          BORDER="0"></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
<?include("form_wrap_end.php");?>
</BODY>
</HTML>
