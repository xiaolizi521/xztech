<?php 

 $file_title = "$sitetitle Home / Editorials";

$nid = time();



/***********************************************
$display decides how many comments per page, defualt is 5
***********************************************/

$display = 4;



//now retrieve the editorials
$news_query = "SELECT * FROM news WHERE category = 3 ORDER BY id DESC";

// get the comments relating to the above news post but before that
// Determine how many pages there are.

	if (isset($_GET['np'])) { // Already been determined.
		$num_pages = $_GET['np'];
	} else { // Need to determine.
//		$query = "SELECT * FROM news ORDER BY id DESC "; // Standard query.
		$query_result = mysql_query ($news_query);
		$num_records = mysql_num_rows ($query_result);
		$commmentno=$num_records;
		if ($num_records > $display) { // More than 1 page.
			$num_pages = ceil ($num_records/$display);
		} else {
			$num_pages = 1;
		}
	}
// Determine where in the database to start returning results.
	if (isset($_GET['s'])) { // Already been determined.
		$start = $_GET['s'];
	} else {
		$start = 0;
	}

		$news_query = $news_query . " LIMIT $start, $display";
		$result_news = mysql_query( $news_query )
				or die("SELECT Error: ".mysql_error());
	
if ( $handle = opendir ( "$script_folder/images/smilies" ) ) {
	while ( false !== ( $file = readdir ( $handle ) ) ) {
		if ( $file != "." && $file != ".." && ereg ( ".gif", $file ) ) {
			$smile_name = str_replace ( ".gif", "", $file );
			$smilies_array[] = $smile_name;
		}
	}
	closedir( $handle );
}

if ( mysql_num_rows ( $result_news ) == 0 ) {
	if ( ereg ( "/news", $_SERVER['QUERY_STRING'] ) ) {
		echo "<table align=\"center\">No editorials have been added</table>";
	} else {
		//include ( "$script_folder/news.php" );
		header ( "Location: $site_path/news" );
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
			$news = ParseMessage ( "$news" );
			$poster = "<a href=\"$site_path/member&amp;id=$show_news[poster]\">$show_news[poster]</a>";;
			$date = DisplayDate( "$show_news[id]", "l, F d, Y \A\\t h:i A", "0" );
			$comments = "Comments ($comments_num)";
			if ( $pages_num == "0" ) {
				$comments = "<a href=\"?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=1\">$comments</a>";
			} else {
				$comments = "<a href=\"?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=$pages_num\">$comments</a>";
			}

			include ( "templates/news.php" );

			if ( $news_num == mysql_num_rows( $result_news ) ) {
				echo "";
			} else {
				echo "<table style=\"height: 20px; border-collapse: collapse;\">
	<tr>
		<td></td>
	</tr>
</table>
";
			}
	}
// Make the links to other pages, if necessary.
	if ($num_pages > 1) {

		echo '<p>';
		// Determine what page the script is on.
		$current_page = ($start/$display) + 1;

		// If it's not the first page, make a Previous button.
		if ($current_page != 1) {
			echo '<a href="?page=member/editorialtest&amp;&amp;s=' . ($start - $display) . '&amp;np=' . $num_pages . '">Previous</a> ';
		}

			// Make all the numbered pages.
		for ($i = 1; $i <= $num_pages; $i++) {
			if ($i != $current_page) {
				echo '<a href="?page=member/editorialtest&amp;&amp;s=' . (($display * ($i - 1))) . '&amp;np=' . $num_pages . '">' . $i . '</a> ';
			} else {
				echo $i . ' ';
			}
		}

		// If it's not the last page, make a Next button.
		if ($current_page != $num_pages) {
			echo '<a href="?page=member/editorialtest&amp;&amp;s=' . ($start + $display) . '&amp;np=' . $num_pages . '">Next</a>';
		}

		echo '</p><br />';

	} // End of links section.
}


function pagecount4comments($num_pages, $news_id, $start,$display)
{
	if ($num_pages > 1)
	{


						// Determine what page the script is on.
						$current_page = ($start/$display) + 1;

						// If it's not the first page, make a Previous button.
						if ($current_page != 1)
						{
							print '<a href="?id=members/view_comments.php&amp;&amp;&amp;vid='.$news_id.'&amp;&amp;s=' . ($start - $display) . '&amp;np=' . $num_pages . '">Previous</a> ';
						}

						// Make all the numbered pages.
						for ($i = 1; $i <= $num_pages; $i++) {
							if ($i != $current_page)
							{
								print '<a href="?id=members/view_comments.php&amp;&amp;&amp;vid='.$news_id.'&amp;&amp;s=' . (($display * ($i - 1))) . '&amp;np=' . $num_pages . '">' . $i . '</a> ';
							} else {
								print $i . ' ';
							}
			}

			// If it's not the last page, make a Next button.
			if ($current_page != $num_pages) {
				print '<a href="?id=members/view_comments.php&amp;&amp;&amp;vid='.$news_id.'&amp;&amp;s=' . ($start + $display) . '&amp;np=' . $num_pages . '">Next</a>';
			}

			print '</p><br />';

	   }
    }



?>
