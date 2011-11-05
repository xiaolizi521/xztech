<?php

require_once("CORE_app.php");
require_once("common.php");

checkDataOrExit( array( "sql" => "SQL Report" ) );
$date_where = getDateWhere();
$due_where = getDateWhere("queue_cancel_server.sec_due_offline");

sendHeaders( $sql );

$query = join("",file( $sql ));
$query = ereg_replace('--date_where', $date_where, $query);
$query = ereg_replace('--AND ?date_where', "AND $date_where", $query);
$query = ereg_replace('--due_where', $due_where, $query);
$query = ereg_replace('--AND ?due_where', "AND $due_where", $query);

$result = $GLOBAL_db->SubmitQuery($query);

$rows = $result->numRows();
$cols = $result->numFields();

$fields = array();
for( $i = 0; $i < $cols; $i++ ) {
    $field = $result->getFieldName($i);
    $field = ereg_replace('"','""',$field);
    $fields[] = "\"$field\"";
}
echo join("\t",$fields);
echo "\n";

for( $i=0; $i<$rows; $i++ ) {
    $cells = array();
    for( $j = 0; $j < $cols; $j++ ) {
        $cell = $result->getCell($i,$j);
        $cell = ereg_replace('"','""',$cell);
        $cells[] = "\"$cell\"";
    }
    echo join("\t",$cells)."\n";
}
    


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>