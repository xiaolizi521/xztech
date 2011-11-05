<br />
<span class="VerdanaSize1Main"><b>Bleach 7 &gt; Staff Section &gt; Editorials</b><br />
<br />
</span><span class="VerdanaSize2Main"><b>Editorials</b></span><span class="VerdanaSize1Main"><br />
<br />

<?php
$comment_limit = 10;

//if id is set
if ( isset ( $_GET['id'] ) ) { 
	$news_id = $_GET['id'];
	$result_sql = 'SELECT * FROM `news` WHERE `id` = \'' . $news_id . '\'';
	$result_news = mysql_query( $result_sql ) or die("SELECT Error: ".mysql_error());
	while ( $show_editorial = mysql_fetch_array ( $result_news ) ) {
		$poster = $show_editorial['poster'];
		$title = stripslashes ( $show_editorial['headline'] );
		$link = "?page=member/member&amp;id=$poster";
		
// Check date in "Month (spelled out), Day (two digit mode; plus ending if possible), Year (four digit mode)
		$date = date ( "F dS, Y", $news_id );
		$editorial = stripslashes ( nl2br ( $show_editorial['news'] ) );
		$editorial = htmlentities  ( $editorial, ENT_QUOTES, ISO-8859-15 );
		$editorial = ParseMessage ( "$editorial" );
		$editorial = html_entity_decode ( $editorial );
/*		$comments_num = $show_editorial['comments'];
		$pages_num = ceil ( $comments_num/$limit );
		$comments = "Comments ($comments_num)";
		if ( $pages_num == "0" ) {
			$comments = "<a href=\"?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=1\">$comments</a>";
		} else {
			$comments = "<a href=\"?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=$pages_num\">$comments</a>";
		}
*/		echo "<b> &gt $poster</b><br />
		<br />
		</font><font face=\"Verdana\" size=\"2\"><b>TITLE:&nbsp; </b></font><font face=\"Verdana\" size=\"2\">$title<br />
		&nbsp;by: <a href=\"$link\">$poster</a> ($date)</font><font face=\"Verdana\" size=\"1\"><br />
<br />
$editorial<br />
<br />";
/*<table cellpadding=\"0\" cellspacing=\"0\" class=\"main\" style=\"width: 100%; border: none;\">
	<tr>
		<tb style=\"text-align: right;\">$comments</tb>
	</tr>
</table>";*/
	}
}

// If not, then show a list of all editorials.
// Make sure about the the tables and columns being used.
// Add "Nickname" to user table
else {

	echo "<br />
<br />
</font><font face=\"Verdana\" size=\"2\"><b>Staff Editorials:</b></font><font face=\"Verdana\" size=\"1\"><br />
&nbsp;-- All about the staff that you'd want to know, and even more about what you don't!<br />
";

	// Get a list of user ranks from 80 - 99
	$result_rank_sql = "SELECT * FROM `rank` WHERE `ID` >=80 ORDER BY `ID` DESC"; 
	$result_rank_list = mysql_query( $result_rank_sql ) or die( "SELECT Error: ".mysql_error() );
	while ( $show_rank = mysql_fetch_array ( $result_rank_list ) ) {	

		// Within each rank, list each person alphabetically
		$result_user_sql = "SELECT * FROM `users` WHERE `type` = $show_rank[ID] ORDER BY `username` ASC"; 
		$result_user_list = mysql_query( $result_user_sql ) or die("SELECT Error: ".mysql_error());
		// Within each user, list all editorials
		while ( $show_user = mysql_fetch_array ( $result_user_list ) ) {
			// Category 3 is the news category for Editorials
			$result_sql = "SELECT * FROM `news` WHERE `poster` = CONVERT( _utf8 '$show_user[username]' USING latin1 ) COLLATE latin1_swedish_ci AND `category` =3 ORDER BY `id` ASC";
			$result_list = mysql_query( $result_sql ) or die("SELECT Error: ".mysql_error());
			// If the user has not posted any editorial, do not show user
			if ( mysql_num_rows ( $result_list ) != 0 ) {
				echo "<br />
				</font><font face=\"Verdana\" size=\"2\">&nbsp;- $show_user[username]</font><font face=\"Verdana\" size=\"1\"><br />";
				while ( $show_list = mysql_fetch_array ( $result_list ) ) {
					$title = stripslashes ( $show_list['headline'] );
					$link = "?page=member/editorial&amp;id=$show_list[id]";
					echo "&nbsp;&nbsp;&nbsp;&nbsp; + <a href=\"$link\">$title - read</a><br />
";
				}
				echo "&nbsp;&nbsp;&nbsp;&nbsp; + <i>next chapter soon</i><br />
";
			}
		}
	}
	echo "<br />
	&nbsp;MORE SOON</font></p>";
}
?>