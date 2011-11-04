<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Servers with Incomplete Locations ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Servers with Incomplete Locations ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
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
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> 
                  Servers with Incomplete Locations 
                  &nbsp; &nbsp; &nbsp; <?=strftime("%m/%d/%Y")?> </TD>
         		</TR>
               <TR>
                  <TD>

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE class=datatable>
<?
	$result = QueryIncompleteLocationServers($datacenter_number);
	$num_rows = $db->NumRows($result);
	if ($num_rows< 1)
	{
		print("No online servers without switch or port numbers.");
		exit();
	}

	print("<tr>
            <td class=counter>&nbsp;</td>\n
            <th> Server </th>\n
            <th> Online Date </th>\n
		    <th> Build Tech </th>\n
		    <th> Switch [Port] </th>\n
            <th> Datacenter</th>\n");
	for ($i = 0; $i < $num_rows; $i++)
	{
		if (($i%2)==0)
         $bgcolor="class=even";
      else
         $bgcolor="class=odd";
      $ctr =$i + 1;   
      $item = $db->FetchArray($result, $i);
		$current_status = $item['status'];
		if (!isset($previous_status) || $current_status != $previous_status)
		{
			print("<tr>\n<th colspan=6 class=subhead1>");
			print("$current_status </th>\n</tr>\n");
			$previous_status = $current_status;
		}
		if ($item['status_number'] != 12)
		{
			$userid = "";
		}
		else
		{
			$userid = $db->GetVal("select userid
				from build_tech
				where computer_number = $item[computer_number]
				order by sec_created DESC
				limit 1");
		}
		printf("<tr $bgcolor>\n"
            . "<td class=counter> $ctr </td>"
            . "\t<td><a href=display_computer.php3?"
			. "customer_number=%s&computer_number=%s> %s-%s </a></td>\n"
			. "\t<td align=center> %s </td>\n"
			. "\t<td align=center> %s </td>\n"
         . "\t<td align=center><pre>%s [%s]</pre></td>\n"
         . "\t<td> %s </td>\n",
			$item['customer_number'], $item['computer_number'], 
			$item['customer_number'], $item['computer_number'], 
			$item['date'], $userid ."&nbsp;",
			$item['switch_number'], $item['port_number'],
			$item['datacenter_name']);
	   print("</tr>\n");
	}
	print("</table>\n");
?>

<!-- End Outlined Table Content -------------------------------------------- -->
                  </TD>
               </TR>
         		</TABLE>
        </TD>
    </TR>
    </TABLE></TD>
</TR>
</TABLE>
</BODY>
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?=page_stop()?>
</HTML>
<? $page_timer->stop();?>