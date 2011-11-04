<?php
	$result_donation_info = mysql_query( "SELECT * FROM index_info" );
	$donation_info = mysql_fetch_array( $result_donation_info );

	if ( $donation_info['anime_raw'] < 100 ) {		// if the latest anime raw episode number is less than 100
		$anime_raw = "0".stripslashes ( $donation_info['anime_raw'] );		//add a "0" before the number
	}
	else {
		$anime_raw = $donation_info['anime_raw'];
	}
	if ( $donation_info['anime_sub'] < 100 ) {		// if the latest anime sub episode number is less than 100
		$anime_sub = "0".stripslashes ( $donation_info['anime_sub'] );		//add a "0" before the number
	}
	else {
		$anime_sub = $donation_info['anime_sub'];
	}
	if ( $donation_info['manga_raw'] < 100 ) {		// if the latest manga raw episode number is less than 100
		$manga_raw = "0".stripslashes ( $donation_info['manga_raw'] );		//add a "0" before the number
	}
	else {
		$manga_raw = $donation_info['manga_raw'];
	}
	if ( $donation_info['manga_sub'] < 100 ) {		// if the latest anime raw episode number is less than 100
		$manga_sub = "0".stripslashes ( $donation_info['manga_sub'] );		//add a "0" before the number
	}
	else {
		$manga_sub = $donation_info['manga_sub'];
	}

	switch ( $donation_info['month'] ) {
		case 1:
			$donation_month = "January";
			break;
		case 2:
			$donation_month = "February";
			break;
		case 3:
			$donation_month = "March";
			break;
		case 4:
			$donation_month = "April";
			break;
		case 5:
			$donation_month = "May";
			break;
		case 6:
			$donation_month = "June";
			break;
		case 7:
			$donation_month = "July";
			break;
		case 8:
			$donation_month = "August";
			break;
		case 9:
			$donation_month = "September";
			break;
		case 10:
			$donation_month = "October";
			break;
		case 11:
			$donation_month = "November";
			break;
		case 12:
			$donation_month = "December";
			break;
	}

	$result_donator_list = mysql_query( "SELECT * FROM donator WHERE month = $donation_info[month] AND year = $donation_info[year] ORDER BY donator DESC" );
	$current = 0;
	if ( mysql_num_rows ( $result_donator_list ) > 0 ) { //valid donator list is found
		while ( $view = mysql_fetch_array( $result_donator_list ) ) {
			$current = $current + $view['amount'];
		}
	}
	$remaining = $donation_info['goal'] - $current;
	if ( $remaining < 0 ) {
		$remaining = 0;
	}
?>
			<div id="donation">
				<div id="donate_cur" class="pos_donate">$<?php echo "$current"; ?></div>
				<div id="donate_goal" class="pos_donate">$<?php echo "$donation_info[goal]"; ?></div>
				<div id="donate_amo" class="pos_donate">$<?php echo "$remaining"; ?></div>
