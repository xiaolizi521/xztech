<?php
# TITLE: The Example Report
# $Id: example_report.demo.php 1.2 01/08/06 21:27:02-00:00 docwhat@ $

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
$report->setQuery( '
SELECT "Title",
       "FirstName",
       "LastName",
       date("Created") as "Created",
       "CONT_Contact"."ID" as cid
FROM "CONT_Contact" join "CONT_Person"
     on ( "CONT_PersonID" = "CONT_Person"."ID" )
'.$order_by
 );

# This is to Mix "FirstName" and "LastName" (see the query above) to a
# field named "Name".  This will be mixed by a ' ' (space).
# You can use this to mix any number of fields with a common seperator.
$report->addMix( 'Name', ' ', 'FirstName', 'LastName' );

# This says to ignore (not print) the field CID.  You can then use this
# for generating info or links behind the scenes.
$report->ignoreField( 'cid' );

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
        $report->setFieldRule( 'Name',
                            '<a href="' . $GLOBALS["roster_url"] . 'view_employee.jsf?contact_id=%cid">%FirstName&nbsp;%LastName</a>' );

        # These add the sort arrows for each field.
        # The first argument is the field name
        # The second argument is the text to replace it with.
        $report->setHeaderReplacement( "Name",
                                       $report->strArrows('"LastName"',$args).
                                       ' Name' );
        $report->setHeaderReplacement( "Title",
                                       $report->strArrows('"Title"',$args).
                                       ' Title' );
        $report->setHeaderReplacement( "Created",
                                       $report->strArrows('"Created"',$args).
                                       ' Created' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>