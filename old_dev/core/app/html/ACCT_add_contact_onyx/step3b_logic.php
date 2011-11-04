<?php

require_once("CORE_app.php");
require_once('helpers.php');

session_register("SESSION_person_fname");
session_register("SESSION_person_lname");
session_register("SESSION_last_name");
session_register("SESSION_first_name");
session_register("SESSION_step");

$step = $SESSION_step + 1;

if(empty($SESSION_person_fname)) {
    $first_name = $SESSION_first_name;
} else {
    $first_name = $SESSION_person_fname;
}

if(empty($SESSION_person_lname)) {
    $last_name = $SESSION_last_name;
} else {
    $last_name = $SESSION_person_lname;
}

?>
