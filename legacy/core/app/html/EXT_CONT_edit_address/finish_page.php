<?require_once('finish_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Address Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="finish_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Address</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5" WIDTH="100%">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Commit Changes</B></TD>
                        </TR>
                        <TR>
                          <TD><p>You are asking to change the address to:</p>
                               <TABLE BORDER='0' CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
                                  <TR>
                                     <TD STYLE="background: #DDDDDD; width: 100%; height: 100%;">
                                        <PRE><?=$new_address ?></PRE>
                                     </TD>
                                  </TR>
                              </TABLE>
                          </TD>
                        </TR>
                        <TR> 
                            <TD> 
                               <p>For the following contacts:</p>
                               <TABLE BORDER='0' CELLPADDING="2" CELLSPACING="0" WIDTH="100%">
                                  <TR>
                                     <TD STYLE="background: #DDDDDD; width: 100%; height: 100px;">
                                        <DIV NAME="div_contact_list" STYLE="width: 100%; height: 100%; overflow: auto;">
                                           <?=$contact_list ?>
                                        </DIV>
                                     </TD>
                                  </TR>
                               </TABLE>
                              <p>
                              If this is correct, please press <b>Finish</b>
                              </p>
                            </TD> 
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
