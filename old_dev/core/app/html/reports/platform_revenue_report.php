<?php
# TITLE: Platform Revenue for Top 50 Accounts
# $Id: platform_revenue_report.php 1.3 02/01/29 01:27:51-00:00 egrubbs@ $

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

# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='"LastName"'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.

$db->SubmitQuery('
    SELECT *
    INTO TEMP platform_revenue_temp
    FROM (
        SELECT customer_number,
            SUM(final_monthly) AS monthly
        FROM server
        WHERE status_number >= ' . STATUS_ONLINE . '
        GROUP BY customer_number
        ) q1
    ORDER BY monthly DESC
    LIMIT 50
    ');

$status_online = STATUS_ONLINE;
$query = <<< SQL
SELECT 
    ' ALL ACCOUNTS' AS account,
    0 AS customer_number,
    SUM(final_monthly) AS platform_monthly,
    determine_os(computer_number) AS platform
FROM server
    JOIN platform_revenue_temp USING (customer_number)
WHERE status_number >= $status_online
GROUP BY platform
UNION
SELECT 
    text(customer_number) AS account,
    customer_number,
    SUM(final_monthly) AS platform_monthly,
    determine_os(computer_number) AS platform
FROM server
    JOIN platform_revenue_temp USING (customer_number)
WHERE final_monthly > 0
    AND status_number >= $status_online
GROUP BY customer_number, platform
UNION
SELECT text(customer_number), 
    customer_number, 
    monthly, 
    'ALL PLATFORMS'
FROM platform_revenue_temp
ORDER BY customer_number, platform_monthly DESC
SQL;


$report->setQuery($query);

$report->ignoreField( 'customer_number' );
# This sets the size of a single page.  We generally don't want to
# let the user alter this
$report->setPageSize( 500 );
$report->_total_pages = 1;

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

        $report->setFieldRule( 'platform_monthly',
            '<p align=right>%platform_monthly</p>');

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
