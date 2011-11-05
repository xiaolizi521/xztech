<?php

require_once("CORE_app.php");
require_once("helpers.php");

checkDataOrExit( array( 'search_type' => 'Type of Search' ) );

MakeNotEmpty($search_number);
$search_number = ereg_replace( "#", "", $search_number );
$search_number = trim($search_number);
$search_text = $search_number;
$search_number += 0;

$is_computer = $db->getVal( "select count(*) from server where computer_number = $search_number" );
$is_account = $db->getVal( "select count(*) from \"xref_customer_number_Account\" where customer_number = $search_number" );
$is_aggproduct = $db->getVal( "select count(*) from customer_agg_products where agg_product_number = $search_number" );

if( empty($search_text) ) {
    // Do Nothing
} elseif( $search_type == "customer_search" and $search_number == 0 ) {
    $search_type = "super_search_account_name";
} elseif( $search_type == "computer_search" and $search_number == 0 ) {
    $search_type = "super_search_computer_name";
} elseif( $search_type == "computer_search" and
          ereg( "^[0-9.]+$", $search_text ) and
          "$search_text" != "$search_number" ) {
    $search_type = "super_search_computer_ip";
} elseif( ereg("[0-9][0-9][0-9][0-9][0-9][0-9]-[0-9][0-9][0-9][0-9]", 
         $search_text) ) {
    $search_type = 'ticket_jump';
} elseif( ereg("-[0-9]+", $search_text) ) {
    $date = strftime("%y%m%d");
    $incr = substr($search_text,1);
    $search_text = sprintf("%06d-%04d", $date, $incr);
    $search_type = 'ticket_jump';
} elseif( $search_type == 'agg_search' ) {
    // Do Nothing, this is to bypass the stuff below....
} elseif( $is_computer and !$is_account ) {
    $search_type = "computer_search";
} elseif( $is_account and !$is_computer ) {
    $search_type = "customer_search";
} elseif( $is_aggproduct ) {
    $search_type = "agg_search";
}

// For Debugging
if( !empty($test_system) and 0 ) {
    trigger_error( "t: $search_type n: '$search_number'   '$search_text'",
                   FATAL );
}

// This runs Info Search if needed
switch( $search_type ) {
/*
 quick_find and info_search
 are handled in javascript (MENU_launcher.js),
 these are just in case javascript fails.
*/
case "super_search_account_name":
    ForcePopup("/tools/search.php3?command=SEARCH&account_name=$search_text",
               'supersearch');
    break;
case "super_search_computer_name":
    ForcePopup("/tools/search.php3?command=SEARCH&server_name=$search_text",
               'supersearch');
    break;
case "super_search_computer_ip":
    ForcePopup("/tools/search.php3?command=SEARCH&ip=$search_text",
               'supersearch');
    break;
case "super_search":
    ForcePopup("/tools/search.php3", 'supersearch');
    break;
case "quick_find":
    ForceReload("/tools/quick_find.php3");
    break;
case "info_search":
    ForceReload("/CORE_info_search.php?search_number=$search_number");
    break;
// These are always handled here.
case "customer_search":
    ForceReload("/tools/quick_find.php3?customer_number=$search_number&command=FIND_CUSTOMER");
    break;
case "computer_search":
    ForceReload("/tools/quick_find.php3?computer_number=$search_number&command=FIND_COMPUTER");
    break;
case "agg_search":
    $search_number += 0;
    ForceReload("/tools/quick_find.php3?agg_product_number=$search_number&command=FIND_AGG_PROD");
    break;
case "ticket_search":
    ForceReload("$py_app_prefix/ticket/search.pt");
    break;
case "ticket_jump":
    ForceReload("$py_app_prefix/ticket/view.pt?ref_no=$search_text");
    break;
case "session_search":
    ForceReload("http://www.rackspace.com/goconfigure/configurator/network_map.php?customer_number=$search_number");
    break;
default:
    print "<html><body><p style=\"text-align: center; margin: 2ex\">I'm sorry, CORE couldn't find anything matching '$search_text'.  If you think CORE should have been able to find this, then please send an email to core@rackspace.com and we'll try to expand CORE's searching ability.</p></body></html>";
    break;
}





?>
