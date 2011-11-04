<?require_once('step3b_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Choose Primary Contact Wizard - 3b</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step3b_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Choose Primary Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Create a New Person</B></TD>
                        </TR>
                        <TR> 
                            <TD> 
                                <P>You are about to add a new person to the database 
                                    because the last name you searched for was not 
                                    found. If you enter the name of a person who 
                                    is already in the database, then you will create 
                                    consistency problems in the database. Please 
                                    enter the name of the person you want to add 
                                    below.</P>
                                </TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV ALIGN="CENTER">
                                    <TABLE>
                                        <TR>
                                            <TD ALIGN="right">
                                          <? showIsRequired("First Name:",$first_name); ?> </TD>
                                            <TD><INPUT TYPE="text" NAME="first_name" VALUE="<?=$first_name ?>" MAXLENGTH="32" SIZE="32" CLASS="data"></TD>
                                        </TR>
                                        <TR>
                                            <TD ALIGN="right">
                                          <? showIsRequired("Last Name:",$last_name); ?> </TD>
                                            <TD>
                                                <INPUT TYPE="text" NAME="last_name" VALUE="<?=$last_name ?>" MAXLENGTH="32" SIZE="32" CLASS="data">
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
