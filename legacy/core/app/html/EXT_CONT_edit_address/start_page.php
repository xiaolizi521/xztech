<?require_once('start_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Address Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step1_page.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Address</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Welcome to the Edit Address Wizard!</B></TD>
                        </TR>
                        <TR>
                            <TD>This wizard will help you edit a contact's
                                address.
                                You can move back to the beginning of the
                                wizard by using the "Back" button, but if you
                                choose a different path forward through the
                                wizard again then you may loose some of your
                                previous edits.<br>
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
