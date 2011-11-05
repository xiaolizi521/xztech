<?php

require_once("CORE_app.php");

function gset( $lvar, $rvar ) {
    global $$lvar, $$rvar;
    if( isset($$rvar) ) {
        $$lvar = $$rvar;
    } else {
        $$lvar = '';
    }
}

session_register("SESSION_step");
session_register("SESSION_lock_account_role");

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step8_page.php");
} elseif(!empty($next)) {
        session_register("SESSION_question");
        session_register("SESSION_answer");

        gset("SESSION_question", "question");
        gset("SESSION_answer", "answer");

        $SESSION_step += 1;
        if( !empty( $SESSION_lock_account_role ) ) {
            ForceReload("finish_replace_page.php");
        } else {
            ForceReload("finish_page.php");
        }
} else {
        ForceReload($HTTP_REFERER);
}

?>