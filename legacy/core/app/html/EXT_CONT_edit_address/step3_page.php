<?require_once('step3_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Address Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step3_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Address</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Select Contacts for Address Change </B></TD>
                        </TR>
                        <TR>
                          <TD>
                              Choose the contacts to apply the change to and press <b>Next</b>

                              <p>
                              <?=$contact_list ?>
                              </p>
                          </td>
                        </TR>
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
