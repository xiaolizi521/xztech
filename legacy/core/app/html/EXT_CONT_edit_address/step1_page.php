<?require_once('step1_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Address Wizard</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step1_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Address</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Select the Country</B></TD>
                        </TR>
                        <TR>
                          <TD>Please select the correct country.
                              <p>
                              Select a country and press <b>Next</b>
                              </p>
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <table border='0' align='center'>
                              <tr>
                                <td align='right'>
                                  Country:
                                </td>
                                <td>
                                    <SELECT name="country_id">
                                      <?=$country_options ?>
                                      </SELECT>
                                </td>
                              </tr>
                              </table>
                              
                     
                          </TD> 
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
