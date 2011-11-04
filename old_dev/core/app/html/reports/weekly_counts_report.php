<?php
# TITLE: Weekly Computer Counts
# $Id: weekly_counts_report.php 1.1 01/07/11 20:54:54-00:00 docwhat@ $

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

class MyReporter extends CORE_Reporter {

        function _printReport( $index = 1, $type = "doesn't matter" ) {
                $start = strtotime ("15 December 2000");
                $now = time();
                $increment = 60*60*24*7;

                $css = $this->_css;

                echo "<TABLE border='0' cellspacing='0' cellpadding='3' class='$css'>\n";
                echo "<TR class='$css'>";
                echo "<TH class='$css'> Week of </TH>";
                echo "<TH class='$css'> #Online </TH>";
                echo "</TR>\n";

                $count = 0;
                for( $i = $start ; $i < $now ; $i += $increment ) {
                        if( $count++ % 2 ) {
                                $rowcss = $css;
                        } else {
                                $rowcss = $css."odd";
                        }
                        echo "<TR class='$rowcss'>";
                        $value = $this->_db->GetVal('
SELECT count(computer_number)
FROM   sales_speed left join offline_servers using (computer_number)
WHERE
      NOT sales_speed.sec_finished_order = 0
 AND  sales_speed.sec_finished_order <= '.$i.'
 AND  (  offline_servers.sec_created > '.$i.'
       OR offline_servers.sec_created is null )
'); 
                        echo "<TD class='$rowcss'>".strftime('%Y %m %d', $i)."</TD>";
                        echo "<TD class='$rowcss' align='center'>$value</TD>";
                        echo "</TR>\n";
                }

                echo "</TABLE>\n";
        }

}

$report = new MyReporter;

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.
$report->setQuery( '  ');

$NO_DOWNLOAD=true;

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