<?$display_error_message=1;?>
<?require_once("CORE_app.php");?>
<?set_back_link("");?>
<HTML>
   <TITLE>CORE: ERROR</TITLE>
<?require_once("tools_body.php");?>

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
         			<TD BGCOLOR="#003399" CLASS="hd3rev">*** ERROR *** </TD>
         		</TR>
               <TR>
                  <TD>Message:<BR> <?print($error);?> </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<?$db->CloseConnection();?>
<?=page_stop()?>
</HTML>
