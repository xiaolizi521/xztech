<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Suspended Servers ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Suspended Servers ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
     <LINK HREF="/css/core_ui.css" REL="stylesheet">
     <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<?require_once("tools_body.php");?> 
<?include("wait_window_layer.php")?> 
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
    <td colspan=2> Server CANNOT be pinged from CORE to verify they are offline. CORE is firewalled.   </td>
</tr>
</table>
<br clear="all">
<br>
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
          BGCOLOR="FFFFFF">
    <TR>       
        <TD> 
         		<TABLE BORDER="0"
         		       CELLSPACING="2"
         		       CELLPADDING="2">
         		<TR>
         			<TD BGCOLOR="#003399" CLASS="hd3rev"> 
                  Suspended Servers 
                  &nbsp; &nbsp; &nbsp; <?=strftime("%m/%d/%Y")?> </TD>
         		</TR>
               <TR>
                  <TD> 

<!-- Begin Outlined Table Content ------------------------------------------ -->

<TABLE class=datatable>

<?
	# These unions are slow, but are still faster than doing multiple
	# queries.
    $suspended_status_string = '';
    $status_number_list = array_merge(
        $GLOBALS['SUSPENDED_STATUS_NUMBERS'],
        $GLOBALS['WAIT_SUSPENDED_STATUS_NUMBERS'],
        STATUS_REACTIVATION
        );
    for($i = 0; $i < sizeof($status_number_list); $i++) {
        if ($i != 0) {
            $suspended_status_string .= ', ';
        }
        $suspended_status_string .= $status_number_list[$i];
    }
    $server_query = "
        select 
            t1.customer_number, t1.computer_number, 
            server_location(t1.computer_number),
            t1.sec_last_mod, status, t3.status_number,
            t6.sec_due_offline IS NOT NULL AS cancelled
        from 
            server t1
            JOIN status_options t3 USING (status_number)
            JOIN datacenter t4 USING (datacenter_number)
            LEFT JOIN queue_cancel_server t6 
                ON t1.computer_number = t6.computer_number
        where t1.status_number in ($suspended_status_string)
        order by status_number, cancelled, sec_last_mod
        ";

	$result = $db->SubmitQuery($server_query);
	$num_rows = $db->NumRows($result);
	if ($num_rows< 1)
	{
		print("No suspended servers");
		exit();
	}

	$today = time();
	print("<tr>
            <td class=counter> # </td>
            <th> Cust# </th>
            <th> Server# </th>
            <th> Suspended Date </th>
		      <th> Days Suspended </th>
		      <th> Switch [Port] </th>\n");

	$display_count = 1;
    $previous_status = -100;
    $previous_cancelled = '';
    $previous_computer = 0;
	for ($i = 0; $i < $num_rows; $i++)
	{
      $item = $db->FetchArray($result, $i);

		# The UNIONS in the main SELECT statement causes
		# duplicates.
		if ($previous_computer == $item['computer_number'])
		{
			continue;
		}
		else
		{
			$previous_computer = $item['computer_number'];
		}

		$current_status = $item['status'];
		$current_cancelled = $item['cancelled'];
		if ($current_status != $previous_status
                || $current_cancelled != $previous_cancelled) {
			print("<tr><th colspan=6 class=subhead1>");
			print("$current_status");
            if ($current_cancelled == 't') {
                print(" <font color=red>(Cancelled)</font>");
            }
            print "\n";
			$previous_status = $current_status;
			$previous_cancelled = $current_cancelled;
		}
		$days_suspended = ($today - $item['sec_last_mod']) / 3600 / 24;
		# AUP suspension limit is 60 days
		# Billing suspension limit is 90 days
        if (($item['status_number'] == 16 and $days_suspended > 60)
			or ($item['status_number'] == 17 and $days_suspended > 90))
		{
    		$suspension_limit_color = "<font color=red>";
            if (($i%2)==0)
                $bgcolor="class=evenred";
            else
                $bgcolor="class=oddred";
		} else {
            $suspension_limit_color = "";
			if (($i%2)==0)
                $bgcolor="class=even";
            else
                $bgcolor="class=odd";
		}
		printf("<tr $bgcolor>
               <td class=counter> %d </td> 
               <td> %s </td>
               <td> <a href=display_computer.php3?"
			. "customer_number=%s&computer_number=%s>%s</a> </td>"
			. "\n\t<td align=center> %s </td>"
            . "<td align=center>%s %d </td>"
            . "<td align=left><tt> %s </tt></td></tr>",
			$display_count,
			$item['customer_number'], $item['customer_number'], 
			$item['computer_number'], $item['computer_number'], 
			strftime("%m-%d-%Y", $item['sec_last_mod']), 
			$suspension_limit_color,
			$days_suspended,
			$item['server_location']);
		$display_count++;
	}
	print("</TR>");
	print("</table>");
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
<SCRIPT language="javascript">
<!-- 
	//Hides wait window when page loads
	ap_showWaitMessage('waitDiv', 0);
//--> 
</SCRIPT>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
