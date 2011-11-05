<?php
require_once("CORE_app.php");

if( empty($startdate) or $startdate == "01/01/1997" ) {
    unset( $startdate );
}
if( empty($enddate) or $enddate == "Now" ) {
    unset( $enddate );
}

require_once("common.php");

if( $first_time ) {
    ForceReload("build_page.php?$dateargs");
    exit;
}

function QuickTable( $array, $border=0, $bgcolor='#FFFFFF') {
    echo "\n";
    echo "<table border='$border' bgcolor='$bgcolor'>\n";
    foreach( $array as $th => $td ) {
        echo "  <tr>\n";
        echo "    <td>$th</td>\n";
        echo "    <td>$td</td>\n";
        echo "  </tr>\n";
    }
    echo "</table>\n";
}

function PrintOneGroup( $group, $num = -1 ) {
    global $report_db, $dotime, $debug, $REPORTDB_IS_AVAILABLE;

    if( !empty( $group[0]['logic'] ) ) {
        unset( $group[0]['logic'] );
    }
    ptime();
    $sql = BuildSQL( $group );
    $count = $report_db->GetVal( $sql );

    $prodwhere = BuildProductWhere( $group );
    $product_sum = $report_db->GetVal( 'SELECT count(product_sku) FROM server_parts WHERE '.$prodwhere );
    $monthly_sum = $report_db->GetVal( 'SELECT sum(product_price) FROM server_parts WHERE '.$prodwhere );
    $monthly_avg = $report_db->GetVal( 'SELECT avg(product_price) FROM server_parts WHERE '.$prodwhere );

    if( $dotime or !empty($debug)) {
        if( empty($REPORTDB_IS_AVAILABLE) ) {
            echo "DB: Global Database<br>\n";
        } else {
            echo "DB: Reporting Database<br>\n";
        }
        echo "Query: <pre>$sql;</pre>\n";
        echo "ProdWhere: <pre>$prodwhere;</pre>\n";
    }
    ptime();
    if( $num >= 0 ) {
        global $dateargs;
        $serv_link = "(<a href='list_page.php?id=$num$dateargs'>view</a>)";
        $prod_link = "(<a href='product_list_page.php?id=$num$dateargs'>view</a>)";
    } else {
        $serv_link = $prod_link = "";
    }
    $array = array( "Servers &nbsp; $serv_link" => number_format($count),
                    "Products &nbsp; $prod_link" =>number_format($product_sum),
                    'Monthly Product Revenue' => number_format($monthly_sum,2),
                    'Average Revenue per Product' => number_format($monthly_avg,2) );

    # This is for caching the numbers, so that if there is only
    # one group, we can store the total instead of recalculating it.
    global $servers, $monthly, $products;
    if( !is_array( $servers ) ) { $servers = array(); }
    if( !is_array( $monthly ) ) { $monthly = array(); }
    if( !is_array( $products ) ) { $products = array(); }
    $servers[$num] = $count;
    $monthly[$num] = $monthly_sum;
    $products[$num] = $product_sum;

    echo "<br clear='all'>\n";
    PrintList( $group, false );
    echo "<br clear='all'>\n";
    QuickTable( $array, 1, '#EFEFFF' );
}

function PrintSummary() {
    global $SESSION_parts;

    if( empty( $SESSION_parts) or count($SESSION_parts) <= 0 ) {
        echo "No Query Built<br>\n";
        return;
    }

    $groups = GroupParts( $SESSION_parts );
    for( $i=0; $i<count($groups); $i++ ) {
        if( $i != 0 ) {
            echo "<hr noshade>\n";
        }
        PrintOneGroup( $groups[$i], $i );
    }
}

function PrintTotals() {
    global $report_db, $SESSION_parts;
    global $servers, $products, $monthly;

    echo "<h2>Total for all</h2>\n";

    if( is_array( $servers ) and count($servers) == 1 ) {
        $tot_servers = array_pop($servers);
    } else {
        return;
    }

    $tot_products = 0;
    if( is_array( $products ) ) {
        foreach( $products as $v ) {
            $tot_products += $v;
        }
    }

    $tot_monthly = 0;
    if( is_array( $monthly ) ) {
        foreach( $monthly as $v ) {
            $tot_monthly += $v;
        }
    }

    QuickTable( array( 
        'Total Servers' => number_format($tot_servers),
        'Total Products <a href="#warning">*</a>' => number_format($tot_products),
        'Total Monthly Product Revenue <a href="#warning">*</a>' => number_format($tot_monthly,2),
        ),
                1, '#FFE0E0' );
    ttime();

    echo '<p> <a name="warning">*</a> Please note that these numbers can be thrown off in various ways and are nothing more than a direct summation of the numbers on this page (not the database).  Using complex OR and AND queries where products and servers overlap (ie, are counted twice) will make these numbers very wrong.'."\n";
}

#$print_sql = true;
// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
