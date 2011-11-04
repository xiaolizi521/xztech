<?php
require("./logic.php");
require_once("menus.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
	<TITLE> CORE: Customer IQ Reports </TITLE>
	<LINK HREF="/css/core_ui.css"
          REL="stylesheet">	
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <?=menu_headers()?>
</HEAD>
<?=page_start()?>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="0"
       ALIGN="left">
<TR BGCOLOR="#FFCC33">
    <TD VALIGN="top"><IMG SRC="/images/note_corner.gif"
                          WIDTH="10"
                          HEIGHT="10"
                          HSPACE="0"
                          VSPACE="0"
                          BORDER="0"
                          ALIGN="TOP"
                          ALT=""> INSTRUCTIONS: </TD>
</TR>
<TR BGCOLOR="#FFF999">
    <TD>
      <p> Below are reports pulled from CORE.  Each report is keyed off of the Account Number, except those marked otherwise.  All reports are in TSV format (Tab Seperated Values) which can be read directly into MS EXCEL. The date is when the SQL Query itself was updated.
<br>
<b>
<?php
getReportDB(); // Check that we can connect to it.
if( empty($REPORTDB_IS_AVAILABLE) ) {
    echo "All data is current as of now.";
} else {
    echo "All data is current as of yesterday night.";
}
?></b>
      </p>
    </TD>
</TR>
</TABLE>
<BR><BR CLEAR="all">		
<!-- End Instructions ------------------------------------------------------ -->  
<!-- Begin Main Content ---------------------------------------------------- -->
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
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> CORE: Customer IQ Reports </TD>
         		</TR>
               <TR>
                  <TD> <?php printList(); ?> </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</tr>
</TR>
</TABLE>
<!-- End Main Content ------------------------------------------------------ -->
<?=page_stop()?>
</HTML>
