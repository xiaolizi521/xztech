<?php
require_once("CORE_app.php"); 
$report_db = getReportDB();
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Monthly Sales Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');
$white = array( '#E6E6E6',   '#FFFFFF' );
$blue  = array( '#BCBCF0',   '#EFEFFF' );
$green = array( '#BCF0BC',   '#EFFFEF' );

?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Monthly Sales Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
</head> 
<?require_once("tools_body.php");?> 
<table border="0"
       cellspacing="0"
       cellpadding="0"
       align="left">
<tr bgcolor="#FFCC33">
    <td valign="top" width=10><img src="/images/note_corner.gif"
                          width="10"
                          height="10"
                          hspace="0"
                          vspace="0"
                          border="0"
                          valign="TOP"
                          alt=""></td>
     <td> NOTES: </td>
</tr>
<tr bgcolor="#FFF999">
    <td colspan=2> 
<p> All non-real servers (Firewalls &amp;
Load-Balancers) have been removed from this report.
</p>
<?php
if( empty($REPORTDB_IS_AVAILABLE) ) {
    echo '<p style="font-weight: bolde">All data is current as of now.</p>';
} else {
    echo '<p style="font-weight: bold">All data is current as of yesterday night.</p>';
}
?>
</td>
</tr>
</table>
<br clear="all">
<br>
<?include("wait_window_layer.php")?>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<TABLE BORDER="0"
       CELLSPACING="0"
       CELLPADDING="2"
       CLASS="titlebaroutline">
<TR>
   <TD>
	<TABLE BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Monthly Sales Stats </TD>
         		</TR>
               <TR>
                  <TD>
<!-- Begin Outlined Table Content ------------------------------------------ -->
<TABLE BORDER="0"
       CELLSPACING="1"
       CELLPADDING="2"
       VALIGN="TOP">

<TR BGCOLOR='<?php echo $green[1]; ?>'>
	<TH ALIGN='left'>Date</TH>
<?php //	<TH>Cumlative Sales</TH>
?>
	<TH align='right'>Added Servers</TH>
	<TH>&nbsp;-&nbsp;</TH>
	<TH align='right'>Lost Servers</TH>
	<TH>&nbsp;=&nbsp;</TH>
	<TH align='right'>Delta&nbsp;</TH>
   <TH align='right' BGCOLOR='<?php echo $green[0]; ?>'>&nbsp;Sums</TH>
</TR>
<?php

$stamp=time();

$curr_month=date("m",$stamp);
$curr_year=date("Y",$stamp);

$running_total = 0;
$sum_new_servers = 0;
$sum_lost_servers = 0;

$warnings = "";

$datarray = array();
for ( $year = 1998 ; $year <= $curr_year ; $year++ ) {
  $last_month = ($year==$curr_year)?$curr_month:12;

  for( $month=1; $month <=$last_month ; $month++ ) {

    for( $last_day = 31 ; $last_day >= 28 ; $last_day-- ) {
      if( checkdate($month,$last_day,$year) ) {
        break;
      }
    }

    $stamp_start = mktime(0,0,0,$month,1,$year);
    $stamp_end   = mktime(23,59,59,$month,$last_day,$year);

    $new_servers = $report_db->GetVal("
        SELECT COUNT(ss.computer_number)
        FROM sales_speed ss
        WHERE sec_finished_order >= '$stamp_start'
            AND sec_finished_order <= '$stamp_end'
            AND is_real_server(ss.computer_number)
        ");

    $lost_servers = $report_db->GetVal(" 
        SELECT COUNT(o.computer_number)
        FROM offline_servers o,
             sales_speed ss
        WHERE o.computer_number = ss.computer_number
            AND ss.sec_finished_order > 0
            AND o.sec_offline >= '$stamp_start'
            AND o.sec_offline <= '$stamp_end'
            AND is_real_server(o.computer_number)
            AND o.customer_number = ss.customer_number
            AND o.computer_number = ss.computer_number
        ");

    $delta = $new_servers - $lost_servers;
    
    $sum_new_servers  += $new_servers;
    $sum_lost_servers += $lost_servers;
    $running_total    += $delta;

    $data = array( 
        'year' => $year, 
        'month' => sprintf( "%02d", $month),
        'new_servers' => $new_servers, 
        'lost_servers' => $lost_servers, 
        'delta' => $delta,
        'running_total' => $running_total 
        );
    array_unshift( $datarray, $data );

  }
}


$db_lost_servers = $report_db->GetVal("
    SELECT COUNT(o.computer_number)
    FROM offline_servers o,
         sales_speed ss
    WHERE o.computer_number = ss.computer_number
        AND ss.sec_finished_order > 0
        AND is_real_server(o.computer_number)
        AND o.customer_number = ss.customer_number
        AND o.computer_number = ss.computer_number
    ");

$db_new_servers  = $report_db->GetVal("
    SELECT COUNT(ss.computer_number)
    FROM sales_speed ss
    WHERE sec_finished_order > 0
        AND is_real_server(ss.computer_number)
    ");

$db_final_total = $db_new_servers - $db_lost_servers;

if( $db_final_total == $running_total ) {
  $scolor = '#E0E0E0';
} else {
  $scolor = '#FF0000';
  $warnings .= "<br>The real totals don't match the calculated totals.";

  echo "<TR>\n",
    "<TD ALIGN='left'>Real Totals</TD>",
    "<TD ALIGN='right'>$db_new_servers</TD>",
    "<TD>&nbsp;</TD>",
    "<TD ALIGN='right'>$db_lost_servers</TD>",
    "<TD>&nbsp;</TD>",
    "<TD>&nbsp;</TD>",
    "<TD ALIGN='right' BGCOLOR='$scolor'>$db_final_total</TD>",
    "</TR>";
}

echo "<TR>",
  "<TD ALIGN='left'>Calculated Totals</TD>",
  "<TD ALIGN='right'>$sum_new_servers</TD>",
  "<TD>&nbsp;</TD>",
  "<TD ALIGN='right'>$sum_lost_servers</TD>",
  "<TD>&nbsp;</TD>",
  "<TD>&nbsp;</TD>",
  "<TD ALIGN='right' BGCOLOR='$scolor'>$running_total</TD>",
  "</TR>";

echo "<TR><TD COLSPAN='7'>&nbsp;</td></tr>\n";

$counter = 0;
reset($datarray);
while( list( $junk, $data ) = each( $datarray ) ) {

  $counter++;

  if( $data['year'] == $curr_year and $data['month'] == $curr_month ) {
    $color =  '#FFD0D0';  # Red
    $scolor = '#FFB0B0'; # Dark Red
  } else {
    $scolor = $green[ $counter%2 ];

    if( $data['year'] % 2 ) {
      $color = $white[ $counter%2 ];
    } else {
      $color = $blue[ $counter%2 ];
    }
  }

  echo "<TR BGCOLOR='$color'>",
    "<TD>",
    "$data[year] / $data[month]</TD>";

    
  echo "<TD align='right'>";
  if( $data['new_servers'] > 0 ) {
    echo "<A HREF='sales_new_breakout.php?",
        "month=$data[month]&year=$data[year]'>",
        "$data[new_servers]</A>";
  } else {
    echo $data['new_servers'];
  }
  echo "</TD>";

  echo "<TD>&nbsp;</TD>";

  echo "<TD align='right'>";
  if( $data['lost_servers'] > 0 ) {
    echo "<A HREF='sales_lost_breakout.php?",
        "month=$data[month]&year=$data[year]'>",
        "$data[lost_servers]</A></TD>";
  } else {
    echo $data['lost_servers'];
  }
  echo "</TD>";

  echo "<TD>&nbsp;</TD>",
    "<TD align='right'>$data[delta]</TD>",
    "<TD align='right' BGCOLOR='$scolor'>$data[running_total]</TD>",
    "</TR>\n";
}
  
?>
</TABLE><BR CLEAR="ALL">

<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<?php
if ( !empty( $warnings ) ) { 
  print "<FONT COlOR='red'>WARNING(S):</font>$warnings\n";
}
?>

<P>
Suspended and migrating servers are not counted in the above listing as
we don't currently track history data suspensions and migrating states.

<?php
if( empty($REPORTDB_IS_AVAILABLE) ) {
    echo '<p style="font-weight: bolde">All data is current as of now.</p>';
} else {
    echo '<p style="font-weight: bold">All data is current as of yesterday night.</p>';
}
?>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>
