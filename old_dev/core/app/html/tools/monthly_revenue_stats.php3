<?php
require_once("CORE_app.php");
set_back_link("monthly_revenue_stats.php3");

$white = array( '#BCBCBC',   '#FFFFFF' );
$blue  = array( '#BCBCF0',   '#EFEFFF' );
$green = array( '#BCF0BC',   '#EFFFEF' );

if (!isset($year)) {
    $year = strftime("%Y", time());
}
if ($year < 1998) {
    DisplayError("Invalid year: $year");
}

?>
<HTML id="mainbody">
<TITLE>Monthly Revenue Stats </TITLE>
<BODY BGCOLOR=WHITE>
<?require_once("tools_body.php");?>
<?php  back_link(); ?>

<?include("form_wrap_begin.php");?>

<FORM ACTION=monthly_revenue_stats.php3>
<B>Enter year:</B>
<INPUT NAME=year TYPE=TEXT>
<INPUT TYPE=SUBMIT VALUE="Display stats for year">
</FORM>

<?include("form_wrap_end.php");?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=8 HEIGHT=17>
	<IMG SRC="assets/images/c-tl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="TOP"></TD>
</TR>
<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="TOP" COLSPAN=8 HEIGHT=17>
	<FONT COLOR="#FFFFFF" SIZE="+2" ><CENTER>Monthly Revenue Stats </CENTER></FONT></TD>
</TR>

<TR BGCOLOR='<?php echo $green[1]; ?>'>
	<TH ALIGN='left'>Date</TH>
<?php //	<TH>Cumlative Sales</TH>
?>
	<TH align='right'>Hardware Revenue</TH>
	<TH>&nbsp;+&nbsp;</TH>
	<TH align='right'>Other RevenueServers</TH>
	<TH>&nbsp;=&nbsp;</TH>
    <TH align='right' BGCOLOR='<?php echo $green[0]; ?>'>&nbsp;Sums</TH>
	<TH>&nbsp;=&nbsp;</TH>
    <TH align='right' BGCOLOR='<?php echo $green[0]; ?>'>&nbsp;Running Total</TH>
</TR>
<?php

function queryRevenue($filter_hardware, 
        $is_hardware, $stamp_start, $stamp_end) {
    global $db;
    if ($filter_hardware) {
        if ($is_hardware) {
            $comparator = ' = ';
        }
        else {
            $comparator = ' != ';
        }
        $and_hardware = "AND w2.product_category $comparator 'hardware' ";
    }
    else {
        $and_hardware = '';
    }    
        
    $query = "
        SELECT SUM(w1.product_price)
        FROM server_parts w1, sku w2,
            sales_speed w3, server svr
        WHERE 
            w1.product_sku = w2.product_sku 
            AND w3.computer_number = w1.computer_number
            AND svr.computer_number = w3.computer_number
            $and_hardware
            AND w3.sec_finished_order >= '$stamp_start'
            AND w3.sec_finished_order <= '$stamp_end'
        ";
    $result = $db->SubmitQuery($query);
    $sum = 0;
    for ($i = 0; $i < $result->numRows(); $i++) {
        $sum += $result->getResult($i, 0);
    }
    return $sum;
}

$stamp=time();

$curr_month=date("m",$stamp);
$curr_year=date("Y",$stamp);

$running_total = 0;
$sum_new_servers = 0;
$sum_lost_servers = 0;

$warnings = "";

$datarray = array();
if ($year == $curr_year) {
    $last_month = $curr_month;
}
else {
    $last_month = 12;
}

if ($last_month == 1) {
    $first_month = 1;
}
else {
    $first_month = $last_month - 1;
}

for( $month=$first_month; $month <=$last_month ; $month++ ) {
    for( $last_day = 31 ; $last_day >= 28 ; $last_day-- ) {
        if( checkdate($month,$last_day,$year) ) {
            break;
        }
    }

    $stamp_start = mktime(0,0,0,$month,1,$year);
    $stamp_stop  = mktime(23,59,59,$month,$last_day,$year);

    $hardware_revenue = queryRevenue(true, true, $stamp_start, $stamp_stop);
    $other_revenue = queryRevenue(true, false, $stamp_start, $stamp_stop);
    $total_revenue = queryRevenue(false, false, $stamp_start, $stamp_stop);
    $running_revenue = queryRevenue(false, false, 915170400, $stamp_stop);

    $data = array( $year, sprintf( "%02d", $month),
                    $hardware_revenue, $other_revenue,
                    $total_revenue,$running_revenue);
    array_unshift( $datarray, $data );
}
echo "<TR><TD COLSPAN='7'>&nbsp;</td></tr>\n";

$counter = 0;
while( list( $junk, $data ) = each( $datarray ) ) {

  $counter++;

  if( $data[0] == $curr_year and $data[1] == $curr_month ) {
    $color =  '#FFD0D0';  # Red
    $scolor = '#FFB0B0'; # Dark Red
  } else {
    $scolor = $green[ $counter%2 ];

    if( $data[0] % 2 ) {
      $color = $white[ $counter%2 ];
    } else {
      $color = $blue[ $counter%2 ];
    }
  }

  echo "<TR BGCOLOR='$color'>",
    "<TD>",
    "$data[0] / $data[1]</TD>";
    
  echo "<TD align='right'>";
    echo $data[2];
  echo "</TD>";
  echo "<TD>&nbsp;</TD>";
  echo "<TD align='right'>";
    echo $data[3];
  echo "</TD>";
  echo "<TD>&nbsp;</TD>";
  echo "<TD align='right'>";
    echo $data[4];
  echo "</TD>";
  echo "<TD>&nbsp;</TD>";
  echo "<TD align='right'>";
    echo $data[5];
  echo "</TD>";
   echo  "</TR>\n";
}
  
?>

<TR>
	<TD BGCOLOR="#000000" ALIGN="LEFT" VALIGN="BOTTOM" COLSPAN='8' HEIGHT=17><IMG SRC="assets/images/c-bl.jpg" WIDTH=10 HEIGHT=10 BORDER=0 ALT="" ALIGN="BOTTOM"></TD>
</TR>

</TABLE><BR CLEAR="ALL">

<?php
if ( !empty( $warnings ) ) { 
  print "<FONT COlOR='red'>WARNING(S):</font>$warnings\n";
}
?>

<P>
<FONT COLOR=RED>Note: This table displays revenue based on the prices in 
product invoice of each server. The revenue is not based on the actual monthly fee for the server.</FONT>
<P>
<I>Suspended and migrating servers are not subtracted from the above listing as
we don't currently track the history of data suspensions and migrating states.</I>

<P>

This data is current as of <?php echo gmdate( "Y M d H:i" ); ?> GMT.
<?php back_link(); ?>
<?=page_stop()?>
</HTML>
