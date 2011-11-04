<?php

require_once("CORE_app.php");
$report_db = getReportDB();

$current_stamp=time();
global $REPORTDB_IS_AVAILABLE;
if( $REPORTDB_IS_AVAILABLE ) {
    $current_stamp = $current_stamp - 60*60*24;
}

// Enable Progress Bar
include("TimeRegister.php");
$page_timer = new PageTimeRegister();
$page_timer->start();
?>    
<HTML id="mainbody">
<HEAD>
<?
    $loadtime = number_format((0+$page_timer->average_duration),0);
?>    
<?set_title('Parts Usage ------ (Avg Load: '.$loadtime.' secs)','#003399');?>
<TITLE>CORE: Parts Usage ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<?require_once("tools_body.php");?>
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
	<TABLE WIDTH="100%"
	       BORDER="0"
	       CELLSPACING="0"
	       CELLPADDING="0"
          BGCOLOR="#FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> Parts Usage
                  &nbsp; &nbsp; &nbsp; <?=strftime("%m/%d/%Y", $current_stamp)?> </TD>
         		</TR>
               <TR>
                  <TD> 

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE class=datatable>
<TR>
	<TH> Date </TH>
   <TH COLSPAN=5> Parts </TH>
</TR>
<?
	//Do it for last 30 days and  -  total and then 4 week view
	//Build total
	$current=getdate($current_stamp);
	$today=mktime(0,0,0,$current['mon'],$current['mday'],$current['year']);
	$start=$today-(3600*24*28);
	$i=0;

	$interval_start[$i]=$start;
	$interval_stop[$i++]=$start+(3600*24*28);

	$interval_start[$i]=$start;
	$interval_stop[$i++]=$start+(3600*24*7);

	$interval_start[$i]=$start+(3600*24*7);
	$interval_stop[$i++]=$start+(3600*24*14);

	$interval_start[$i]=$start+(3600*24*14);
	$interval_stop[$i++]=$start+(3600*24*21);

	$interval_start[$i]=$start+(3600*24*21);
	$interval_stop[$i++]=$start+(3600*24*28);


	for ($j=0;$j<count($interval_start);$j++)
	{
?>
	<TR>
		<TH ALIGN=CENTER VALIGN=TOP>TOTALS FOR<br>
      <?print (date('m/d/y',$interval_start[$j])." - ".date('m/d/y',$interval_stop[$j])); ?>
		<BR><br>
		<? 
			$interval=($interval_stop[$j]-$interval_start[$j])/(24*3600);	
		print ($interval);?> Days</TH>
		<TD>
		<TABLE class=datatable>
        <tr bgcolor=#e6e6e6>
            <td>No.</th>
            <td>Part Description</td>
            <td>Cnt</td>
            <td>Adj</td>
            <td>No.</th>
            <td>Part Description</td>
            <td>Cnt</td>
            <td>Adj</td>            
        </tr>       
	<?
		$parts=$report_db->SubmitQuery("
            SELECT * 
            FROM (
            SELECT DISTINCT ON (product_sku) 
                w1.product_sku, w2.product_description, w2.product_name 
            FROM server_parts w1, product_table w2 
            WHERE w1.product_sku = w2.product_sku 
                AND product_name !~ 'Bandwidth' 
                AND product_name !~ 'uired' 
                AND product_description !~ 'Backup' 
                AND product_description != 'IP Application Fee' 
                AND product_name !~ 'IP' 
                AND product_name !~ 'Service' 
                AND w1.sec_created >= $interval_start[$j] 
                AND w1.sec_created <= $interval_stop[$j] 
            ) PRODUCTS 
            ORDER BY product_description, product_sku, product_name ASC;
            ");
        $ctr=0;
		for ($i=0; $i < $parts->numRows(); $i++)
		{
			$count=$report_db->getVal( "
                select count(product_sku) 
                from server_parts 
                where product_sku=".$parts->getResult($i,"product_sku")." 
                and sec_created>=$interval_start[$j] 
                and sec_created<=$interval_stop[$j];");
			$previous_interval_start=$interval_start[$j]-(3600*24*$interval);
			$previous_interval_stop=$interval_stop[$j]-(3600*24*$interval);
			$previous_count=$report_db->getVal( "
                select count(product_sku) 
                from server_parts 
                where product_sku=".$parts->getResult($i,"product_sku")." 
                and sec_created>=$previous_interval_start 
                and sec_created<=$previous_interval_stop;");

			$adj_count=$count-$previous_count;
			if ($ctr==0) {
			    if (($i%4)==0)
				    $begin_tag="<TR class=even>\n";
				else
				    $begin_tag="<TR class=odd>\n";
            $end_tag="";
			$ctr=1;
            } else {
				$begin_tag="";
				$end_tag="</TR>\n";
				$ctr=0;
            }
			print ($begin_tag);
            print "<td class=counter>".($i+1)."</td>\n";
			print ("\t<TD>".$parts->getResult($i,"product_description")."</TD>\n");
            print "\t<TD ALIGN=right>".$count."</td>\n<td align=left>";
			if ($adj_count == 0)
                print "0";
            if ($adj_count>0)
				print ("<font color=#0000FF> +$adj_count</FONT>");
			else if ($adj_count<0)
				print("<font color=#FF0000><strong>$adj_count</strong></FONT>");
			print("</TD>\n");
			print ($end_tag);
		}
		$parts->freeResult();
	?>
		</TABLE>
		</TD>
   </TR>
	<TR>
      <TD> &nbsp; </TD>
   </TR>
<?}?>
</TABLE>

<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>