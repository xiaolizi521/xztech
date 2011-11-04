<?php

require_once("CORE_app.php");

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
    ForceReload("step6_page.php");
} elseif(!empty($next)) {

    if ( !empty( $new_phone_number ) ) {
        $primary_phone_number = "";
        while(list($key,$val) = @each($new_phone_number)) {
            $primary_phone_number .= $val;
        }
    } else { 
        $primary_phone_number = '';
    }
    
    session_register("SESSION_primary_phone_number");
    session_register("SESSION_primary_phone_type_id");   

    session_register("SESSION_fax_primary_phone_number");   

    gset('SESSION_primary_phone_number', 'primary_phone_number');
    gset('SESSION_primary_phone_type_id', 'primary_phone_type_id');

    gset('SESSION_fax_primary_phone_number', 'fax_number');

    if(!empty($primary_phone_number)
       and !empty($primary_phone_type_id)) {
        $SESSION_step += 1;
        ForceReload("step8_page.php");
    } else {
        ForceReload($HTTP_REFERER);
    }
}

// Local Variables:
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
