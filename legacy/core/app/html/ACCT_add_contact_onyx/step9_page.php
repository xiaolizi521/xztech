<?require_once('step9_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Add Contact Wizard - 9</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step9_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Add Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Enter a Secret Question & Answer</B></TD>
                        </TR>
                        <TR> 
                            <TD> 
                                <P>Enter a question that will be asked of the
                                   contact for verbal authentication challenge.
                                   Then enter the correct answer that the person
                                   must give as the response.</P>
                                </TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV ALIGN="CENTER">
                                    <TABLE>
                                        <TR>
                                            <TD ALIGN="right">Question: </TD>
                                            <TD><INPUT TYPE="text" NAME="question" VALUE="<?=$question ?>" SIZE="32" CLASS="data"></TD>
                                        </TR>
                                        <TR>
                                            <TD ALIGN="right">Answer: </TD>
                                            <TD>
                                                <INPUT TYPE="text" NAME="answer" VALUE="<?=$answer ?>" SIZE="32" CLASS="data">
                                            </TD>
                                        </TR>
                                    </TABLE>
                                </DIV>
                            </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="next" VALUE=" Next -> " CLASS="data" style="display: none"><!-- default submit button for enter key -->
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
