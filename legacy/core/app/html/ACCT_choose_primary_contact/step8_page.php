<?require_once('step8_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Choose Primary Contact Wizard - 8</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">
<FORM ACTION="step8_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Choose Primary Contact</FONT></B></TD> 
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Enter the Primary E-Mail 
                                Address </B></TD>
                        </TR>
                        <TR> 
                            <TD>Please enter the primary e-mail 
                                address for the contact. You can add other e-mail 
                                addresses later.
                                <P>
                               <? if ( !empty($SESSION_error) ): ?>
                               <FONT COLOR=RED><?=$SESSION_error?></FONT>
                               <? endif; ?>
                                </TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV ALIGN="CENTER"> 
                                    <TABLE>
                                        <TR>
                                            <TD ALIGN="RIGHT"><? showIsRequired("Primary E-Mail Address:", $email); ?> </TD>
                                            <TD>
                                                <INPUT TYPE="text" NAME="email" VALUE="<?=$email ?>" SIZE="32" CLASS="data">
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
