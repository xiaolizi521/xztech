<?php
# TITLE: Part Sales
# This report shows the parts that were in servers that have gone online
# or offline in a given period.

# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.

if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='"product_sku"'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
function DateToSec($date)
{
    $date = ltrim($date);
    $arr = explode("-", $date);
    if( count($arr) == 1 ) {
        $arr = explode("/", $date);
    }
    if( count($arr) != 3 ) {
        return "";
    }
    return(mktime(0,0,0, $arr[0], $arr[1], $arr[2]));
}

$date_where1 = $date_where2 = "";
if (!empty($start) and !empty($stop)) {
    $start_sec = DateToSec($start);
    $stop_sec = DateToSec($stop);
    if( !empty($start_sec) and
        !empty($stop_sec) ) {
        $date_where1 = "WHERE sec_finished_order > $start_sec
                        AND sec_finished_order < $stop_sec";
        $date_where2 = "WHERE sec_offline > $start_sec 
                        AND sec_offline < $stop_sec";
    } else {
        // Clear out unparsable variables.
        if( empty( $start_sec ) ) {
            $start = "";
        }
        if( empty( $stop_sec ) ) {
            $stop = "";
        }
    }
}
$args_array[] = 'start';
$args_array[] = 'stop';

#SELECT computer_number, product_sku, COUNT(computer_number) AS online, 
# datacenter_number FROM sales_speed JOIN server USING (computer_number) JOIN
# server_parts USING (computer_number) where datacenter_number = 0;

@$report->setQuery("
    SELECT product_sku, 
        (SELECT product_name || ' - ' || product_description
         FROM product_table sub1
         WHERE ( sub1.product_sku = q1.product_sku
                AND sub1.datacenter_number = q1.datacenter_number )
            OR ( sub1.product_sku = q2.product_sku
                AND sub1.datacenter_number = q1.datacenter_number )
         ) AS product,
        online, offline,
        (NULL_TO_FLOAT8(online) - NULL_TO_FLOAT8(offline)) AS changed,
        datacenter.name as \"Datacenter\"
    FROM (
        SELECT product_sku, COUNT(computer_number) AS online,
               datacenter_number
        FROM sales_speed 
            JOIN server USING (computer_number)
            JOIN server_parts USING (computer_number)
        $date_where1
        GROUP BY product_sku, datacenter_number
        ) q1
        FULL JOIN 
        (
        SELECT product_sku, COUNT(computer_number) AS offline,
               datacenter_number
        FROM offline_servers
            JOIN server USING (computer_number)
            JOIN queue_server_parts USING (computer_number)
        $date_where2
        GROUP BY product_sku, datacenter_number
        ) q2
        JOIN datacenter using (datacenter_number)
        USING (product_sku, datacenter_number)
    " . $order_by
 );

$report->setCountQuery('
    SELECT COUNT(*)
    FROM product_table
    ');

# This sets the size of a single page.  We generally don't want to
# let the user alter this
$report->setPageSize(100);

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
    # You need both of these globals!!
    global $report, $page_index, $page, $start, $stop;

    # These are the default args.  Useful for calling your page
    # again.
    $args = "page=$page&page_index=$page_index&start=$start&stop=$stop";

    # These add the sort arrows for each field.
    # The first argument is the field name
    # The second argument is the text to replace it with.
    $report->setHeaderReplacement( "product_sku",
                                    $report->strArrows('"product_sku"',$args).
                                    ' Product Sku' );
    $report->setHeaderReplacement( "online",
                                    $report->strArrows('"online"',$args).
                                    ' Online' );
    $report->setHeaderReplacement( "offline",
                                    $report->strArrows('"offline"',$args).
                                    ' Offline' );
    $report->setHeaderReplacement( "product",
                                    $report->strArrows('"product"',$args).
                                    ' Product' );
    $report->setHeaderReplacement( "changed",
                                    $report->strArrows('"changed"',$args).
                                    ' Changed' );
    ?>
    <h3>Part Sales:</h3>
    <i>(Use the Excel report to get more than the first 300 results)</i>
    <br>
    <form action=show.php>
    <table><tr>
    <?
    print("<tr><td align=center colspan=2>Limit to online dates between");
    print("<tr><td>Start (mm-dd-yyyy):</td><td><input type=text name=start value=\""
        . @$start ."\">");
    print("<tr><td>Stop:</td><td><input type=text name=stop value=\""
        . @$stop . "\">");

    ?>
    </TD></TR>
    <TR><TD></TD><TD>
    <input type=submit value=Search>
    </td></tr>
    </table>
    <input type=hidden name=page value="part_sales_report.php">
    </form>
    <?
    if (empty($start) and empty($stop)) {
        return;
    }

    # This does the actual printing of the HTML
    $report->printHTML( $page_index );
}


?>
