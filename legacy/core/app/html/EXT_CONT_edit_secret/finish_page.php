<?require_once('finish_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Secret Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="finish_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Secret</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Commit Changes</B></TD>
                        </TR>
                        <TR>
                          <TD>
                              You are asking to set the Secret
                              Question to '<b><?=$question ?></b>'
                              and the Answer to '<b><?=$answer ?></b>'.
                              <p>
                              If this is correct, please press <b>Finish</b>
                              </p>
                          </td>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Finish " CLASS="data">
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
