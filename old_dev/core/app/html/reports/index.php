<?PHP
// THIS IS ALL YOU NEED!
require_once("CORE_app.php");
require_once("menus.php");
require_once("./includes.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: SQL Reports
    </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?set_title('SQL Reports','#003399');?>    
<SCRIPT TYPE="text/javascript" 
        LANGUAGE="Javascript1.2" 
        SRC="/script/MENU_workspace.js">
</SCRIPT>
<?=menu_headers()?>
</HEAD>
<?=page_start()?>
<?include("form_wrap_begin.php"); ?>
<!-- Begin Reports --------------------------------------------------------- -->
<BR>
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
	<TD>
	<TABLE BORDER="0"
	       CELLSPACING="2"
	       CELLPADDING="2"
	       ALIGN="left">
	<TR>
		<TD BGCOLOR="#003399" 
			CLASS="hd3rev"> SQL Reports </TD>
	</TR>
	<TR>
		<TD>
			<FORM ACTION="show.php">
			<SELECT NAME="page" SIZE='20'>
			<?php

$dir = ereg_replace( "/[^/]*$", "", $SCRIPT_FILENAME );
$dirobj = dir( '.' );
$list = array();
while( $file = $dirobj->read() ) {
    if( eregi( "_report.php$", $file ) ) {        
        //filter out all reports that will be going away as part of Onyx integration.  If a user complains about
        // a report being no longer available, inform them that the report is going away as part of the Onyx integration.
        // If they still really want the report, let Ben Truitt know, and tell them this work around:
        // navigate to http://core.rackspace.com/reports/show.php?page=[name of report filename here]
        if($file == 'account_segment_report.php') {
            $title = getTitle($file);
            $list[$title] = $file;
        }
    }
}
 
ksort($list);
foreach( $list as $title => $file ) {
    print "<option value='$file'>$title</option>\n";
}

			?></SELECT>
			</TD>
		</TR>
		<TR>
			<TD>
                        <SELECT NAME="download">
                        <OPTION VALUE="">HTML</OPTION>
                        <OPTION VALUE="xls">Excel</OPTION>
                        </SELECT>
			<INPUT TYPE="image"
			       SRC="/images/button_command_select_off.jpg"
			       BORDER="0">
			</FORM></TD>
	</TR>
	</TABLE></TD>
</TR>
</TABLE>
<!-- End Reports ----------------------------------------------------------- -->
<?include("form_wrap_end.php"); ?>
<?=page_stop()?>
</HTML>
<?php

// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
