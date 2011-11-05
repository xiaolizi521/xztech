<?php
# TITLE: Active Servers
# $Id: active_server_report.php 1.1 01/09/13 20:33:32-00:00 docwhat@ $

# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='acct'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";

# Add other fields to sorting to make it look pretty.
if( $sortby != 'acct' ) {
        $order_by .= ", acct $direction";
}
if( $sortby != 'comp' ) {
        $order_by .= ", comp $direction";
}


# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
$report->setQuery( '
select 
server.customer_number as acct,
computer_number as comp,
status
from 
  server
  join status_options using (status_number)
where
  status_number >= 7
'.$order_by );

# This sets the size of a single page.  We generally don't want to
# let the user alter this
$report->setPageSize( 60 );

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
        $report->setFieldRule( 'acct',
                            '<a href="/tools/quick_find.php3?command=FIND_CUSTOMER&customer_number=%acct">%acct</a>' );
        $report->setFieldRule( 'comp',
                            '<a href="/tools/quick_find.php3?command=FIND_COMPUTER&computer_number=%comp">%comp</a>' );

        # These add the sort arrows for each field.
        # The first argument is the field name
        # The second argument is the text to replace it with.
        $report->setHeaderReplacement( "acct",
                                       $report->strArrows('acct',$args).
                                       ' Acct #' );
        $report->setHeaderReplacement( "comp",
                                       $report->strArrows('comp',$args).
                                       ' Comp #' );
        $report->setHeaderReplacement( "status",
                                       $report->strArrows('status',$args).
                                       ' Status' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
