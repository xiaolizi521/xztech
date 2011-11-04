<?require_once('step3_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Person Wizard - 3</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="edit_person_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Person</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Edit First and Last Name</B></TD>
                        </TR>
                        <TR>
                            <TD>Please make your corrections to the person's
                                name.  Click "Finish" to finalize your
                                changes.</TD>
                        </TR>
                        <TR>
                          <TD><DIV ALIGN="center">
                                  <TABLE BORDER="0">
                                      <TR>
                                          <TD ALIGN="right">First Name:</TD>
                                          <TD><INPUT TYPE="text" NAME="first_name" VALUE="<?=$first_name ?>" CLASS="data"></TD>
                                      </TR>
                                      <TR>
                                          <TD ALIGN="right">Last Name:</TD>
                                          <TD><INPUT TYPE="text" NAME="last_name" VALUE="<?=$last_name ?>" CLASS="data"></TD>
                                      </TR>
                                  </TABLE>
                              </DIV>
                          </TD> 
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                            <?php if ( $step != 1 ) { ?>
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                            <?php } ?>
                                <INPUT TYPE="submit" NAME="finish" VALUE=" Finish " CLASS="data">
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
