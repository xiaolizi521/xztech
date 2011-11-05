<?php

require_once("CORE_app.php");

//session_start();
session_register("SESSION_question");
session_register("SESSION_answer");
session_register("SESSION_external_contact_primary_id");
session_register("SESSION_individual_id");

if(empty($individual_id) and empty($SESSION_individual_id)) {
    trigger_error("Missing individual_id");
}

if (!empty($individual_id)) {
    $SESSION_individual_id = $individual_id;
    $SESSION_external_contact_primary_id = $external_contact_primary_id;
}

if (!isset($question)) {
    $question = $SESSION_question;
}
if (!isset($answer)) {
    $answer = $SESSION_answer;
}

$question = stripslashes($question);
$answer = stripslashes($answer);

session_register("SESSION_step");
$step = $SESSION_step;

?>
