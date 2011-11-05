<?php

require_once("CORE_app.php");

//session_start();

/* Next Step! */
session_register("SESSION_step");
$step = $SESSION_step;

session_register("SESSION_choice");
session_register("SESSION_question");
session_register("SESSION_answer");

$question = stripslashes($SESSION_question);
$answer = stripslashes($SESSION_answer);

?>
