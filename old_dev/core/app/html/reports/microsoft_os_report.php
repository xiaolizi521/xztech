<?php
# TITLE: Microsoft OS Report
# $Id: microsoft_os_report.php 1.2 01/10/01 22:32:22-00:00 egrubbs@ $

# Hello!  Welcome to this demo report.  You can view this report by
# calling the show.php page like so:
# show.php?page=example_report.demo.php

# This report will not show up on the index.php list, because it does
# not end with _report.php

# YOUR reports should end with _report.php, though.

# The 'TITLE:' bit at the top is important.  It must be in the first
# 5 lines of the file, and it must start with 1 or more '#'s.
# This sets the TITLE as it will appear in the report list and as it
# will appear an the show.php page.


# This is the query.  Make sure you like the field names.  You want them
# to be human readable.

$now = time();
$year = strftime('%Y', $now);
$month = strftime('%m', $now);
$last_month = $month - 1;
if ($month == 1) {
    $last_month = 12;
    $last_year = $year - 1;
}
else {
    $last_month = $month - 1;
    $last_year = $year;
}

$start_sec = mktime(0, 0, 0, $last_month, 1, $last_year, 0);
$end_sec = mktime(0, 0, 0, $month, 1, $year, 0);

$query = <<< SQL
SELECT DISTINCT ON (computer_number)
    *
FROM (
    SELECT t3.product_description, t1.product_sku, 
        "AccountNumber", t1.computer_number,
        TEXTCAT("LastName", textcat(', ', "FirstName")), 
        "Street", "City", "State", c4."Abbrev" as "Country", "PostalCode",
        date(sec_finished_order::abstime) as "Begin Date", 
        date(sec_due_offline::abstime) as "End Date",
            CASE 
            WHEN t4.product_description ~* 'Dual' THEN 2
            ELSE 1
            END as "Processor Count"
    FROM 
        sales_speed t5
        JOIN server_parts t1 USING (computer_number)
        JOIN server_parts t2 USING (computer_number)
        JOIN product_table t3 ON t1.product_sku = t3.product_sku
        JOIN product_table t4 ON t2.product_sku = t4.product_sku
        JOIN "ACCT_Account" c1 ON t5.customer_number = c1."AccountNumber"
        JOIN "ACCT_xref_Account_Contact_AccountRole" x1
            ON c1."ID" = x1."ACCT_AccountID"
        JOIN "CONT_Contact" c2 ON x1."CONT_ContactID" = c2."ID"
        JOIN "CONT_Address" c3 ON c2."CONT_AddressID" = c3."ID"
        JOIN "CONT_Country" c4 ON c3."CONT_CountryID" = c4."ID"
        JOIN "CONT_Person" c5 ON c2."CONT_PersonID" = c5."ID"
        LEFT JOIN queue_cancel_server t6 USING (computer_number)
    WHERE sec_finished_order > $start_sec
        AND sec_finished_order < $end_sec
        AND t3.product_name = 'OS'
        AND (t3.product_description ~* 'win'
            OR t3.product_description ~* 'nt')
        AND t4.product_name = 'Processor'
        AND x1."ACCT_val_AccountRoleID" = ACCOUNT_ROLE_PRIMARY
    UNION
    SELECT t3.product_description, t1.product_sku, 
        "AccountNumber", t1.computer_number,
        TEXTCAT("LastName", textcat(', ', "FirstName")), 
        "Street", "City", "State", c4."Abbrev" as "Country", "PostalCode",
        date(sec_finished_order::abstime) as "Begin Date", 
        date(sec_due_offline::abstime) as "End Date",
        processor_count AS "Processor Count"
    FROM 
        sales_speed t5
        JOIN server_parts t1 USING (computer_number)
        JOIN product_table t3 USING (product_sku)
        JOIN microsoft_os_license_map t4 
            ON t3.product_sku = t4.license_product_sku
        JOIN "ACCT_Account" c1 ON t5.customer_number = c1."AccountNumber"
        JOIN "ACCT_xref_Account_Contact_AccountRole" x1
            ON c1."ID" = x1."ACCT_AccountID"
        JOIN "CONT_Contact" c2 ON x1."CONT_ContactID" = c2."ID"
        JOIN "CONT_Address" c3 ON c2."CONT_AddressID" = c3."ID"
        JOIN "CONT_Country" c4 ON c3."CONT_CountryID" = c4."ID"
        JOIN "CONT_Person" c5 ON c2."CONT_PersonID" = c5."ID"
        LEFT JOIN queue_cancel_server t6 USING (computer_number)
    WHERE sec_finished_order > $start_sec
        AND sec_finished_order < $end_sec
        AND x1."ACCT_val_AccountRoleID" = ACCOUNT_ROLE_PRIMARY
    ) AS foo
SQL;
$report->setQuery($query);
$report->setCountQuery('SELECT 2000');

# This says to ignore (not print) the field CID.  You can then use this
# for generating info or links behind the scenes.
$report->ignoreField( 'CID' );

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
        # You need both of these globals!!
        global $report, $page_index;

        # This turns the Name Field into a link to the correct contact.
        $report->setFieldRule( 'Name',
                            '<a href="' . $GLOBALS["roster_url"] . 'view_employee.jsf?contact_id=%CID">%FirstName %LastName</a>' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
