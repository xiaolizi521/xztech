<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
	$stamp=time();
	if (!isset($month))
		$month=date("m",$stamp);
	
	if (!isset($day))
		$day=date("d",$stamp);
	if (!isset($year))
		$year=date("Y",$stamp);    
?>
<?
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Value Added Stats ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<? if (!isset($COMMAND)||$COMMAND==""):?>
<? require_once("menus.php"); ?>
<HTML id="mainbody"> 
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Value Added Stats ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<?include("wait_window_layer.php")?> 
<?
if (empty($offset)) {
    $offset = 0;
}
?>

<? if ($offset >= 5) { ?>
<a class="text_button"
    href="value_add_stats_by_date.php?offset=<?= $offset - 5 ?>">Previous</a>
<? } ?>
<a class="text_button"
    href="value_add_stats_by_date.php?offset=<?= $offset + 5 ?>">Next</a>
<table border="0"
       cellspacing="0"
       cellpadding="2"
       class="titlebaroutline">
<tr>
   <td>
	<table width="100%"
	       border="0"
	       cellspacing="0"
	       cellpadding="0"
          bgcolor="#FFFFFF">
    <tr>       
        <td> 
         		<table border="0"
         		       cellspacing="2"
         		       cellpadding="2">
         		<tr>
         			<td bgcolor="#003399" class="hd3rev">CORE: Value Added Stats
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                    Date Ending: <?print($month."/".$day."/".$year);?></td>
         		</tr>
               <tr>
                  <td>
<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
<TABLE class=datatable>
<?

	$product_list=$db->SubmitQuery("
        SELECT product_sku, product_name, product_description 
        FROM product_table 
        WHERE val_add='t' 
        GROUP BY product_sku, product_name, product_description
        ORDER BY product_name,product_description ASC
        ");
	$datacenters = $db->SubmitQuery("
        SELECT datacenter_number, name 
        FROM datacenter 
        WHERE datacenter_number >0 
        ORDER BY datacenter_number ASC
        ");
	$ConfigOpt = new ConfiguratorOptions(1, ADMIN);
	for ($z =0 ;$z<$datacenters->numRows();$z++) {
		$datac_num = $datacenters->getResult($z,"datacenter_number");
		$ConfigOpt->setDataCenterNumber($datac_num);
		print("<TR bgcolor=#6699ff>
            <Td COLSPAN=7 class=hd4rev> Datacenter: ".$datacenters->getResult($z,"name")."</Td></TR>\n");

?>
<TR>
    <th align=center. width=5> # </th>
	<Th> Value Added </Th>
	<Th> Total </Th>
	<Th> %&nbsp;of&nbsp;Total&nbsp; </Th>
	<Th> Revenue per Month </Th>
	<Th> Revenue per Server </Th>
    <th>Graph: Rev per Month</th>
</TR>
<?
	$end_date=$month."/".$day."/".$year;
	$total = $db->GetVal("
        SELECT COUNT(*)
        FROM server inv, sales_speed t2
        WHERE inv.computer_number = t2.computer_number 
            AND status_number >= " . STATUS_ONLINE . "
            AND date(t2.sec_contract_received::abstime) <= '$end_date' 
            AND inv.datacenter_number= $datac_num
        ");

	$tmonthly=$db->GetVal("
        SELECT SUM(final_monthly) 
        FROM server inv
        WHERE datacenter_number = $datac_num
            AND status_number >= " . STATUS_ONLINE . "
        ");
	$ctr=$product_list->numRows();
	for ($i=$offset;$i<$offset + 5;$i++)
	{
		$ValueAdded[$i] = $product_list->getResult($i, "product_description");
		$os=$ValueAdded[$i];
		$product[$os] = "product_sku = "
            . $product_list->getResult($i, "product_sku");
		$server_query[$os]=$db->SubmitQuery("
            SELECT w1.computer_number 
            FROM server_parts w1, server inv
            WHERE ".$product[$os]." 
                AND w1.computer_number = inv.computer_number 
                AND inv.datacenter_number = $datac_num
                AND status_number >= " . STATUS_ONLINE . "
            ");
		$num_servers[$os]=$db->NumRows($server_query[$os]);
        if ($total == 0) {
            $percent_servers[$os] = '0.00 %';
        }
        else {
            $percent_servers[$os] = number_format(
                (($num_servers[$os]/$total)*100),2) . "%";
        }
        if ($num_servers[$os] == 0) {
            $total_monthly[$os] = 0;
            $total_monthly_per_server[$os] = 0;
        }
        else {
            $total_monthly[$os]=$db->GetVal("
                SELECT SUM(final_monthly) 
                FROM server_parts w1, server inv
                WHERE w1.computer_number = inv.computer_number 
                    AND ".$product[$os]." 
                    AND inv.datacenter_number = $datac_num
                    AND status_number >= " . STATUS_ONLINE . "
                ");
            $total_monthly_per_server[$os] = 
                $total_monthly[$os] /$num_servers[$os];
        }
		
		//Build data arrays for graphs
		$label[$i] = $os;
		//print ("$label[$i]<BR>");
		$total_count[$i] = $num_servers[$os];
		//print ("$total_count[$i]<BR>");
		$percent_total[$i] = str_replace(",","",substr_replace($percent_servers[$os],'',-1,1));
		//print ("$percent_total[$i]<BR>");
		$monthly[$i] = str_replace(",","",substr_replace($total_monthly[$os],'',0,1));
		//print ("$monthly[$i]<BR>");
		$per_server[$i] = str_replace(",","",substr_replace($total_monthly_per_server[$os],'',0,1));
		//print ("$per_server[$i]<BR>");
		
		if ($i%2)
		{
			print("<TR class=even>");
		}
		else
		{
			print("<TR class=odd>");
		}
?>
	<td class=counter> <?=($i+1)?> </td>
    <TD><?print($ValueAdded[$i]);?></TD>
	<TD align=right><?print($num_servers[$os]);?></TD>
	<TD align=right><?print($percent_servers[$os]);?></TD>
	<TD align=right><?print($ConfigOpt->GetCurrencyHTML().$ConfigOpt->FormatNumber($total_monthly[$os]));?></TD>
	<TD align=right><?print($ConfigOpt->GetCurrencyHTML().$ConfigOpt->FormatNumber($total_monthly_per_server[$os]));?></TD>
    <TD valign=middle><img src='/images/339900.gif' height=10 width="<?=($total_monthly[$os]/10000)?>" border=0></TD>
</TR>
<?}?>
<TR>
	<Th> Totals </Th> 
    <th></th>
	<Th align=right><?print($total);?> &nbsp; &nbsp;</Th>
	<Th align=right>100.00%</TD>
	<Th align=right><?print($ConfigOpt->GetCurrencyHTML().$ConfigOpt->FormatNumber($tmonthly));?></Th>
	<Th align=right><?print($ConfigOpt->GetCurrencyHTML()
        . $ConfigOpt->FormatNumber($tmonthly / $total));?></Th>
    <th></th>
</TR>
<TR>
	<TD COLSPAN="6">&nbsp;</TD>
</TR>
<?}?>
</TABLE>
</td>
               </tr>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>
<?endif;?>
