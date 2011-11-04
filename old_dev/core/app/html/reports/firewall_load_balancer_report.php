<?php
# TITLE: Firewall/Load-Balancer Servers
# $Id: firewall_load_balancer_report.php 1.8 02/01/10 14:21:35-00:00 egrubbs@ $

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.


# Build the sort ordering part of the query.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='"Customer", "Computer"'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";

$query = <<< SQL
SELECT 
    "Customer",
    "Computer",
    MAX("Net Device") AS "Net Device",
    server_location("Computer") as "Location",
    device_number
FROM (
    SELECT DISTINCT ON (
            "Customer",
            "Computer",
            "Net Device"
            )
        fw.customer_number AS "Customer",
        server.computer_number AS "Computer",
        CASE 
            WHEN behind.device_number > 0 
                    THEN TEXT(behind.device_number)
            WHEN fw.computer_number = server.computer_number
                    THEN TEXTCAT(fw.computer_number, ' Self')
            ELSE ''
        END AS "Net Device",
        device_number
    FROM 
        server_parts fwpart 
        JOIN product_os USING (product_sku)
        JOIN server fw USING (computer_number)
        JOIN server server USING (customer_number)
        LEFT JOIN computer_behind_network_device behind 
            ON server.computer_number = behind.computer_number
                AND fw.computer_number = behind.device_number
    WHERE os IN ('Firewall - Cisco ASA', 'Firewall - Cisco PIX', 'Load-Balancer')
    ) AS FOO
GROUP BY "Customer", "Computer", device_number
$order_by
SQL;

$count_query = <<< SQL
SELECT COUNT(*)
FROM (
    SELECT DISTINCT server.computer_number, behind.device_number
    FROM 
        server_parts fwpart 
        JOIN server fw USING (computer_number)
        JOIN server server USING (customer_number)
        LEFT JOIN computer_behind_network_device behind
            ON server.computer_number = behind.computer_number
    WHERE product_sku IN (101419, 101420)
    ) AS FOO
SQL;

$report->setQuery($query);
$report->setCountQuery($count_query);
$report->ignoreField( 'device_number' );

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

        if (in_dept('NETWORK')) {
            # This turns the Name Field into a link to the correct contact.
            $report->setFieldRule( 'Net Device',
                '<a href="/tools/organize_net_device.php?'
                . 'device_number=%device_number">%Net Device</a>' );
        }

        $report->setFieldRule( 'Location', '<TT>%Location</TT>' );

        $report->setHeaderReplacement( "Net Device",
            $report->strArrows('"Customer", "Net Device", "Computer"',
                $args).
            '&nbsp;Net&nbsp;Device' );
        $report->setHeaderReplacement( "Computer",
            $report->strArrows('"Customer", "Computer"',$args).
            '&nbsp;Computer' );

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
