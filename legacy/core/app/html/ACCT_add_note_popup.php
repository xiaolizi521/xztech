<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( "account_number" => "Account Number" ) );

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

?>
<HTML>
<HEAD>
<TITLE>Add A Note</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="ACCT_add_note_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Add A Note</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Enter the text of your note</B></TD>
                        </TR>
                        <TR>
                          <TD>
                              <p>
                              Type in the note you want to add to
                              account <b><?=$account->account_name ?></b>.
                              This note will be highlighted to
                              seperate them from ones automatically generated
                              by <b>CORE</b>.
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <textarea cols='40' rows='4' name='note_text'></textarea>
                              <input type='hidden' name='account_number'
                              value='<?=$account_number ?>'>
                     
                          </TD> 
                        <TR> 
                            <TD ALIGN="RIGHT">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Add Note " CLASS="data">
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
