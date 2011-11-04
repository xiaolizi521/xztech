<?php
require("./logic.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<TITLE> CORE: Customer IQ Reports </TITLE>
	<LINK HREF="/css/core_ui.css"
          REL="stylesheet">	
</HEAD>
<BODY MARGINWIDTH="10"
      MARGINHEIGHT="10"
      LEFTMARGIN="10"
      TOPMARGIN="10"
      BGCOLOR="#FFFFFF">
<!-- Begin Instructions ---------------------------------------------------- -->
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
      <p> Below are reports pulled from CORE.  All reports are in TSV format (Tab Seperated Values) which can be read directly into MS EXCEL. To set a different date, select the month and year, and then press <b>Set Date</b>. All data is current.
      </p>
    </TD>
</TR>
</TABLE>
<BR><BR CLEAR="all">
<form>

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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> CORE: Salvage Yard Reports </TD>
               </TR>

               <TR>
                 <TD align="center">
                   Date: <select name="month">
                   <?php
                   for( $i=1; $i<13; $i++ ) {
                       echo "<option ";
                       if( $i == $month ) {
                           echo "SELECTED ";
                       }
                       echo "value=\"$i\"> $i </option>\n";
                   }
                   ?>
                   </select>
                   <select name="year">
                   <?php
                   for( $i=1999; $i<=($this_year+1); $i++ ) {
                       echo "<option ";
                       if( $i == $year ) {
                           echo "SELECTED ";
                       }
                       echo  "value=\"$i\"> $i </option>\n";
                   }
                   ?>
                   </select>
                 </TD>
               </TR>
               <TR>
                 <TD align="center">
                   Currently viewing <?=$month?>-<?=$year?>
                 </TD>
               </TR>
               <TR>
                 <TD align="center">
                   <input type="submit" value=" Set Date ">
                 </TD>
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

</form>
</BODY>
</HTML>
