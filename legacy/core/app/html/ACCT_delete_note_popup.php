<?php
require_once("CORE_app.php");
require_once("helpers.php");
require_once("act/ActFactory.php");

checkDataOrExit( array( "account_number" => "Account Number",
                        "note_id" => "Note ID" ) );

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);
$account_name = $account->account_name;
$notes = $i_account->getAccountNotes($account->crm_company_id);

foreach($notes as $note) {
    if($note->notePrimaryId == $note_id) {
        $contents = $note->noteText;
        if($note->userComment) {
            $type = "automatically generated note";
        } else {
            $type = "CORE user comment";
        }
    }
}

?>
<HTML>
<HEAD>
<TITLE>Delete A Note</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
<STYLE TYPE="text/css">
p.note {
color: blue
}
</STYLE>
</HEAD>
<BODY>
<FORM ACTION="ACCT_delete_note_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Delete A Note</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Do you wish to delete this note?</B></TD>
                        </TR>
                        <TR>
                          <TD>Note: <p class="note"><?=$contents ?></p>
                              <p> Are you sure you wish to delete this
                              <?=$type ?> permently from the account
                              <b><?=$account_name ?></b>?
                              <input type='hidden' name='account_number'
                              value='<?=$account_number ?>'>
                              <input type='hidden' name='note_id'
                              value='<?=$note_id ?>'>
                          </td>
                        <TR> 
                            <TD ALIGN="RIGHT">
                                <INPUT TYPE="submit" NAME="delete" VALUE=" Delete " CLASS="data">
                                <INPUT TYPE="submit" NAME="cancel" VALUE=" Cancel " CLASS="data">
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
