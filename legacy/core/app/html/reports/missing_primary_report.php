<?php
# TITLE: CORE Team: Missing Primary Accounts
# $Id: missing_primary_report.php 1.1 01/09/14 18:04:17-00:00 docwhat@ $

if( !in_dept("CORE") ) {
        trigger_error("You are not authorized", FATAL);
}

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

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
$report->setQuery( '
select 
  customer_number as acct, 
  userid,
  "Text" 
from "xref_customer_number_Account" 
join ( select "ID" from "ACCT_Account" 
       except 
       select "ACCT_AccountID" as "ID" 
       from "ACCT_xref_Account_Contact_AccountRole" 
       where "ACCT_val_AccountRoleID" = ' . ACCOUNT_ROLE_PRIMARY . ' ) as foo 
on ("ID" = "ACCT_AccountID") 
join "ACCT_xref_Account_Note" 
using ("ACCT_AccountID") 
join "NOTE_Note" 
on ("NOTE_NoteID" = "NOTE_Note"."ID") 
join "xref_employee_number_Contact" 
using ("CONT_ContactID") 
join employee_authorization 
using (employee_number)
'.$order_by );

$report->setCountQuery( 'select 300' );

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

        # These add the sort arrows for each field.
        # The first argument is the field name
        # The second argument is the text to replace it with.
        $report->setHeaderReplacement( "acct",
                                       $report->strArrows('acct',$args).
                                       ' Acct #' );
        $report->setHeaderReplacement( "userid",
                                       $report->strArrows('userid',$args).
                                       ' Userid' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>