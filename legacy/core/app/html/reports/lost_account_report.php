<?php
# TITLE: Lost Account Report
# $Id: lost_account_report.php 1.1 01/10/26 16:52:08-00:00 egrubbs@ $

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

$status_online = STATUS_ONLINE;
$query = <<< SQL
SELECT DISTINCT customer_number AS "Account"
FROM sales_speed t1
WHERE t1.sec_finished_order > 0
    AND t1.customer_number NOT IN (
        SELECT customer_number
        FROM server sub1
        WHERE t1.customer_number = sub1.customer_number
            AND status_number >= $status_online
        )
ORDER BY customer_number
SQL;
$report->setQuery($query);
$report->setCountQuery('
    SELECT COUNT(*)
    FROM (
        SELECT DISTINCT customer_number AS "Account"
        FROM sales_speed t1
        WHERE t1.sec_finished_order > 0
            AND t1.customer_number NOT IN (
                SELECT customer_number
                FROM server sub1
                WHERE t1.customer_number = sub1.customer_number
                    AND status_number >= ' . STATUS_ONLINE . '
                )
        ) q1
    ');

$report->setPageSize(40);
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
        $report->setFieldRule( 'Account',
            '<a href="/ACCT_main_workspace_page.php?content_page='
            . urlencode("/py/account/view.pt")
            . '&args=' . urlencode('account_number=') 
            . '%Account">%Account</a>');


        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
