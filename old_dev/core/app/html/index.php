<?php
require_once("CORE_app.php");

/*
 *  This opens the main CORE window to the correct thingy.
 */
if( !empty( $computer_number ) ) {
    $url = "/ACCT_main_workspace_page.php?computer_number=$computer_number";
} elseif( !empty( $account_number ) ) {
    $url = "/ACCT_main_workspace_page.php?account_number=$account_number";
} elseif( !empty( $ticket_number ) ) {
    $url = "/py/ticket/view.pt?ref_no=$ticket_number";
} else {
    $url = "/py/splash.pt";
}
header("Location: $url");
?>
