<? require_once("CORE_app.php"); 
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Server Repairs & Upgrades ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html id="mainbody">
<head>
<script>
    var avgLoadTime = <?print $loadtime;?>;
</script>
<TITLE>CORE: Server Repairs & Upgrades ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
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
    <td colspan=2> Servers CANNOT be pinged from CORE.
    CORE is behind a firewall. </td>
</tr>
</table>
<br clear="all">

<SCRIPT LANGUAGE="JavaScript"
        SRC="/script/wait_window.js"> 
</SCRIPT>

<p>

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
         			<td bgcolor="#003399" class="hd3rev"> Server Repairs & Upgrades </td>
         		</tr>
               <tr>
                  <td>
<TABLE class=datatable>
<?
	# These unions are slow, but are still faster than doing multiple
	# queries.
	$server_query = "
            SELECT 
                si.customer_number, si.computer_number, 
                server_location(si.computer_number),
                si.sec_last_mod, status, status_number
            FROM server si 
                JOIN status_options so USING (status_number)
            WHERE status_number in (13, 14)
            ORDER BY status_number, sec_last_mod
		";

	$result = $db->SubmitQuery($server_query);
	$num_rows = $db->NumRows($result);
	if ($num_rows< 1)
	{
		print("No suspended servers");
		exit();
	}

	$today = time();
	print(" <tr>
                <td class=counter> # </td>
                <th>Cust#</th>
                <th>Server#</th>
                <th>Age&nbsp;(hrs)</th>
        		<th>Datacenter:Switch[Port]</th>
		        <th>Status Change Comment</th></tr>\n");

	$display_count = 1;
	$previous_computer = false;
	$previous_status = false;
	for ($i = 0; $i < $num_rows; $i++)
	{
    	if (($i%2)==0) {
             $bgcolor="class=even";
        } else {
             $bgcolor="class=odd";
        }
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
		if ($current_status != $previous_status)
		{
			print("<tr><th colspan=8 class=subhead1>");
			print("$current_status\n");
			$previous_status = $current_status;
		}
		$comments = $db->GetVal("select comments from computer_log
			where computer_number = $item[computer_number]
				and comments ~ 'Status'
			order by sec_created DESC
			limit 1");
		$comments = substr($comments, 0, 250);
		$hours_at_status = sprintf("%3.1f",
			(time() - $item['sec_last_mod']) / 3600);
		printf("<tr $bgcolor><td class=counter>$display_count </td>"
			. "<td> $item[customer_number]</td>"
			. "<td><a href=display_computer.php3?"
			. "customer_number=$item[customer_number]"
			. "&computer_number=$item[computer_number]>"
			. "$item[computer_number]</a></td>"
			. "\n\t<td align=center>$hours_at_status </td>"
			. "<td><tt>$item[server_location]</td>"
			. "<td><font size=-1>$comments</td>" 
			);
		$display_count++;
	}
	print("</TR><TR><TD>");
	print("</table>");
?></td>
               </tr>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table>
<?=page_stop()?>
</html>
