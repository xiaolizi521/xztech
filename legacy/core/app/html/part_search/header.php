<? require_once("menus.php") ?>
<HTML>
<HEAD>
	<TITLE>CORE: Part Search</TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <SCRIPT LANGUAGE="JavaScript"
            SRC="/script/popup.js"></SCRIPT>
    <SCRIPT LANGUAGE="JavaScript"
            SRC="/script/date-picker.js"></SCRIPT>
<STYLE type="text/css">
th { background: #cccccc; color: #000022; }
td.alt { background: #f0f0f0; }
pre { font-family: courier, monospace; font-size: 8pt}
.skutable { float: left }
.skutable { align: left }

table.reporter {border: double black; margin: 1ex}
th.reporter {background: #666666;
color: white}

.reporter {border-spacing: 0}

table.reporter td {border-top: solid #808080;
border-right: solid #808080;
text-align: right}
tr.reporter    {background: #cccccc}
tr.reporterodd {background: #ffffff}

optgroup[label] {
  font-style: normal
}

</STYLE>
<?= menu_headers() ?>
</HEAD>
<?= page_start() ?>
<?php
	if( !empty( $notes ) ):
?>
<br clear='all'>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="0"
       WIDTH='600'>
<TR BGCOLOR="#FFCC33">
    <TD VALIGN="top">
  <IMG SRC="/images/note_corner.gif"
       WIDTH="10"
       HEIGHT="10"
       HSPACE="0"
       VSPACE="0"
       BORDER="0"
       ALIGN="TOP"
       ALT=""> NOTES: </TD>
</TR>
<TR BGCOLOR="#FFF999">
    <TD> <?=$notes ?> </TD>
</TR>
</TABLE>
<BR>
<?php
  endif;
?>

<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
	<TABLE BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="2"
	       ALIGN="left" >
	<TR>
	  <TD BGCOLOR="#003399" CLASS="hd3rev">
	  <?php
	  if( empty( $title ) ) {
	    echo "Untitled Area";
	  } else {
	     echo $title;
	  }
	  ?>
	  </TD>
	</TR>
	<TR>
	  <TD>	
      
