<?php

require_once("CORE_app.php");

//session_start();
session_register("SESSION_step");
session_register("SESSION_question");
session_register("SESSION_answer");

$SESSION_question = $question;
$SESSION_answer = $answer;

$SESSION_step += 1;
ForceReload( "finish_page.php" );

?>
