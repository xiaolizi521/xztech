<?php

require_once("CORE_app.php");

$contact_notes = '';

$contact = new CONT_Contact;
$contact->loadID($contact_id);

$temp = $contact->getNotes();
$temp = $temp->getArray();
foreach( array_reverse($temp) as $part ) {
  $note =& $part->getNote();
  $author =& $note->getContact();
  $person =& $author->getPerson();
  $contact_notes .= '
                  <TABLE class="note">
                  <TR>
                    <th class="note">' . $person->getFirstName() .
                                         ' ' . $person->getLastName() .  
                                         '</th>
                    <th class="date">'. $note->getDate() . '</th>
                  </TR>
                  <TR>
                    <TD class="note" colspan=2>' . $note->getText() . '<BR></TD>
                  </TR>
                </TABLE><br>
';
}
?>