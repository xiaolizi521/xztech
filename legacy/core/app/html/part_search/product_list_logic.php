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
$order_by = "$sortby $direction";
if( $sortby == '"Acct #"' ) {
    $order_by .= ', computer_number '.$direction;
}
if( $sortby == '"Account Exec."' ) {
    $order_by .= ', "Acct #" '.$direction;
    $order_by .= ', computer_number '.$direction;
}
if( $sortby != '"Product"' ) {
    $order_by .= ', "Product" '.$direction;
}

$report->setDescription( $title );
$report->setFileName( "part_search_products" );

$groups = GroupParts($SESSION_parts);


$where = BuildProductWhere( $groups[$id] );
if( !empty($where) ) {
    $where .= " AND";
}
$where .= " status_number > 7 ";

$sql = '
SELECT 
  customer_number as "Acct #",
  computer_number as "Computer#",
  server_parts.product_price as "Monthly",
  date(server_parts.sec_created::abstime) as "Date Bought",
  "SLA",
  ae as "Account Exec.",
  product_table.product_name
  || \' '.$show_datacenter.' [#\' ||
  product_sku
  || \'] \' ||
  product_description as "Product"
FROM
  server
  join datacenter using (datacenter_number)
  join "xref_customer_number_Account" using (customer_number)
  join (
        SELECT DISTINCT ON ( aid )
            "ACCT_val_AccountRoleID",
            "ACCT_AccountID" as aid,
            "FirstName" || \' \' || "LastName" as ae,
            "ACCT_val_SLAType"."Name" as "SLA"
        FROM
            "ACCT_xref_Account_Contact_AccountRole"
            join "ACCT_Account" on ("ACCT_AccountID" = "ACCT_Account"."ID")
            join "CONT_Contact" on ("CONT_Contact"."ID"="CONT_ContactID")
            join "CONT_Person" on ("CONT_Person"."ID" = "CONT_PersonID")
            join "ACCT_val_SLAType" on ("ACCT_val_SLATypeID" = "ACCT_val_SLAType"."ID")
        WHERE "ACCT_val_AccountRoleID" = '.ACCOUNT_ROLE_ACCOUNT_EXECUTIVE.') as AEView
  on (aid = "ACCT_AccountID")
  join server_parts using (computer_number)
  join product_table using (product_sku,datacenter_number)
WHERE 

'.$where.'
'.$and_datacenter.'

ORDER BY '.$order_by;
$report->setQuery( $sql );
$report->setCountQuery( 'select count(product_sku) from server join server_parts using (computer_number) where '.$where );

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

$xls_link =  $this_page . "&download=xls";
$csv_link =  $this_page . "&download=csv";
$gnumeric_link = $this_page . "&download=gnumeric";

function printreport() {
    global $report, $page_index, $id, $dateargs;
    $args = "id=$id$dateargs";

    $report->setHeaderReplacement( "Acct #", 
                                   $report->strArrows('"Acct #"',$args).
                                   ' Acct #' );
    $report->setHeaderReplacement( "Computer#", 
                                   $report->strArrows("computer_number",$args).
                                   ' Computer#' );
    $report->setHeaderReplacement( "Account Exec.", 
                                   $report->strArrows('"Account Exec."',$args).
                                   ' Acct Exec' );
    $report->setHeaderReplacement( "Monthly", 
                                   $report->strArrows('"Monthly"',$args).
                                   ' Monthly' );
    $report->setHeaderReplacement( "SLA", 
                                   $report->strArrows('"SLA"',$args).
                                   ' SLA' );
    $report->setHeaderReplacement( "Product", 
                                   $report->strArrows('"Product"',$args).
                                   ' Product' );
    $report->setHeaderReplacement( "Date Bought", 
                                   $report->strArrows('"Date Bought"',$args).
                                   ' Bought' );

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
