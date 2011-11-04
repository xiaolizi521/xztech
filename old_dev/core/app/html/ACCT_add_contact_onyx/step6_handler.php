<?php

require_once("CORE_app.php");

session_register("SESSION_org_search");
session_register("SESSION_step");

function gset( $lvar, $rvar ) {
        global $$lvar, $$rvar;
        if( isset($$rvar) ) {
                $$lvar = $$rvar;
        } else {
                $$lvar = '';
        }
}

if(!empty($back)) {
    $SESSION_step -= 1;
    ForceReload("step3b_page.php");
} elseif(!empty($next)) {
    session_register("SESSION_job_title");
    session_register("SESSION_street1");
    session_register("SESSION_street2");
    session_register("SESSION_street3");
    session_register("SESSION_city");
    session_register("SESSION_state");
    session_register("SESSION_zip");
    session_register("SESSION_country_id");
    gset( 'SESSION_job_title', 'job_title' );
    gset( 'SESSION_street1', 'street1' );
    gset( 'SESSION_street2', 'street2' );
    gset( 'SESSION_street3', 'street3' );
    gset( 'SESSION_city', 'city' );
    gset( 'SESSION_state', 'state' );
    gset( 'SESSION_zip', 'zip' );
    gset( 'SESSION_country_id', 'country_id' );

    if(isset($job_title) && !empty($street1) && !empty($city)
       && !empty($country_id)) {
        $SESSION_step += 1;
        ForceReload("step7_page.php");
    } else {
        ForceReload($HTTP_REFERER);
    }
}

?>
