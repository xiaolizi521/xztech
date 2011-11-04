<?php

require_once("common.php");
require_once("helpers.php");

if( empty($id) ) {
    $id = 0;
}

require_once("reporter.php");

$report = new CORE_Reporter( $report_db );

$report->setPageSize( 60 );

if( empty( $page_index ) ) {
        $page_index = 1;
}

if( empty( $direction ) ) {
    $direction="ASC";
}
if( empty( $sortby ) ) {
    $sortby = "computer_number";
} else {
    $sortby = ereg_replace( '[\\]', "", $sortby );
}

$report->setDescription( $title );
$report->setFileName( "part_search_computers" );

$groups = GroupParts($SESSION_parts);
$report->setCountQuery( BuildSQL( $groups[$id] ) );

//TODO: this should not be referencing 'ACCT_val_AccountTypeID', or Team
//NOTE this report is going away, so ignore this TODO
$sql = BuildSQL( $groups[$id], false );
$sql = '
SELECT 
  computer_number as "Computer#",
  extract(epoch from sec_finished_order::abstime) as "Online By",
  "Team",
  "ACCT_val_AccountType"."Name" as "Division",
  "ACCT_val_SLAType"."Name" as "SLA",
  datacenter.name as "DataCenter"
FROM server
  join datacenter using (datacenter_number)
  join (select computer_number, sec_finished_order from sales_speed) as Online using (computer_number)
  join "xref_customer_number_Account" using (customer_number)
  join "ACCT_Account" on ("ACCT_AccountID" = "ACCT_Account"."ID")
  join "ACCT_val_SLAType" on ("ACCT_val_SLATypeID" = "ACCT_val_SLAType"."ID")
  join (
     SELECT "ACCT_AccountID" as aid, 
            "ACCT_Team"."Name" as "Team"
     FROM "ACCT_xref_Account_Team_AccountRole"
       join "ACCT_Team" on ("ACCT_xref_Account_Team_AccountRole"."ACCT_TeamID" = "ACCT_Team"."ID")
     WHERE "ACCT_val_AccountRoleID" = '.ACCOUNT_ROLE_SUPPORT.'
     GROUP BY "ACCT_AccountID", "ACCT_Team"."Name"
     ) as TeamView on (aid = "ACCT_Account"."ID")
  join "ACCT_val_AccountType" on ("ACCT_val_AccountTypeID" = "ACCT_val_AccountType"."ID")
WHERE computer_number in ('.$sql.') 
  '.$and_datacenter.'
  AND status_number > 7
ORDER BY '.$sortby.' '.$direction;
$report->setQuery( $sql );

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

$this_page = "$PHP_SELF?id=$id";

$first_link = "<img src='/images/button_nav_first_tiny_off.gif'" .
              " width=13 height=13 border=0 alt='FIRST'>";
$last_link = "<img src='/images/button_nav_last_tiny_off.gif'" .
              " width=13 height=13 border=0 alt='LAST'>";
$next_link = "<img src='/images/button_nav_next_tiny_off.gif'" .
             " width=13 height=13 border=0 alt='NEXT'>";
$prev_link = "<img src='/images/button_nav_prev_tiny_off.gif'" .
             " width=13 height=13 border=0 alt='PREV'>";

if( $report->getPagesLeft( $page_index ) ) {
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

$xls_link = $this_page . "&download=xls";
$csv_link =  $this_page . "&download=csv";
$gnumeric_link = $this_page . "&download=gnumeric";

function printreport() {
    global $report, $page_index, $id, $dateargs;
    $args = "id=$id$dateargs";

    $report->setHeaderReplacement( "Computer#", 
                                   $report->strArrows("computer_number",$args).
                                   ' Computer#' );
    $report->setHeaderReplacement( "Online By", 
                                   $report->strArrows("sec_finished_order",$args).
                                   ' Online By' );
    $report->setHeaderReplacement( "Team", 
                                   $report->strArrows('"Team"',$args).
                                   ' Team' );
    $report->setHeaderReplacement( "Division", 
                                   $report->strArrows('"Division"',$args).
                                   ' Division' );
    $report->setHeaderReplacement( "SLA", 
                                   $report->strArrows('"SLA"',$args).
                                   ' SLA' );
    $report->setHeaderReplacement( "DataCenter", 
                                   $report->strArrows("datacenter.name",$args).
                                   ' DataCenter' );

    # %3D is an '='
    $report->setFieldRule( 'Computer#', 
                           '<a href="/ACCT_main_workspace_page.php?computer_number=%Computer#">%Computer#</a>' );

    ptime();
    $report->printHTML( $page_index );
    ptime();
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
