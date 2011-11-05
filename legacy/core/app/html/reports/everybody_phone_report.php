<?php
# TITLE: Everybody's Phone (Restricted)
# $Id: everybody_phone_report.php 1.2 01/11/13 20:54:57-00:00 docwhat@ $

# This report finds all the site submissions and online server configurations
# whose email and customer first+last name do not match a contact in Core.

if (!in_dept('CORE')) {
    DisplayError('You do not have access to this report.');
}

$query = <<< SQL
SELECT DISTINCT phone 
FROM (
    SELECT phone
    FROM customer_submission 
    UNION
    SELECT phone
    FROM cart_customer_profile 
    UNION
    SELECT "Number"
    FROM "CONT_PhoneNumber"
    ) AS foo
SQL;
$report->setQuery($query);

$report->setCountQuery('
SELECT COUNT(*)
FROM (
    SELECT DISTINCT phone 
    FROM (
        SELECT phone
        FROM customer_submission 
        UNION
        SELECT phone
        FROM cart_customer_profile 
        UNION
        SELECT "Number"
        FROM "CONT_PhoneNumber"
        ) AS foo
    ) AS bar
    ');

# This sets the size of a single page.  We generally don't want to
# let the user alter this
$report->setPageSize( 40 );

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
        # You need both of these globals!!
        global $report, $page_index, $page;

        # These are the default args.  Useful for calling your page
        # again.
        $args = "page=$page&page_index=$page_index";

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
