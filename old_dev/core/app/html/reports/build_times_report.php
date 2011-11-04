<?php
# TITLE: Build Times Report
# $Id: build_times_report.php 1.11 04/04/28 09:31:01-05:00 jryan@dev.core.rackspace.com $

require_once("CORE_app.php");
//$GLOBALS["print_sql"] = true;

if( empty( $datacenter ) ) {
        $datacenter = 0;
}

if (empty($start) or strlen($start) < 8) {
    $start = strftime("%m/%d/%Y", time() - (60 * 60 * 24 * 7));
}
if (empty($stop) or strlen($stop) < 8) {
    $stop = strftime("%m/%d/%Y", time());
}

/*
 * Get the Data
 */
if( empty( $datacenter ) ) {
        $dc_where = "";
} else {
        $dc_where = "AND datacenter_number = $datacenter";
}

if (!empty($group_by_date)) {
    $group_by = "GROUP BY DATE(sec_finished_order::abstime)";
    $date_field = 'DATE(sec_finished_order::abstime) AS "Day",';
    $group_by_date_checked = 'CHECKED';
}
else {
    $group_by = '';
    $date_field = '';
    $group_by_date_checked = '';
    $group_by_date = '';
}

if (!empty($eliminate_over_24_hours)) {
    $where_not_over_24 = "AND (sec_finished_order - sec_contract_received)"
        . " < (24 * 60 * 60)";
    $eliminate_over_24_hours_checked = 'CHECKED';
}
else {
    $where_not_over_24 = '';
    $eliminate_over_24_hours = '';
    $eliminate_over_24_hours_checked = '';
}

if (empty($command) or $command != "Show Late Servers") {
$query = <<< SQL
SELECT $date_field
    COUNT(*) AS "Build Count",
    AVG(timestamptz(abstime(sec_finished_order)) - timestamptz(abstime(sec_contract_received)))
        AS "Average Build Time (days)"
FROM sales_speed t1 JOIN server t2 USING (computer_number)
WHERE sec_finished_order > 0
    AND sec_finished_order::abstime > '$start'::timestamptz
    AND sec_finished_order::abstime < '$stop'::timestamptz
    $dc_where
    $where_not_over_24
$group_by
SQL;
}
else {
$query = <<< SQL
SELECT 
    computer_number AS "Computer",
    TIMESTAMPTZ(sec_finished_order::abstime) AS "Marked Online",
    sec_finished_order::abstime - sec_contract_received::abstime
        AS "Build Time"
FROM sales_speed t1 JOIN server t2 USING (computer_number)
WHERE sec_finished_order > 0
    AND sec_finished_order::abstime > '$start'::timestamptz
    AND sec_finished_order::abstime < '$stop'::timestamptz
    AND (sec_finished_order - sec_contract_received) > (24 * 60 * 60)
    $dc_where
SQL;
}

$NO_DOWNLOAD = true;
$report->_total_pages = 1;
$report->setPageSize( 0 );
$report->setQuery($query);

/*
 * Collect Datacenter Data
 */
$result = $GLOBAL_db->SubmitQuery( 'select name, datacenter_number as number from datacenter' );
for( $i = 0 ; $i < $result->numRows(); $i++ ) {
        $datacenters[$result->getResult( $i, 'number' )] =
                 $result->getResult( $i, 'name' );
}
$datacenters[0] = "All Offices";
ksort( $datacenters );




# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
    global $GLOBAL_db, $datacenter, $PHP_SELF, $page, $report;
    global $totals, $data, $stamp, $datacenters;
    global $start, $stop;
    global $group_by_date_checked, $eliminate_over_24_hours_checked;
    global $group_by_date, $eliminate_over_24_hours;

    $report->setPageSize( 0 );

    $report->setFieldRule('Computer',
        '<a href="/tools/DAT_display_computer.php3?'
            . 'computer_number=%Computer">%Computer</a>');

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
            print "'$PHP_SELF?"
                . "page=$page&datacenter=$id&start=$start&stop=$stop"
                . "&group_by_date=$group_by_date"
                . "&elimnate_over_24_hours=$eliminate_over_24_hours'";
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
<input type="text" name="start" size="15" value="'.$start.'">
<a href="javascript:show_calendar(\'dateform.start\');"
onmouseover="window.status=\'Pick a date\';return true;"
onmouseout="window.status=\'\';return true;"><img 
src="/images/show-calendar.gif" width="24" height="22" border="0" valign="middle"></a>
to
<input type="text" name="stop" size="15" value="'.$stop.'">
<a href="javascript:show_calendar(\'dateform.stop\');"
onmouseover="window.status=\'Pick a date\';return true;"
onmouseout="window.status=\'\';return true;"><img 
src="/images/show-calendar.gif" width="24" height="22" border="0" valign="middle"></a>
<BR>
</center>
<hr>
<input type="checkbox" name="group_by_date" size="15" 
    value="1" ' . $group_by_date_checked . '>
    Group by date built
<BR>
<input type="checkbox" name="eliminate_over_24_hours" size="15" 
    value="1" ' . $eliminate_over_24_hours_checked . '>
    Eliminate server builds over 24 hours
<BR>
<center>
<input type="submit" name="command" value="Show Build Times">
<hr>
<input type="submit" name="command" value="Show Late Servers">
</center>
</form>
';

    print "<B>Servers built between $start and $stop:</B>\n";
    $report->printHTML(1);

}


?>
