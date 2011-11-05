<?php

require_once("CORE_app.php");
require_once('helpers.php');

session_register("SESSION_email");
session_register("SESSION_step");

$step = $SESSION_step + 1;

$email = '';
if(!empty($SESSION_email)) {
    $email = $SESSION_email;
}

?>
