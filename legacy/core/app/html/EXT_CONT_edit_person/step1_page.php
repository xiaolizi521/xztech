<?require_once('step1_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Person Wizard - 1</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step1_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Person</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Choose What to Change</B></TD>
                        </TR>
                        <TR>
                            <TD>There are two ways that you can edit a person.
                                You might want to fix a spelling error or typo
                                in a person's name, or you might want to change
                                the name to a completely different person.
                                Select which action you want to perform and
                                click "Next."</TD>
                        </TR>
                        <TR>
                            <TD><DIV ALIGN="center">
                                <TABLE BORDER="0">
                                    <TR>
                                        <TD><INPUT TYPE="radio" NAME="choice" VALUE="edit">
                                            Edit Name (fix typos, misspelling, etc.)<BR>
                                            <INPUT TYPE="radio" NAME="choice" VALUE="add">
                                            Change to a different person
                                        </TD>
                                    </TR>
                                </TABLE>
                                </DIV></TD> 
                        <TR> 
                            <TD ALIGN="RIGHT"> 
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
