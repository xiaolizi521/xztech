<?php
# TITLE: Product Skus

# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='product_sku'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.

$query =<<< SQL
SELECT product_sku, 
       product_name, 
       product_description,
       product_price, 
       product_setup_fee,
       name as "Datacenter"
FROM product_table join datacenter using ( datacenter_number )
SQL;

$report->setQuery("
    $query
    $order_by
    ");

$report->setCountQuery("
    SELECT COUNT(*)
    FROM product_table
    ");

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

        # These add the sort arrows for each field.
        # The first argument is the field name
        # The second argument is the text to replace it with.
        $report->setHeaderReplacement( "product_sku",
                                $report->strArrows('product_sku', $args)
                                .  ' Sku' );
        $report->setHeaderReplacement( "product_name",
                                $report->strArrows('product_name', $args)
                                .  ' Name' );
        $report->setHeaderReplacement( "product_description",
                                $report->strArrows('product_description', $args)
                                .  ' Description' );
        $report->setHeaderReplacement( "product_price",
                                $report->strArrows('product_price', $args)
                                .  ' Monthly' );
        $report->setHeaderReplacement( "product_setup_fee",
                                $report->strArrows('product_setup_fee', $args)
                                .  ' Setup' );
        $report->setHeaderReplacement( "Datacenter",
                                $report->strArrows('"Datacenter"', $args)
                                .  ' Datacenter' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
