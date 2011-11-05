<?php

require_once("CORE_app.php");

//session_start();
session_register("SESSION_step");
session_register("SESSION_choice");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step2_page.php");
    exit;
}

$note_text = '';

$i_contact = ActFactory::getIContact();
$external_contact = $i_contact->getExternalContact($GLOBAL_db, $SESSION_external_contact_primary_id);

$question = stripslashes($SESSION_question);
$answer = stripslashes($SESSION_answer);
if($question != $external_contact->individual->secretQuestion
        or $answer != $external_contact->individual->secretAnswer) {
    $external_contact->individual->setSecretQuestionAnswer($question, $answer);
}

?>
<HTML>
<HEAD>
<!-- Refresh calling view -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function close_it() { window.close(); }
window.opener.location = window.opener.location;
//-->
</SCRIPT>
</HEAD>
<BODY onLoad="setTimeout(close_it,1)">
</BODY>
</HTML>
