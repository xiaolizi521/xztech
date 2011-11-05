<?PHP
// THIS IS ALL YOU NEED!
require_once("ACCT_view_by_role_logic.php");
require_once("menus.php");
?>
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: List Contacts and Teams by Account Role
    </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
	<?= menu_headers() ?>
<SCRIPT LANGUAGE="JavaScript" SRC="/script/popup.js" TYPE="text/javascript"></SCRIPT>
</HEAD>
<?= page_start() ?>

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
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Contacts by Account Role </TD>
         		</TR>
               <TR>
                  <TD> <?php printCReport(); ?> </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<?= page_stop() ?>
</HTML>
