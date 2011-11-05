<?require_once('EXT_CONT_edit_title_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Job Title</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="EXT_CONT_edit_title_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Job Title</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR>
                          <TD><p>
                              Edit the job title and press <b>Save</b>
                              </p>
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <table border='0' align='center'>
                              <tr>
                                <td align='right' valign='top'>
                                  Job Title:
                                </td>
                                <td>
                                    <input type="text" name="title_name" maxlength="40" size="40" value="<?=$title_name?>" /> <!-- max length limit so it will display nicely in Onyx -->
                                </td>
                              </tr>
                              </table>
                     
                          </TD> 
                        <TR> 
                            <TD ALIGN="RIGHT">
                                <?
                                  if(!empty($hidden_tags)) {
                                    print($hidden_tags);
                                  }
                                ?>
                                <INPUT TYPE="submit" NAME="cancel" VALUE=" Cancel " CLASS="data">
                                <INPUT TYPE="submit" NAME="save" VALUE=" Save " CLASS="data">
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
