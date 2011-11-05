<?php

require_once("CORE_app.php");


if(!empty($last_name)) {

    session_register("SESSION_last_name");
    session_register("SESSION_first_name");
    session_register("SESSION_step");
    
    $first_name = trim(@$first_name);
    $SESSION_first_name = stripslashes($first_name);

    $last_name = trim(@$last_name);
    $SESSION_last_name = stripslashes($last_name);

	$iAccount = ActFactory::getIAccount();
	$result = $iAccount->customerRetrieveList(stripslashes($last_name),stripslashes($first_name));
	// the SOAP call adds its own slashes

    // If they have no results, push them to the new person page.
    if( !$result ) {
            ForceReload("step3b_page.php");
            exit;
    }       
    $person_list = array();
    if ( $result ) {
        foreach ($result as $item) {
            $person_list[] = $item;
        }
    }
    $SESSION_step += 1;
    $step = $SESSION_step + 1;

} else {
    ForceReload($HTTP_REFERER);
}

?>
