<?php
require_once("CORE_app.php");
if( !is_array( $theme_info ) ) {
    $theme_info = array();
}
if( empty($theme_info["next_url"]) ) {
    $theme_info["next_url"] = "MUST_SET_THE_NEXT_URL";
}
if( empty($theme_info["title"]) ) {
    $theme_info["title"] = "MUST SET THE TITLE";
}

function start_theme() {
    global $theme_info;
?>
<HTML>
<HEAD>
<TITLE>Add Rackspace Contact Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="<?=$theme_info['next_url']?>">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;
                     Add Rackspace Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B><?=$theme_info['title']?></B></TD>
                        </TR>
                        <TR> 
                            <TD>
<?php
}

function end_theme() {
    global $theme_info;
?>

                            </TD>
                        </TR>
                    </TABLE>
                </TD>
            </TR>
        </TABLE>
    </DIV>
</FORM>
</BODY>
</HTML>
<?php
}




?>
