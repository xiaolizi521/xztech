<?require_once('step4_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Person Wizard - 4</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step4_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Person</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Remove Old Contact</B></TD>
                        </TR>
                        <TR>
                            <TD>By selecting this action, this contact will be
                                removed from this account, and a new contact
                                will be added in its place.</TD>
                        </TR>
                        <TR>
                            <TD><DIV ALIGN="center">
                                    Are your sure you want to do that?
                                    <TABLE BORDER="0">
                                        <TR>
                                            <TD><INPUT TYPE="radio" NAME="confirm" VALUE="yes"> Yes<BR>
                                                <INPUT TYPE="radio" NAME="confirm" VALUE="no"> No
                                            </TD>
                                        </TR>
                                    </TABLE>
                                </DIV>
                            </TD> 
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Next -> " CLASS="data">
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
