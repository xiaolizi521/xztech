<?require_once('step5_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Person Wizard - 5</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step5_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Person</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Contact Removed</B></TD>
                        </TR>
                        <TR>
                            <TD>
                                <P>The contact <B><?=$contact_name ?></B> has
                                    been queued to be removed from the account
                                    <B><?=$account_name ?></B>.</P>
                                <P>To finish replacing this contact, click
                                    "Next" and complete the "Add Contact"
                                    wizard.  If you do not complete the "Add
                                    Contact" wizard, no changes will be made.</P>
                            </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Add Contact -> " CLASS="data">
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
