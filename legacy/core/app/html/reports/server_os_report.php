<?php
# TITLE: Server OS/Device Type
# $Id: server_os_report.php 1.1 01/11/29 19:32:50-00:00 egrubbs@ $


# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
$status_online = STATUS_ONLINE;
$query =<<< SQL
    SELECT *
    FROM (
        SELECT customer_number as account, computer_number as computer, 
            determine_os(computer_number) AS os
        FROM server
        WHERE status_number >= $status_online
        ) q1
    ORDER BY account, computer
SQL;

$report->setQuery($query);

$count_query =<<< SQL
    SELECT COUNT(*)
    FROM server
    WHERE status_number >= $status_online
SQL;

$report->setCountQuery($count_query);

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

        # This turns the Name Field into a link to the correct contact.
        $report->setFieldRule( 'account',
            '<a href="/ACCT_main_workspace_page.php?'
            . 'account_number=%account">%account</a>' );
        $report->setFieldRule( 'computer',
            '<a href="/ACCT_main_workspace_page.php?'
            . 'content_page=' . urlencode('/tools/DAT_display_computer.php3')
            . '&args=' . urlencode('computer_number=') . '%computer'
            . '">%computer</a>');

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
