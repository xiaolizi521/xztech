<p /><font face="Verdana" size="1"><b>Bleach 7 &gt; Full Donator List</b><br />
<br />
All donations go towards paying for our server costs. The donation goal is reset monthly as our payments on the server our due. Although donations are not mandatory to use the site, every dollar counts and helps us afford the rising bandwith costs as the website gets more populated each and every day. To donate in a currency besides US dollars, visit <a href="?page=supportus">this page</a>. For more detailed information on donations, read our <a href="?page=faq/faqdonate">donation FAQ</a>.
<br /><br />
Donators for the month of <b><?php echo "$donation_month, $donation_info[year]"; ?></b>: (<b>THANK YOU ALL!</b>) <!--[<i><a href="?page=costs">Click Here To View Website Costs</a></i>]--><br />
<br />
<?php
	$result_donator_list = mysql_query( "SELECT * FROM donator WHERE month = $donation_info[month] AND year = $donation_info[year] ORDER BY id ASC" );
	if ( mysql_num_rows ( $result_donator_list ) > 0 ) { //valid donator list is found
		while ( $view = mysql_fetch_array( $result_donator_list ) ) {
			echo $view[donator].' - $'.$view[amount].'<br />';
		}
	}
	else {
		echo "No donations have been made this month yet.<br />";
	}
?>
<br />
</font>