<?php

require_once("CORE_app.php");
require_once("act/ActFactory.php");

$notes = "";

$i_account = ActFactory::getIAccount();
$account = $i_account->getAccountByAccountNumber($GLOBAL_db, $account_number);

if( in_dept("CORE") ) {
    $deletable = true;
} else {
    $deletable = false;
}

$ucOnly = $ucOnly != "f";


$noteArray = $account->getNotes();

$note_count = count($noteArray);
foreach($noteArray as $currentNote) {        
    $note_id = $currentNote->notePrimaryId;
    $name = $currentNote->getUserIdentifier();
    $date = $currentNote->insertDate;
    $text = $currentNote->noteText;
    $uc   = $currentNote->userComment;
    
    //see if we are supposed to exclude non - user comments.
    if($ucOnly && !$uc) {
        continue;
    }
    
    if($uc) {
        $fgcolor = "#FF6666";
        $bgcolor = "#FFCCCC";
        $cnrimg = "note_corner_red.gif";
    } 
    else {
        $fgcolor = "#FFCC33";
        $bgcolor = "#FFF999";
        $cnrimg = "note_corner.gif";
    }
    
    $notes .= '<TABLE WIDTH="220" BORDER="0" CELLSPACING="0" CELLPADDING="0">
  <TR>
    <TD VALIGN="top"
        BGCOLOR="'.$fgcolor.'">
        <IMG SRC="/images/'.$cnrimg.'"
             WIDTH="10"
             HEIGHT="10"
             HSPACE="0"
             VSPACE="0"
             BORDER="0"
             ALIGN="TOP"
             ALT="">';
    if($deletable) {
        $notes .= '<br><a href="javascript:makePopUpNamedWin(\'/ACCT_delete_note_popup.php?account_number='.$account_number.'&note_id='.$note_id.'\',370,300,\'\',4,\'delete_acct_note\')">' .
             '<img src="/images/ex-small.gif" width="10" height="10" border="0" alt="Delete" valign="top" VSPACE="2" HSPACE="1"></a>&nbsp;';
    }
    $notes .= '</TD>
	<TD BGCOLOR="'.$fgcolor.'">' .$name. ' 
	<BR> ' . $date .
         ' CDT </TD>
  </TR>
  <TR>
  	<TD WIDTH="10" BGCOLOR="'.$bgcolor.'"> &nbsp; </TD>
    <TD BGCOLOR="'.$bgcolor.'">' . $text . '</TD>
  </TR>
</TABLE><BR CLEAR="all">
';
}

// Local Variables:
// mode: php 
// c-basic-offset: 4
// indent-tabs-mode: nil 
// End:
?>
