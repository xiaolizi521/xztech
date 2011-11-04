<?PHP
REQUIRE_ONCE("CORE.php");
REQUIRE_ONCE("ACCT_delete_contact_logic.php");

?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Delete Contact
    </TITLE>
	<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY MARGINWIDTH="0" 
      MARGINHEIGHT="0" 
	  LEFTMARGIN=0 
	  TOPMARGIN=0 
	  BGCOLOR="#FFFFFF">
<BR><BR>
<TABLE BORDER="0" 
	   CELLSPACING="0" 
	   CELLPADDING="4" 
	   ALIGN="left">
<TR>
	<TD>
<!-- Begin Left Content Area ----------------------------------------------  -->
<!-- Begin Edit Account ------------------------------------------------------ -->
	<TABLE BORDER="0" 
	       CELLSPACING="0" 
		   CELLPADDING="0" 
		   ALIGN="left">
	<TR>
		<TD><IMG SRC="/images/tbl-left-top.jpg" 
		         WIDTH="20" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
		<TD BGCOLOR="#003399" 
		    CLASS="hd3rev"> Delete Contact Confimation </TD>
		<TD><IMG SRC="/images/tbl-right-top.jpg" 
		         WIDTH="10" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
	</TR>
	<TR>
		<TD BACKGROUND="/images/tbl-left-tile.jpg"> &nbsp; </TD>
		<TD>
            <FORM ACTION="ACCT_delete_contact_handler.php">
			<?=$hidden_tags ?>
			<BR>Are you sure you want to remove<BR>
            		<B><?=$name_value ?></B> as <B><?=$role_value ?> Contact</B><BR>
			for <?=$account_name_value ?>
			(#<?=$account_number ?>)?
			<BR><BR>
			<INPUT TYPE="image"
			       NAME="DELETE"
				   VALUE="YES"
			       SRC="/images/button_command_small_yes.jpg"
			       BORDER="0">
			<INPUT TYPE="image"
			       NAME="NODELETE"
				   VALUE="NO"
			       SRC="/images/button_command_small_no.jpg"
			       BORDER="0">			
			</FORM>
		</TD>
	</TR>
	<TR ALIGN="left" 
	    VALIGN="top" 
		BGCOLOR="#003399">
		<TD ALIGN="left" 
		    VALIGN="bottom"><IMG SRC="/images/tbl-left-bottom.jpg" 
			                     WIDTH="20" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
		<TD BGCOLOR="#003399" 
		    class="smhd">&nbsp;</TD>
		<TD HEIGHT="8" 
		    ALIGN="left" 
			VALIGN="bottom"><IMG SRC="/images/tbl-right-bottom.jpg" 
			                     WIDTH="10" 
								 HEIGHT="8" 
								 BORDER="0" 
								 ALT="" 
								 ALIGN="TOP"></TD>
	</TR>
	</TABLE>
<!-- End Edit Email -------------------------------------------------------- -->
<!-- End Left Content Area ------------------------------------------------  -->
	</TD>
	<TD VALIGN="top">
<!-- Begin Right Content Area ---------------------------------------------  -->
				
<!-- End Right Content Area -----------------------------------------------  -->
	</TD>
</TR>
</TABLE>

</BODY>
</HTML>
