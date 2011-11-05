<?require_once('start_logic.php') ?>
<?require_once('step3_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Last Name </TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="edit_person_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
            <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Last Name</FONT></B>   <span style="color: #ffdd66;">This EDIT feature is to correct misspellings only.</span></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
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
                                          <TD><?=$first_name ?></TD>
                                          <INPUT TYPE="hidden" NAME="first_name" VALUE="<?=$first_name ?>" >
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
