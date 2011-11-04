<?php
require_once("CORE_app.php");
require_once("EXT_CONT_delete_phone_logic.php");

?>
<HTML>
<HEAD>
    <TITLE>
        CORE: Delete Phone
    </TITLE>
	<LINK HREF="/css/core_popup.css" REL="stylesheet">
</HEAD>
<BODY MARGINWIDTH="0" 
      MARGINHEIGHT="0" 
	  LEFTMARGIN=0 
	  TOPMARGIN=0 
	  BGCOLOR="#FFFFFF">
<TABLE BORDER="0" 
	   CELLSPACING="0" 
	   CELLPADDING="4" 
	   ALIGN="left">
<TR>
	<TD>
<!-- Begin Left Content Area ----------------------------------------------  -->
<!-- Begin Edit Email ------------------------------------------------------ -->
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
		    CLASS="hd3rev"> Delete Phone Confimation </TD>
		<TD><IMG SRC="/images/tbl-right-top.jpg" 
		         WIDTH="10" 
				 HEIGHT="25" 
				 BORDER="0" 
				 ALT=""></TD>
	</TR>
	<TR>
		<TD BACKGROUND="/images/tbl-left-tile.jpg"> &nbsp; </TD>
		<TD>
			<FORM action="EXT_CONT_delete_phone_handler.php">
			<?=$hidden_tags ?>
			Are you sure you want to delete<BR>
			<FONT CLASS=label>
				<?=$phone_value ?>
				(<?=$phone_type_value ?>) </FONT>
			<BR>
			from 
			<FONT CLASS=label>
			<?=$first_name_value ?>
			<?=$last_name_value ?>? </FONT>
			
			<BR><BR>
			<INPUT TYPE="submit"
			       NAME="delete"
			       VALUE="yes">			
			<INPUT TYPE="submit"
			       NAME="delete"
			       VALUE="no">	
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
