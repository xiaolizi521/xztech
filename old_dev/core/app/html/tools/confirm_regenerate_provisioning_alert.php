<HTML id="mainbody">
<TITLE>Confirm Regenerate Provisioning Alert</TITLE>
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0">
<TR>
        <TD>
                <TABLE BORDER="0"
                       CELLSPACING="2"
                       CELLPADDING="2">
                <TR>
                        <TD COLSPAN="2"
                            BGCOLOR="#003399"
                            CLASS="hd3rev">Confirm Regenerate Provisioning Alert for Computer #<?=$computer_number?> </TD>
                </TR>
   
                <TR>
<TD COLSPAN="2">
<a class="text_button" 
    href="display_computer.php3?command=RESEND_VAL_ADD&computer_number=<?= $computer_number ?>">
    Resend Provisioning Alert
</a>
<a class="text_button" 
    href="display_computer.php3?computer_number=<?= $computer_number ?>">
    Cancel
</a>
</TD>
                </TR>
                </TABLE>
        </TD>
</TR>
</TABLE>
</FORM>
</HTML>
