<?php

if( empty( $page ) ) {
        print "<HTML><BODY>\n";
        print "<p>I'm sorry, you have not selected a report to run\n";
        print "<p>Please go <a href='index.php'>back</a> and select a report\n";
        print "</body></html>\n";
        exit;
}
require_once("reporter.php");
require_once("./includes.php");

$report = new CORE_Reporter;

$report->setPageSize( 20 );

if( empty( $page_index ) ) {
        $page_index = 1;
}

$title = getTitle( $page );
$report->setDescription( $title );
$report->setFileName( eregi_replace( "\.php$", "", $page ) );

# This is an ugly hack for reports
if( !empty( $sortby ) ) {
        $sortby = stripslashes( $sortby );
}

// Array of args to keep for page changes.
$args_array = array( 'direction','sortby' );

require_once("./$page");

// Launch a special case file download
if( !empty( $download ) ) {
        if( $download == "xls" ) {
                $report->printTSV();
        } elseif ( $download == "csv" ) {
                $report->printCSV();
        } elseif ( $download == "gnumeric" ) {
                $report->printGnumeric();
        } else {
                print "Error, don't know how download type '$download'\n";
        }
        flush();
        exit;
}


$page_total = $report->getTotalPages();
if( $page_index > $page_total ) {
        $page_index = $page_total;
}

$this_page = $PHP_SELF . "?page=$page";
foreach( $args_array as $key ) {
        if( !empty($$key) ) {
                $this_page .= "&$key=" .urlencode($$key);
        }
}

$first_link = "<img src='/images/button_nav_first_tiny_off.gif'" .
              " width=13 height=13 border=0 alt='FIRST'>";
$last_link = "<img src='/images/button_nav_last_tiny_off.gif'" .
              " width=13 height=13 border=0 alt='LAST'>";
$next_link = "<img src='/images/button_nav_next_tiny_off.gif'" .
             " width=13 height=13 border=0 alt='NEXT'>";
$prev_link = "<img src='/images/button_nav_prev_tiny_off.gif'" .
             " width=13 height=13 border=0 alt='PREV'>";

if( $report->getPagesLeft( $page_index ) >= 0 ) {
        $next_link = $this_page . "&page_index=" . ($page_index + 1);
        $next_link = "<a href='$next_link'><img src='/images/button_nav_next_tiny.gif' width=13 height=13 border=0 alt='NEXT'></a>\n";
        $last_link = $this_page . "&page_index=$page_total";
        $last_link = "<a href='$last_link'><img src='/images/button_nav_last_tiny.gif' width=13 height=13 border=0 alt='LAST'></a>\n";
}

if( $page_index > 1 ) {
        $prev_link = $this_page . "&page_index=" . ($page_index - 1);
        $prev_link = "<a href='$prev_link'><img src='/images/button_nav_prev_tiny.gif' width=13 height=13 border=0 alt='PREVIOUS'></a>\n";
        $first_link = $this_page . "&page_index=1";
        $first_link = "<a href='$first_link'><img src='/images/button_nav_first_tiny.gif' width=13 height=13 border=0 alt='FIRST'></a>\n";
}


if( $page_total <= 1 ) {
        $jump_link = "";
        $first_link = $last_link = $next_link = $prev_link = " ";
} else {
        $jump_link = "<select name='page_index' onChange=\"location.href='$this_page&page_index='+this.options[this.selectedIndex].value\">\n";
        for($i = 1 ; $i <= $page_total ; $i++ ) {
                $jump_link .= "<option value='$i' ";
                if( $i == $page_index ) {
                        $jump_link .= "selected";
                }
                $jump_link .= ">$i</option>\n";
        }
        $jump_link .= "</select>\n";
}

$pagesleft = $report->getPagesLeft( $page_index );

$xls_link =  $this_page . "&download=xls";
$csv_link =  $this_page . "&download=csv";
$gnumeric_link = $this_page . "&download=gnumeric";

?>
