<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
?>
<?    
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Rotatation List ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<HTML id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Rotation List ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?>    
<?include("wait_window_layer.php")?> 
<?
	$stamp=time();
	if (isset($month) && $month>0 && isset($day) &&$day>0)
	{
		if ($year<1999)
			$year="2000";
        $stamp=mktime(0,0,0,$month,$day,$year);
	}	
    if( empty($datacenter_number) ) {
		DisplayError("Missing datacenter number.");
	}
	$datacenter_name = $db->GetVal("
		select name
		from datacenter
		where datacenter_number = $datacenter_number");
	if ($datacenter_name == "")
	{
		DisplayError("Invalid datacenter number.");
	}
?>
<?include("form_wrap_begin.php")?>
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
                  <th>
                    <FORM ACTION="rotate_list.php3" METHOD=GET>
                    <INPUT TYPE=HIDDEN NAME=datacenter_number 
                           VALUE="<?print("$datacenter_number");?>">
                    Change Date Displayed To:</th>
                    <td>
                    <INPUT TYPE=text name=month size=2>
                    /<INPUT TYPE=text name=day size=2>
                    /<INPUT TYPE=text name=year size=4>
                    </td>
                    <TD ALIGN=RIGHT>
                    <input type="image"
                       src="/images/button_command_small_view_off.jpg"
                       alt="Go"
                       border="0"></TD>   
               </tr>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table>
<br>

<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>
</FORM>
<?include("form_wrap_end.php");?>
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
         			<td bgcolor="#003399" class="hd3rev"> CORE: Rotation List 
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <?print (date("D M/d/Y h:i A",$stamp));?></td>
         		</tr>
               <tr>
                  <td>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="2" VALIGN="TOP">
<TR>
	<TD BGCOLOR="#99ccff" COLSPAN=6> <strong><?print("$datacenter_name");?></strong></TD>
</TR>

<?
    $wherecond = '';
	if (date('d',$stamp)=="01")
	{
		if ($wherecond != "") {
			$wherecond.=" or ";
		}
        $wherecond.="product_description~'Monthly' ";
	}
	if (date('D',$stamp)=="Mon")
	{
		if ($wherecond != "") {
			$wherecond.=" or ";
		}
        $wherecond.=" product_description~'Weekly' ";
	}
	if ($wherecond != "") {
        $query = "
			select 
				w1.product_sku,
				w1.product_description 
			from 
				product_table w1,
				back_up_support_tape w2 
			where 
				w1.product_sku=w2.product_sku 
					and ($wherecond)
            GROUP BY w1.product_sku, w1.product_description
            ";
		$rotation_result = $db->SubmitQuery($query);
	}
	else {

		$rotation_result = $db->SubmitQuery(" 
			select 
				w1.product_sku,
				w1.product_description 
			from 
				product_table w1,
				back_up_support_tape w2 
			where 
				w1.product_sku=w2.product_sku 
            GROUP BY w1.product_sku, w1.product_description
            ");
	}

	$rotation_list = $db->GetResult($rotation_result, 0, "product_sku");
	for($i = 1; $i < $db->NumRows($rotation_result); $i++)
	{
		$rotation_list .= ", " 
			. $db->GetResult($rotation_result, $i, "product_sku");
	}

	if ($rotation_list!="")
	{
		//Now build up a list of computers who fit the criteria	
        $db->SubmitQuery("set enable_seqscan=0;");
		$computers=$db->SubmitQuery("
             select distinct on (switch_number,port_number)
                        customer_number,
                        si.computer_number,
                        sw.\"Number\" as switch_number,
                        smp.\"Number\" as port_number,
                        px.\"NTWK_val_InterfaceTypeID\",
                        sp.product_sku,
                        pt.product_description as \"rotation\"
             from
                        server si,
                        server_parts sp,
                        product_table pt,
                        \"NTWK_xref_Port_Computer_InterfaceType\" px,
                        \"NTWK_Port\" port,
                        \"NTWK_Switch\" sw,
                        \"NTWK_SwitchModelPort\" smp
             where
                        si.datacenter_number = $datacenter_number
                        and sp.product_sku in ($rotation_list)
                        and si.computer_number= sp.computer_number
                        and px.computer_number = si.computer_number
                        and port.\"NTWK_PortID\" = px.\"NTWK_PortID\"
                        and sw.\"NTWK_SwitchID\" = port.\"NTWK_SwitchID\"
                        and smp.\"NTWK_SwitchModelPortID\" = port.\"NTWK_SwitchModelPortID\"
                        and sp.product_sku = pt.product_sku
                        and si.datacenter_number = pt.datacenter_number
                        and si.status_number >= 12
             order by switch_number, port_number;
            ");
        $db->SubmitQuery("set enable_seqscan=1;");
		$num=$db->NumRows($computers);
        $data = array();
        for($i=0; $i<$num; $i++) {
			$row = $db->FetchArray($computers, $i);
            $switch_number = $row['switch_number'];
            $port_number = $row['port_number'];
            if( eregi("^([A-Z]+)([0-9]+)$",$switch_number,$regs) ) {
                $key = sprintf("% 3s % 3d % 3d",$regs[1],$regs[2],$port_number);
            } else {
                $key = $switch_number." $port_number";
            }
            $data[$key] = $row;
        }
        $computers->freeResult();
		print("<TR><Td COLSPAN=6 align=right> Total: $num Servers </td></TR>\n");

?>
<TR BGCOLOR="black">
    <th>&nbsp;</th>
	<TH> Acct-Computer #</TH>
	<TH> Switch[Port]</TH>
	<TH> Rotation Schedule</TH>
    <th> Tape Drive </th>
</TR>
<?
        $i = 0;
        $keys = array_keys($data);
        sort( $keys );
        foreach( $keys as $key ) {
         if( ($i++)%2 == 0) {
             $color="#f0f0f0";
         } else {
             $color="#ffffff";
         }
         $row =& $data[$key];
         $tape_drive = $db->GetVal("
				select product_description
				from server_parts t1, product_table t2
				where t1.computer_number = $row[computer_number]
					and t1.product_sku = t2.product_sku
					and t2.product_name = 'Tape Drive'
                group by product_description
                ");
         
         print("<TR BGCOLOR=$color>
                <td bgcolor=#e6e6e6 align=center> $i </td>
				<Td>$row[customer_number]-$row[computer_number]</td>
				<td><pre>$row[switch_number][$row[port_number]]</pre></td>
				<td>$row[rotation] &nbsp; &nbsp;</td>
				<td> $tape_drive </td></tr>");
     }
	}
	else
	{
			print("<TR><Td ALIGN=CENTER COLSPAN=6>No Tapes to be rotated</Td></TR>");
	}
?>
</TABLE>
</td>
               </tr>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table><BR CLEAR="ALL">
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>
