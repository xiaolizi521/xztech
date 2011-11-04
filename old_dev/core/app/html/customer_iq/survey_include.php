<?php

if( empty($numbers) ) {
    trigger_error("Incorrectly called", FATAL );
}

$counter = 1;
$fields = array( "Account Number" );

flush();

function DoPart( $num, $text ) {
    global $counter, $fields, $table, $extra_select;

    $fields[] = "Date (#$num)";
    $fields[] = "$text (#$num)";

    $text = ereg_replace('"', '', $text );
    $query = '(
SELECT
  customer_number,
  question_response as "'.$text.'",
  date(sec_created::abstime) as date
FROM
  '.$table.'
WHERE
  question_number = '.$num.'
ORDER BY customer_number ASC, date DESC
) as q'.$counter++;

    return $query;
}

$query = '
SELECT
 question_number,
 question_text
FROM
 survey_cust_questions
WHERE
 question_number in ('.$numbers.')
ORDER BY question_number
';

$report_db = getReportDB();
$result = $report_db->SubmitQuery( $query );

$query = 'select distinct on (customer_number) * from 
(
SELECT customer_number 
FROM "xref_customer_number_Account"
) as base
 LEFT JOIN';

$queries = array();
for( $i=0; $i<$result->numRows(); $i++ ) {
    $qnum = $result->getCell($i, 0);
    $qtext = $result->getCell($i, 1);
    $queries[] = DoPart( $qnum, $qtext );
}

$query .= join( "\n USING (customer_number)\n LEFT JOIN ", $queries );
$query .= "\n USING (customer_number)\n ORDER BY customer_number";


echo '"' . join( "\"\t\"", $fields ) . "\"\n";

$result = $report_db->SubmitQuery( $query );
for( $row=0; $row<$result->numRows(); $row++ ) {
    $row_output = "";
    $row_has_data = false;
    for( $col=0; $col<sizeof($fields); $col++ ) {
        $text = $result->getCell($row,$col);
        $text = preg_replace('/"/', '\"', $text);
        # Yes, there is a control M in that string.
        $text = preg_replace("/[\n]/", ' ', $text);
        if( $col != 0 ) {
            if( !$row_has_data and !empty($text) ) {
                $row_has_data = true;
            }
            $row_output .= "\t";
        }
        if( $col != 0 ) {
            $date = $result->getCell($row,++$col);
            $row_output .= "\"$date\"\t";
        }
        $row_output .= "\"$text\"";
    }
    if( $row_has_data ) {
        echo "$row_output\n";
    }
}

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
