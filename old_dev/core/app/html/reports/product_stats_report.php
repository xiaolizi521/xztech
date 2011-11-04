<?php
# TITLE: Product Stats
# $Id: product_stats_report.php 1.4 02/01/10 14:21:35-00:00 egrubbs@ $

# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='"Category"'; }

$thesort = "ORDER BY $sortby $direction";

if( $sortby != '"Category"' ) {
        $thesort .= ', "Category" '.$direction;
}

if( $sortby != '"Datacenter"' ) {
        $thesort .= ', "Datacenter" '.$direction;
}

if( $sortby != '"Name"' ) {
        $thesort .= ', "Name" '.$direction;
}

if( $sortby != '"SKU"' ) {
        $thesort .= ', "SKU" '.$direction;
}

if( empty($regex) ) { 
        $regex = ""; 
        $theregex = "";
        $thecountregex = "";
} else {
        $theregex = "WHERE product_description ~* '$regex'";
        $thecountregex = "AND product_description ~* '$regex'";
}

# This sets the pages size.
$report->setPageSize( 30 );

#$GLOBALS['print_sql'] = true;

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.
$report->setQuery( '
SELECT
  product_name as "Category",
  datacenter.name as "Datacenter",
  product_sku as "SKU",
  product_description as "Name",
  online as "# Online",
  offline as "# Offline"
FROM
  product_table
  JOIN datacenter using (datacenter_number)
  JOIN (
    SELECT
      product_sku,
      count(status_number) as "online"
    FROM
      server_parts 
      JOIN server_status_all using (computer_number)
    WHERE
      status_number > 7
    GROUP BY product_sku
  ) as counton
  using (product_sku) 
  JOIN (
    SELECT
      product_sku,
      count(status_number) as "offline"
    FROM
      server_parts 
      JOIN server_status_all using (computer_number)
    WHERE
      status_number < 0
    GROUP BY product_sku
  ) as countoff
  using (product_sku) 
'.
$theregex
."\n".
$thesort
 );

# This isn't normally needed, but in somecases, it is.  Reporter tries
# to figure out how to do a count(*) but for some queries (especially 
# ones with multiple selects) it's too hard to figure out.
$report->setCountQuery( '
SELECT
  count(product_sku)
FROM
  server_status_all
  JOIN server_parts using (computer_number)
  JOIN product_table using (product_sku)
WHERE (
  status_number > 7
 OR
  status_number < 0
 )
'.$thecountregex.'
GROUP BY product_sku
' );

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
        # You need both of these globals!!
        global $report, $page_index, $page, $regex;
        global $PHP_SELF, $sortby, $direction;

        # The Warning
        echo "<br><b><font color='red'>Warning</font></b>: The offline number is very inaccurate.  It represents the hardware present in computers brought offline. It doesn't account for individual products removed from a computer.<br>";

        echo "<form action='$PHP_SELF'>\n";
        echo "<input type='hidden' name='page' value='$page'>\n";
        echo "<input type='hidden' name='page_index' value='$page_index'>\n";
        echo "<input type='hidden' name='sortby' value='$sortby'>\n";
        echo "<input type='hidden' name='direction' value='$direction'>\n";
        echo "Search for Name (regex):\n";
        echo "<input type='text' name='regex' value='$regex'>\n";
        echo "<input type='submit' value='Submit'>\n";
        echo "</form>\n";

        $args = "page=$page&page_index=$page_index&regex=$regex";

        $headers = array('Category', 'Datacenter', 'SKU', 'Name', '# Online', '# Offilen');
        foreach( $headers as $name ) {
                $report->setHeaderReplacement( $name,
                              $report->strArrows('"'.$name.'"',$args).
                                       " $name" );
        }

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );

}


?>