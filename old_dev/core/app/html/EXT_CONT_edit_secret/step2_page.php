<?require_once('step2_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Secret Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step2_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Secret</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Edit the Secret</B></TD>
                        </TR>
                        <TR>
                          <TD>Fill out both the secret question and
                              secret answer.  The secret question will
                              be asked for by the Rackspace Employee.
                              The customer will be expected to know and
                              remember the secret answer.
                              <p>
                              Complete the question and answer
                              and press <b>Next</b>
                              </p>
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <table border='0' align='center'>
                              <tr>
                                <td align='right' valign='top'>
                                  Secret Question:
                                </td>
                                <td>
                                    <input type="text" size="50" name="question"
                                    value="<?=$question ?>" class="data">
                                </td>
                              </tr>
                              <tr>
                                <td align='right' valign='top'>
                                  Secret Answer:
                                </td>
                                <td>
                                    <input type="text" size="50" name="answer"
                                    value="<?=$answer ?>" class="data">
                                </td>
                              </tr>
                              </table>
                              
                     
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
