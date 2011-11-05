<?php
# TITLE: Accounts with Consolidated Server Bandwidth
# $Id: consolidated_bandwidth_report.php 1.3 02/01/10 14:21:35-00:00 egrubbs@ $


$query = <<< SQL
SELECT DISTINCT
    fw.customer_number AS "Account",
    server.computer_number AS "Computer",
    determine_os(server.computer_number) AS "OS"
FROM 
    server_parts fwpart 
    JOIN product_os USING (product_sku)
    JOIN server fw USING (computer_number)
    JOIN server server USING (customer_number)
WHERE os IN ('Firewall - Cisco ASA', 'Firewall - Cisco PIX', 'Load-Balancer')
    AND fw.status_number >= 12
    AND server.status_number >= 12
    AND server.computer_number NOT IN (
        SELECT computer_number
        FROM computer_behind_network_device sub1
        WHERE sub1.computer_number = server.computer_number
        )
ORDER BY "Account", "Computer"
SQL;

$count_query = <<< SQL
SELECT COUNT(*)
FROM 
    server_parts fwpart 
    JOIN product_os USING (product_sku)
    JOIN server fw USING (computer_number)
    JOIN server server USING (customer_number)
WHERE os IN ('Firewall - Cisco ASA', 'Firewall - Cisco PIX', 'Load-Balancer')
    AND fw.status_number >= 12
    AND server.computer_number NOT IN (
        SELECT sub1.computer_number
        FROM computer_behind_network_device sub1
            JOIN server sub2 ON device_number = sub2.computer_number
        WHERE sub1.computer_number = server.computer_number
            AND sub2.status_number >= 12
        )
SQL;

$report->setQuery($query);
$report->setCountQuery($count_query);

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

        # This does the actual printing of the HTML
        $report->printHTML( $page_index );
}


?>
