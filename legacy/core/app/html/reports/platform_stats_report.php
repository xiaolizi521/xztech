<?php
# TITLE: Platform Stats by Date
# $Id: platform_stats_report.php 1.10 04/05/13 11:57:26-05:00 choltje@lego.gerf.net $

# DO NOT USE THIS AS AN EXAMPLE!  IT SUCKS AS AN EXAMPLE!

$report_db = getReportDB();

require_once("CORE_app.php");

if( !empty( $report ) ) {
        $report->setQuery( '' );
        $report->_total_pages = 1;
        $NO_DOWNLOAD = true;
}

if( empty( $datacenter ) ) {
        $datacenter = 0;
}

/* Was a date passed in? */
if( !empty( $date ) ) {
        $array = explode( '/', $date );
        if( !empty( $array[0] ) ) {
                $month = $array[0];
        }
        if( !empty( $array[1] ) ) {
                $day = $array[1];
        }
        if( !empty( $array[2] ) ) {
                $year = $array[2];
        }
}

/* Calculate the timestamp */
$stamp=time() - (60 * 60 * 24) ;
global $REPORTDB_IS_AVAILABLE;
if( $REPORTDB_IS_AVAILABLE ) {
    $stamp = $stamp - 60*60*24;
}

if( empty($month) ) {
        $month=strftime("%m",$stamp);
}
if( empty( $day ) ) {
        $day=strftime("%d",$stamp);
}
if( empty( $year ) ) {
        $year=strftime("%Y",$stamp);
}
$stamp = "$month/$day/$year";

/*
 *  Product SKUs that we are interested in
 */

$os_result = $report_db->SubmitQuery('
    SELECT product_sku, os
    FROM product_os
    WHERE is_real_server
    ');
$OS = array();
for ($i = 0; $i < $os_result->numRows(); $i++) {
    $key = $os_result->getResult($i, 0);
    $value = $os_result->getResult($i, 1);
    $OS[$key] = $value;
}

$skus = implode( ",", array_keys( $OS ) );

/*
 * Get the Data
 */
if( empty( $datacenter ) ) {
        $dc_where = "";
} else {
        $dc_where = "AND datacenter_number = $datacenter";
}

$query = '
SELECT
  product_sku,
  sum(final_monthly),
  count(computer_number)
FROM
  server_parts join server using (computer_number)
  join sales_speed using (computer_number)
WHERE
    sec_finished_order > 0
  AND
    sec_finished_order::abstime <= '."'$stamp'".'::timestamptz
  AND
    product_sku in ('.$skus.')
  AND status_number >= ' . STATUS_ONLINE . '
  '.$dc_where.'
GROUP BY product_sku
';

$result = $report_db->SubmitQuery( $query );

$totals = array( 'count' => 0,
                 'percent' => 0,
                 'money' => 0,
                 'perserver' => 0);

$data = array();
for( $i = 0 ; $i < $result->numRows(); $i++ ) {
        $sku = $result->getResult( $i, 'product_sku' );
        $sum = $result->getResult( $i, 'sum' );
        $count = $result->getResult( $i, 'count' );
        $key = $OS[$sku];

        if( empty( $data[$key] ) ) {
                $data[$key] = array( 'count' => 0,
                                     'money' => 0 );
        }
        $data[$key]['count'] += $count;
        $data[$key]['money'] += $sum;
        $totals['count'] += $count;
        $totals['money'] += $sum;
}
ksort( $data );

# Summing totals
foreach( $data as $key => $val ) {
        $percent = ceil( $val['count']/$totals['count'] * 10000 )/100;
        $data[$key]['percent'] = $percent;
        $totals['percent'] += $data[$key]['percent'];

        $perserver = ceil( $val['money']/$val['count'] * 100 )/100;
        $data[$key]['perserver'] = $perserver;
}
$totals['perserver'] = ceil( $totals['money']/$totals['count'] * 100 )/100;

/*
 * Collect Datacenter Data
 */
$result = $report_db->SubmitQuery( 'select name, datacenter_number as number from datacenter where "Active" = TRUE' );
for( $i = 0 ; $i < $result->numRows(); $i++ ) {
        $datacenters[$result->getResult( $i, 'number' )] =
                 $result->getResult( $i, 'name' );
}
$datacenters[0] = "All Offices";
ksort( $datacenters );


if( empty( $report ) ) {
        Header("Pragma:");
        Header("Content-Disposition: inline; filename=platform_stats.xls");
        Header("Content-Description: Admin Tool Generated Data");
        Header("Content-type: application/vnd.ms-excel; name='Platform_Stats'");
        flush();

        $rows = array();
        array_push( $rows, array( $datacenters[$datacenter] ) );
        array_push( $rows, array( 'Platform', 'Total', '% of Total',
                                  'Monthly Revenue', 'Per Server Revenue' )
                );
        foreach( $data as $key => $val ) {
                array_push( $rows, array( $key, $val['count'], $val['percent'],
                                          $val['money'], $val['perserver'] )
                        );
        }

        array_push( $rows, array( "Total", $totals['count'], $totals['percent'],
                                  $totals['money'], $totals['perserver'] )
                );

        foreach( $rows as $row ) {
                $count = 0;
                foreach( $row as $item ) {
                        if( $count > 0 ) {
                                print "\t";
                        } else {
                                $count = 1;
                        }
                        print '"'.$item.'"';
                }
                print "\n";
        }
}

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
        global $datacenter, $PHP_SELF, $page;
        global $totals, $data, $stamp, $datacenters;
        global $date, $month, $day, $year;

        $report_db = getReportDB();
        $datevalue = "$month/$day/$year";

        /*
         * Print out datacenter selector
         */
        print "<center><TABLE border='0'>\n";
        print "<tr>\n";
        foreach( $datacenters as $id => $name ) {
                print " <td>";
                if( $id == $datacenter ) {
                        print "<b>$name</b>";
                } else {
                        print "<a href=";
                        print "'$PHP_SELF?page=$page&datacenter=$id&date=$datevalue'";
                        print ">$name</a>";
                }
                print "</td>\n";
        }
        print "</tr>\n";
        print "</TABLE></center>\n";

        echo "<form name='dateform' action='$PHP_SELF'>\n";
        echo "<input type='hidden' name='page' value='$page'>\n";
        if( !empty($datacenter) ) {
                echo "<input type='hidden' name='datacenter' value='$datacenter'>\n";
        }
        echo '
<center>
<input type="text" name="date" size="15" value="'.$datevalue.'">
<a href="javascript:show_calendar(\'dateform.date\');"
   onmouseover="window.status=\'Pick a date\';return true;"
   onmouseout="window.status=\'\';return true;"><img 
   src="/images/show-calendar.gif" width="24" height="22" border="0" valign="middle"></a>
<input type="submit" value="Set Date">
</center>
</form>
';

        /*
         * Main Data Table
         */
        print "<BR>\n";
        print "<TABLE border='0' class='reporter'>\n";
        print "<TR><th colspan='5'>Data as of $stamp (inclusive)</th></TR>\n";
        $c = 'class="reporter"';
        $co = 'class="reporterodd"';
        ## Print Headers
        print "<TR $c>\n";
        print "  <th $c>Platform</th>\n";
        print "  <th $c>Count</th>\n";
        print "  <th $c>Percent</th>\n";
        print "  <th $c>Monthly Revenue</th>\n";
        print "  <th $c>Per Server</th>\n";
        print "</TR>\n";
       

        $tog = 0;
        foreach( $data as $key => $val ) {
                $count = number_format( $val['count'] );
                $percent = number_format( $val['percent'], 2 );
                $money = number_format( $val['money'], 2 );
                $perserver = number_format( $val['perserver'], 2 );
                if( $tog ) {
                        $cl = $c;
                } else {
                        $cl = $co;
                }
                $tog = !$tog;
                print "<TR $cl>\n";
                print "  <td $cl>$key</td>\n";
                print "  <td $cl align='right'>$count</td>\n";
                print "  <td $cl align='right'>$percent%</td>\n";
                print "  <td $cl align='right'>\$$money</td>\n";
                print "  <td $cl align='right'>\$$perserver</td>\n";
                print "</TR>\n";
        }

        print "<tr><td colspan='5'><hr noshade></td></tr>\n";
        ## Print Grand Totals
        $count = number_format( $totals['count'] );
        $count = $totals['count'];
        $money = number_format( $totals['money'], 2 );
        $percent = number_format( $totals['percent'], 2 );
        $perserver = number_format( $totals['perserver'], 2 );
        print "<TR>\n";
        print "  <td>Totals</td>\n";
        print "  <td align='right'>$count</td>\n";
        print "  <td align='right'>100% <!-- $percent% --></td>\n";
        print "  <td align='right'>\$$money</td>\n";
        print "  <td align='right'>\$$perserver</td>\n";
        print "</TR>\n";
                
        print "</TABLE><br>\n";

        print "<a href='$page'>";
        print "<img src='/tools/assets/images/i-msexcel-32.gif' ";
        print "ALT='Export to Excel' border='0'>";
        print "</a>\n";
}


?>
