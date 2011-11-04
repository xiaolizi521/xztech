<?php
				$result_donation_info = mysql_query( "SELECT month, year FROM index_info" );
				$donation_info = mysql_fetch_array( $result_donation_info );
				$result_donator_list = mysql_query( "SELECT * FROM donator WHERE month = $donation_info[month] AND year = $donation_info[year] ORDER BY donator ASC" );
				echo "<p style='text-align: center;'><a href='$PHP_SELF?view=main&amp;type=index&amp;action=add'>Click here to add a new donator.</a></p>";
				if ( mysql_num_rows ( $result_donator_list ) > 0 ) { //valid donator list is found
					if ( count ( $errors ) > 0 ) {
						echo "<table cellpadding='0' cellspacing='0' class='main'>
							<tr>
								<td>";
						DisplayErrors();
						echo "</td>
							</tr>
						</table>
						";
					}
					echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 50%;'>";
					$count = 1;
					while ( $view = mysql_fetch_array( $result_donator_list ) ) {
						$date = DisplayDate( "$edit[id]", "l, F d, Y \A\\t h:i A", "0" );
						echo "	<tr>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $view['donator'] )."</b></span> - <i>Currently Donated $".stripslashes ( $view['amount'] )."</i></td>
							<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='$PHP_SELF?view=main&amp;type=index&amp;action=edit&amp;id=$view[id]'>Edit</a> | 
							<a href='$PHP_SELF?view=main&amp;type=index&amp;action=delete&amp;id=$view[id]'>Delete</a></td>
						</tr>
						";
						$count++;
					}
					echo "</table>";
				}
				else {
					switch ( $donation_info['month'] ) {
						case 1:
							$month = "January";
							break;
						case 2:
							$month = "February";
							break;
						case 3:
							$month = "March";
							break;
						case 4:
							$month = "April";
							break;
						case 5:
							$month = "May";
							break;
						case 6:
							$month = "June";
							break;
						case 7:
							$month = "July";
							break;
						case 8:
							$month = "August";
							break;
						case 9:
							$month = "September";
							break;
						case 10:
							$month = "October";
							break;
						case 11:
							$month = "November";
							break;
						case 12:
							$month = "December";
							break;
					}
					echo "<p style='text-align: center;'><b>Currently there are no donators listed for the month of $month, $donation_info[year]</p>";
				}
?>
