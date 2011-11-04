<?php
# TITLE: London Email 
# $Id: london_email_report.php 1.1 01/10/01 19:38:46-00:00 egrubbs@ $

# This report finds all London email addresses.

$status_online = STATUS_ONLINE;
$query = <<< SQL
SELECT "AccountNumber", "Address"
FROM "ACCT_Account" t1
    JOIN "ACCT_xref_Account_Contact_AccountRole" t2
        ON t1."ID" = t2."ACCT_AccountID"
    JOIN "CONT_Contact" t3
        ON t3."ID" = t2."CONT_ContactID"
    JOIN "CONT_xref_Contact_Email_ContactEmailType" t4
        ON t3."ID" = t4."CONT_ContactID"
    JOIN "CONT_Email" t5
        ON t5."ID" = t4."CONT_EmailID"
    JOIN server
        ON t1."AccountNumber" = customer_number
WHERE "ACCT_val_AccountRoleID" = ACCOUNT_ROLE_PRIMARY
    AND "CONT_val_ContactEmailTypeID" = 1
    AND (datacenter_number = 2 or datacenter_number = 7 or datacenter_number = 8)
    AND status_number >= $status_online
ORDER BY INT4("AccountNumber")
SQL;
$report->setQuery($query);

$report->setCountQuery('
    SELECT COUNT(*)
    FROM (
        SELECT DISTINCT customer_number
        FROM server
        WHERE (datacenter_number = 2 or datacenter_number = 7 or datacenter_number = 8)
            AND status_number >= ' . STATUS_ONLINE . '
        ) AS foo');

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
