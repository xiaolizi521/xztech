<?php
require ("CORE.php");
$base="http://admin.rackspace.com";
?>
<? if ((empty($start) and empty($stop)) or empty($textonly)): ?>
<title>Part Sales</title>
<?require_once("tools_body.php");
//print "PLEASE USE THE 'Part Sales' REPORT IN MAIN->REPORTS->SQL_REPORTS.";
print "
<script>
    document.location = \"/reports/show.php?page=part_sales_report.php\"
</script>";
exit();
?>
<h3>Part Sales:</h3>
<form action=part_sales.php3>
<table><tr>
<?
print("<tr><td align=center colspan=2>Limit to online dates between");
print("<tr><td>Start (mm-dd-yyyy):<td><input type=text name=start value=\"$start\">");
print("<tr><td>Stop:<td><input type=text name=stop value=\"$stop\">");
if ($textonly != "")
{
	$check = "CHECKED";
}
print("<tr><td>Text-only result<td>
	<input type=checkbox name=textonly $check>");
?>
<tr><td><td><input type=submit value=Search>
</table>
</form>
<pre>
<h1>Results</h1>
<? 
else:
	Header("content-type: text/plain");
endif; 

	function DateToSec($date)
	{
		$arr = explode("-", ltrim($date));
		return(mktime(0,0,0, $arr[0], $arr[1], $arr[2]));
	}

if ($start != "" and $stop != "")
{
	$start = DateToSec($start);
	$stop = DateToSec($stop);

	$query = "
        SELECT FROM (
            SELECT product_sku, COUNT(computer_number) AS gained
            FROM sales_speed 
                JOIN server_parts USING (computer_number)
            WHERE sec_finished_order > $start 
                AND sec_finished_order < $stop
            GROUP BY product_sku
            ) q1
            OUTER JOIN 
            (
            SELECT product_sku, COUNT(computer_number) AS lost
            FROM offline_servers
                JOIN queue_server_parts USING (computer_number)
            WHERE sec_offline > $start 
                AND sec_offline < $stop
            GROUP BY product_sku, product_name, product_description
            ) q2
            USING (product_sku)
        ORDER BY count, product_name, product_description DESC
        ";
	if ($textonly == "")
	{
		print "<hr><pre>$query<hr>";
	}
	$server_list = $db->SubmitQuery( $query);
	if ($server_list->numRows()<1)
		print("<h1>No parts found</h1>");
	else
	{
		$num = $server_list->numRows();
		printf("% 3s %7s\t%4s\t%50s\t%s\n",
			"ROW",
			"SKU",
			"GAINED",
			"LOST",
			"CHANGE",
			"PRODUCT DESCRIPTION",
			"PRODUCT TYPE");
		$i = 0;
		while($i < $num)
		{
			$index = $i + 1;

			printf("% 3s %7s\t%4s\t%50s\t%s\n",
				$index, 
				$server_list->getResult( $i, "product_sku"),
				$server_list->getResult( $i, "count"),
				$server_list->getResult( $i, "product_description"),
				$server_list->getResult( $i, "product_name"));
			$i++;
		}
	}
	$server_list->freeResult();
	$db->CloseConnection();
}
?>
	
		

	
