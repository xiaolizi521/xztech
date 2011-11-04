<?php
# TITLE: Computers by Segment

//NOTE: this should not be referencing 'ACCT_val_AccountTypeID'. However, this report is to be deleteed, so I'm ignoring.
# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
$query =<<< SQL
    select
        c.computer_number,
        avat."Name"
    from
        "ACCT_Account" aa ,
        "ACCT_val_AccountType" avat ,
        "server" c
    where
        -- Limit
        c.status_number >= 7
        -- Joins
        AND aa."AccountNumber" = c.customer_number
        AND aa."ACCT_val_AccountTypeID" = avat."ID"

    order by avat."Name"
SQL;

$report->setQuery($query);

$count_query =<<< SQL
    SELECT COUNT(*)
    FROM "server"
    WHERE status_number >= 7
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

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
