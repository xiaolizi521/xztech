<?require_once('finish_replace_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Add Contact Wizard - Finish</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="finish_replace_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Add Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Replace Existing Contact</B></TD>
                        </TR>
                        <TR> 
                            <TD> 
                                <P>
                                  You have decided to replace the
                                <b><?=$role ?></b> contact
                                <b><?=$old_name ?></b> with
                                <?php if($is_new) echo "the new"; ?>
                                contact <b><?=$new_name ?></b>.
                                </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
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
