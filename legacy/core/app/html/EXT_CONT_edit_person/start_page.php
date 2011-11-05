<?require_once('start_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Person Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="start_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Person</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Welcome to the Edit Person Wizard!</B></TD>
                        </TR>
                        <TR>
                            <TD>This wizard will help you edit a person.
                                You can move back to the beginning of the
                                wizard by using the "Back" button, but if you
                                choose a different path forward through the
                                wizard again then you may loose some of your
                                previous edits.<br>
                                <?if($warning_replace_admin_who_is_primary): ?>
                                <p><font color="red">Warning:</font> 
                                You are trying replace an 
                                Administrative Contact who is also a 
                                Primary Contact.  
                                We will let you replace the 
                                Primary Contact instead.</p>
                                <?endif; ?>
                                <p>
                                <b>Click "Start" to begin.</b>
                                </p>
                                </td>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="start" VALUE=" Start " CLASS="data">
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
