<?php

require_once("CORE_app.php");

$external_contact = '';
$i_contact = ActFactory::getIContact();
$external_contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);

$notes = '';

$contactNotes = $external_contact->getNotes();
foreach($contactNotes as $currentNote ) {
  $notes .= '
                  <TABLE class="note">
                  <TR>
                    <th class="note">' . $currentNote->getUserIdentifier() .  
                                         '</th>
                    <th class="date">'. $currentNote->insertDate . '</th>
                  </TR>
                  <TR>
                    <TD class="note" colspan=2>' . $currentNote->noteText . '<BR></TD>
                  </TR>
                </TABLE><br>
';
}
?>