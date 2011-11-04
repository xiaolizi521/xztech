<?php

require_once("common.php");

if( empty($startdate) ) {
    $startdate = "01/01/1997";
}

if( empty($enddate) ) {
    $enddate = "Now";
}

$result = $report_db->SubmitQuery('
SELECT "ID", "Name"
FROM "ACCT_val_SLAType"
ORDER BY "ID"
');
$sla_select = "\n<select name='sla_type' ";
$sla_select .= "onChange=\"location.href='$PHP_SELF?startdate=$startdate&enddate=$enddate&sla_type='+this.options[this.selectedIndex].value\"";
$sla_select .= ">\n";
if( empty($sla_type) ) {
    $chk = "selected";
} else {
    $chk = "";
}
$sla_select .= "  <option value='0' $chk> Any </option>\n";
for($i=0; $i<$result->numRows(); $i++) {
    $name = $result->getCell($i,'Name');
    $id = $result->getCell($i,'ID');
    if( $id == $sla_type ) {
        $chk = "selected";
    } else {
        $chk = "";
    }
    $sla_select .= "  <option value='$id' $chk>$name</option>\n";
}
$sla_select .= "</select>\n";



$result = $report_db->SubmitQuery('
SELECT datacenter_number, name
FROM datacenter
WHERE datacenter_number > 0
ORDER BY name
');
$dc_select = "\n<select name='dc_type' ";
$dc_select .= "onChange=\"location.href='$PHP_SELF?startdate=$startdate&enddate=$enddate&dc_type='+this.options[this.selectedIndex].value\"";
$dc_select .= ">\n";
if( empty($dc_type) ) {
    $chk = "selected";
} else {
    $chk = "";
}
// Commented out because it just acts weird with "ALL".
//$dc_select .= "  <option value='0' $chk> ALL </option>\n";
for($i=0; $i<$result->numRows(); $i++) {
    $id = $result->getCell($i,0);
    $name = $result->getCell($i,1);
    if( $id == $dc_type ) {
        $chk = "selected";
    } else {
        $chk = "";
    }
    $dc_select .= "  <option value='$id' $chk>$name</option>\n";
}
$dc_select .= "</select>\n";

// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>