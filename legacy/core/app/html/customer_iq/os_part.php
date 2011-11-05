<?php
// TITLE: OS Count $Date: 04/01/05 16:25:50-06:00 $

require_once("CORE_app.php");
require_once("common.php");

sendHeaders( "os_part.php" );

#$cols[] = 'Account Number';

$report_db = getReportDB();
$result = $report_db->SubmitQuery('
SELECT distinct os FROM product_os ORDER BY os');

$rows = $result->numRows();
for( $i=0; $i<$rows; $i++ ) {
    $cols[] = ''.$result->getCell($i,0).'';
}

echo '"Account Number"'."\t".'"'.join( "\"\t\"", $cols) . "\"\t";
echo '"Total Servers"'."\n";

$list = array();

function QueryOS( $name ) {
    global $list;
    
    $query = '
  SELECT customer_number, 
         count(*) AS "'.$name.'"
  FROM computer_os 
  JOIN server USING (computer_number) 
  WHERE os = '."'$name'".'
    AND status_number >= ' . STATUS_ONLINE . '
  GROUP BY customer_number';

    $report_db = getReportDB();
    $c = $report_db->SubmitQuery( $query );

    for($i = 0; $i<$c->numRows(); $i++) {
        $cn  = $c->getCell($i,0);
        $num = $c->getCell($i,1);
        if( empty($list[$cn]) ) {
            $list[$cn] = array();
        }
        $list[$cn][$name] = $num;
    }

    return;
}

$query = "SELECT *\nFROM ".QueryOS($cols[1]) ."\n";

foreach( $cols as $os ) {
    QueryOS( $os );
}

foreach( $list as $computer_number => $data ) {
    echo "\"$computer_number\"";
    $sum = 0;
    foreach( $cols as $os ) {
        if( empty($data[$os]) ) {
            echo "\t\"\"";
        } else {
            echo "\t\"".$data[$os].'"';
            $sum += $data[$os];
        }
    }
    echo "\t\"$sum\"";
    echo "\n";
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
