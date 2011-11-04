<?php

require_once("CORE_app.php");

// Turned off by request of Ruth.
// These numbers don't match the numbers on stats.rackspace.com
// Until we can fix it, it'll be turned off
trigger_error( "Turned off until we have time to fix bugs.  Only submit a bug if you need access to this now.", FATAL );

$accounts = array();

#$print_sql = true;

# bw query
for($month = 6; $month > 0; $month--) {
    $next_month = $month - 1; // since the query is working backwards
    $date = $GLOBAL_db->getVal("
        SELECT date_trunc('month', 
                (now() - interval '$next_month month')) - 1
        ");

    $result = $GLOBAL_db->SubmitQuery("
        SELECT DISTINCT
               customer_number,
               computer_output
        FROM bandwidth_month_total
             JOIN server_status_all USING (computer_number, customer_number)
        WHERE stats_date = '$date' AND status_number > 7");

    for($i = 0; $i < $result->numRows(); $i++) {
        if(empty($accounts[$result->getCell($i, 'customer_number')])) {
            $accounts[$result->getCell($i, 'customer_number')] = array();
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_6_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_5_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_4_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_3_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_2_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['bandwidth_1_month_ago'] = 0;
            $accounts[$result->getCell($i, 'customer_number')]['AccountNumber'] = $result->getCell($i, 'customer_number');
        }
        $accounts[$result->getCell($i, 'customer_number')]['bandwidth_'.$month.'_month_ago'] = $result->getCell($i, 'computer_output') / 1073741824.0; // GB
    }
}

$keys = array_keys($accounts);
sort($keys);

if( !headers_sent() ) {
    Header("Pragma:");
    Header("Content-type: application/vnd.ms-excel");
    Header("Content-Description: CORE Customer Monthly Bandwidth Report");
    Header("Content-Disposition: inline; filename=customer_bandwidth.xls");
    flush();
}

print '"AccountNumber"' . "\t" .
      '"bandwidth_6_month_ago"' . "\t" .
      '"bandwidth_5_month_ago"' . "\t" .
      '"bandwidth_4_month_ago"' . "\t" .
      '"bandwidth_3_month_ago"' . "\t" .
      '"bandwidth_2_month_ago"' . "\t" .
      '"bandwidth_1_month_ago"' . "\n";

foreach($keys as $key) {
   if(!empty($accounts[$key]['AccountNumber'])) {
   print '"' . $accounts[$key]['AccountNumber'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_6_month_ago'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_5_month_ago'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_4_month_ago'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_3_month_ago'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_2_month_ago'] . '"' . "\t" .
         '"' . $accounts[$key]['bandwidth_1_month_ago'] . '"' . "\n";
    }
}
?>
