<?php

require_once("CORE_app.php");

session_register("SESSION_question");
session_register("SESSION_answer");
session_register("SESSION_step");

$step = $SESSION_step + 1;

if(empty($SESSION_question)) {
    $question = '';
} else {
    $question = $SESSION_question;
}

if(empty($SESSION_answer)) {
    $answer = '';
} else {
    $answer = $SESSION_answer;
}

?>
