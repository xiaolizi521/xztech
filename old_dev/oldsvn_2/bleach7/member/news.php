<?php 

 $file_title = $sitetitle . ' Home / News & Updates';

$nid = time();



/***********************************************
$display decides how many comments per page, default is 5
***********************************************/

$display = 6;


//now retrieve the news
if ( isset ( $_GET['id'] ) ) { //if id is set
	$news_id = $_GET['id'];
	$result_sql = 'SELECT * FROM `news` WHERE `id` = \'' . mysql_real_escape_string ( $news_id ) . '\'';
}
// 
else if ( isset ( $user_info['user_id'] ) ) {
	switch ( $user_info['category'] ) {
		case 0:			//	Show all news
			$result_sql = 'SELECT * FROM `news` WHERE `category` <= \'2\'';
			break;
		case 1:			//	Show just site news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'0\'';
			break;
		case 2:			//	Show just manga news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'1\'';
			break;
		case 3:			//	Show just anime news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'2\'';
			break;
		case 4:			//	Show Site News and Manga news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'0\' OR `category` = \'1\'';
			break;
		case 5:			//	Show Site News and Anime news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'0\' OR `category` = \'2\'';
			break;
		case 6:			//	Show Manga News and Anime news
			$result_sql = 'SELECT * FROM `news` WHERE `category` = \'1\' OR `category` = \'2\'';
			break;
	}
	$result_sql .= ' ORDER BY `id` DESC';
}
else {
	// User is a guest, so show all news
	$result_sql = 'SELECT * FROM `news` WHERE `category` <= \'2\' ORDER BY `id` DESC';
}
// get the comments relating to the above news post but before that
	// Determine where in the database to start returning results.
	if (isset($_GET['s'])) { // Already been determined.
		$start = $_GET['s'];
	}
	else {
		$start = 0;
	}

	// Determine how many pages there are.
	if (isset($_GET['np'])) { // Already been determined.
		$num_pages = $_GET['np'];
	} 
	else { // Need to determine.
		$query_result = mysql_query ($result_sql);
		$num_records = @mysql_num_rows ($query_result);
		$commmentno = $num_records;
		if ($num_records > $display) { // More than 1 page.
			$num_pages = ceil ($num_records/$display);
		} else {
			$num_pages = 1;
		}
	}

	$result_sql .= ' LIMIT ' . mysql_real_escape_string ( $start ) . ', ' . mysql_real_escape_string ( $display );


	$result_news = mysql_query( $result_sql ) or die( 'SELECT Error: ' . mysql_error() );
	if ( $handle = opendir ( $script_folder . '/images/smilies' ) ) {
		while ( false !== ( $file = readdir ( $handle ) ) ) {
			if ( $file != '.' && $file != '..' && ereg ( '.gif', $file ) ) {
				$smile_name = str_replace ( '.gif', '', $file );
				$smilies_array[] = $smile_name;
			}
		}
		closedir( $handle );
	}

	if ( mysql_num_rows ( $result_news ) == 0 ) {
		if ( ereg ( '/news', $_SERVER['QUERY_STRING'] ) ) {
			echo '<p style="text-align: center;">No news items have been added</p>';
		} else {
			//include ( "$script_folder/news.php" );
			header ( 'Location: ' . $site_path . '/news' );
		}
	} else {
		$limit = 10;
		$news_num = 0;
		while ( $show_news = mysql_fetch_array ( $result_news ) ) {
			$news_num++;
			$comments_num = $show_news['comments'];
			//echo 'NUM' . $comments_num;
			$pages_num = ceil ( $comments_num/$limit );
			$headline = stripslashes ( $show_news['headline'] );
			$news = stripslashes ( nl2br ( $show_news['news'] ) );
			$news = htmlentities  ( $news, ENT_QUOTES );
			$news = ParseMessage ( $news );
			$poster = '<a href="' . $site_path . '/member&amp;id=' . $show_news['poster'] . '">' . $show_news['poster'] . '</a>';
			if(isset($user_info['dst']))
			{$dst = $user_info['dst'];}
			else
			{$dst = 0;}
 			if ( $dst == 1 ) {
				$news_id = $show_news['id'] + 3600;
			}
			else {
				$news_id = $show_news['id'];
			}
			$date = DisplayDate( $news_id, 'l, F d, Y \a\\t h:i A', '0' );
			$comments = 'Comments (' . $comments_num . ')';
			if ( $pages_num == '0' ) {
				$comments = '<a href="?' . $ident . '=' . $script_folder . '/comments&amp;id=' . $show_news['id'] . '&amp;pg=1">' . $comments . '</a>';
			} else {
				$comments = '<a href="?' . $ident . '=' . $script_folder . '/comments&amp;id=' . $show_news['id'] . '&amp;pg=' . $pages_num . '">' . $comments . '</a>';
			}
            /*if(isset($user_info['username']) && $show_news['category'] == 2 && $show_news['db'] == 1)
			{
			 if($user_info['username'] == 'ExiledVip3r' || $user_info['username'] == 'anpan' || $user_info['username'] == 'niger')
			 {
			  include('time.php');
			 }
			}*/
			include ( 'templates/news.php' );
			
			if ( $news_num == mysql_num_rows( $result_news ) ) {
				echo '';
			} else {
			echo '<table style="height: 20px; border-collapse: collapse;">
	<tr>
		<td></td>
	</tr>
</table>
';
			}


		}
// Make the links to other pages, if necessary.
		if ($num_pages != 1) {
			echo '<p>';

			// Determine what page the script is on.
			$current_page = ( $start/$display ) + 1;

			// If it's not the first page, make a Previous button.
			if ( $current_page != 1 ) {
				echo '<a href="?page=member/news&amp;s=', ($start - $display), '&amp;np=', $num_pages, '">Previous</a> ';
			}

			// Make all the numbered pages.
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( $i != $current_page ) {
					echo '<a href="?page=member/news&amp;s=', ( ( $display * ( $i - 1 ) ) ), '&amp;np=', $num_pages, '">', $i, '</a> ';
				}
				else {
					echo $i, ' ';
				}
			}

			// If it's not the last page, make a Next button.
			if ( $current_page != $num_pages ) {
				echo '<a href="?page=member/news&amp;s=' . ($start + $display) . '&amp;np=' . $num_pages . '">Next</a>';
			}

			echo '</p><br />';

		} // End of links section.
}


function pagecount4comments ( $num_pages, $news_id, $start, $display ) {
	if ( $num_pages > 1 ) {
		// Determine what page the script is on.
		$current_page = ($start/$display) + 1;

		// If it's not the first page, make a Previous button.
		if ( $current_page != 1 ) {
			print '<a href="?id=members/view_comments.php&amp;vid='.$news_id.'&amp;s=' . ($start - $display) . '&amp;np=' . $num_pages . '">Previous</a> ';
		}

		// Make all the numbered pages.
		for ( $i = 1; $i <= $num_pages; $i++ ) {
			if ($i != $current_page) {
				print '<a href="?id=members/view_comments.php&amp;vid='.$news_id.'&amp;s=' . (($display * ($i - 1))) . '&amp;np=' . $num_pages . '">' . $i . '</a> ';
			}
			else {
				print $i . ' ';
			}
		}

		// If it's not the last page, make a Next button.
		if ( $current_page != $num_pages ) {
			print '<a href="?id=members/view_comments.php&amp;vid='.$news_id.'&amp;s=' . ($start + $display) . '&amp;np=' . $num_pages . '">Next</a>';
		}
		print '</p><br />';
	}
}
?>