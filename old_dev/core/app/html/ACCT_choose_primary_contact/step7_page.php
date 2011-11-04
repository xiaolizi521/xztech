<?require_once('step7_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Choose Primary Contact Wizard - 7</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">
<FORM ACTION="step7_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Choose Primary Contact</FONT></B></TD> 
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Enter the Primary Phone Number</B></TD>
                        </TR>
                        <TR> 
                            <TD>Please enter the primary phone number for the contact. 
                                You can add other phone numbers later.</TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV ALIGN="CENTER"> 
                                    <TABLE>
                                        <TR> 
                                            <TD ALIGHT="RIGHT"><? showIsRequired("Primary Phone:", $primary_phone_number); ?> </TD>
                                            <TD NOWRAP> 
                                                <INPUT TYPE="text" NAME="primary_phone_number" VALUE="<?=$primary_phone_number ?>" MAXLENGTH="32" SIZE="14" CLASS="data">
                                          Type: 
                                                <SELECT NAME="primary_phone_type_id" CLASS="data">
                                                <?=$primary_phone_type_id_options ?>
                                                </SELECT>
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
