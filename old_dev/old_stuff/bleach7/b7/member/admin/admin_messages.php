<?php

	echo "<form method='post' name='form_messages'>";

		$usertype = $user_info['type'];
		$user_rank = 0;
		
		if($usertype == 99 || $usertype == 98)
		{ $user_rank = 4; }
		elseif($usertype == 90 || $usertype == 91)
		{ $user_rank = 3; }
		elseif($usertype == 80 || $usertype == 81)
		{ $user_rank = 2; }
		elseif($usertype == 30 || $usertype == 31)
		{ $user_rank = 1; }
		elseif($usertype == 20 || $usertype == 21)
		{ $user_rank = 0; }
		else
		{ $user_rank = 0; }
		echo "
		<b><span style='text-decoration: underline;'><a href='index.php?action=addmessage'>Click here to add a new message</a></span></b><br />
		<br />";
		$result_message_list = mysql_query( "SELECT * FROM `admin_message` ORDER BY `id` DESC" )or die(mysql_error());
			$count = 1;
			echo "<table cellpadding='7' cellspacing='0' class='main' style='border-top: 1px solid #C3C3C3; width: 60%;'>
			";
			while ( $message_list = mysql_fetch_array( $result_message_list ) ) {
							$color = ( $count % 2 == 0 ) ? "#eeeeee" : "#ffffff";
				//$color2 = ( $count % 2 == 0 ) ? "#ffffff" : "#eeeeee";

				$date = DisplayDate( "$message_list[id]", "l, F d, Y \A\\t h:i A", "0" );
				echo "	<tr bgcolor='$color'>
							<td align='left' style='border-bottom: 1px solid #C3C3C3'>$count. <span style='text-decoration: underline;'><b>".stripslashes ( $message_list['headline'] )."</b></span><br />
								- <i>Posted By $message_list[poster] On $date</i></td>
							<td align='right' style='border-bottom: 1px solid #C3C3C3'><a href='index.php?action=viewmessage2&amp;id=$message_list[id]'>View</a>";
				if ( $user_info['username'] == $message_list['poster'] || $user_info['type'] >= 80 ) {
					echo " | <a href='index.php?action=editmessage&amp;id=$message_list[id]'>Edit</a> | <a href='index.php?action=deletemessage&amp;id=$message_list[id],$message_list[poster]' onclick='DeleteMessage( \"index.php?action=deletemessages&amp;id=$message_list[id],$message_list[poster]\" )'>Delete</a>";
				}
				echo "		</td>
					</tr>
					";
				$count++;
			}
			echo "</table>";
		
	echo "</form>";


?>