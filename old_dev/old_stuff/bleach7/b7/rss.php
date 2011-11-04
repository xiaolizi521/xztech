<?php
require_once ( 'member/db.php' );
require_once ( 'member/functions.php' );

$now = date ( "D, d M Y H:i:s T", time() );

$rss = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>';
$rss .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss" 
        xmlns:atom="http://www.w3.org/2005/Atom">

	<channel>
		<title>Bleach7 RSS Feed</title>
		<link>http://www.bleach7.com/</link>
		<description>Bleach 7 - The First Source for Bleach Information, Media, News and Fan Interaction.</description>
		<language>en-us</language>
		<copyright>Copyright 2004 - 2008</copyright>
		<generator>automatic</generator>
		<managingEditor>webmaster@bleach7.com (Hinalover)</managingEditor>
		<webMaster>tseltman@comcast.net (Hinalover)</webMaster>
		<lastBuildDate>' . $now . '</lastBuildDate>
		<ttl>15</ttl>';

$result_news = mysql_query( 'SELECT * FROM `news` ORDER BY `id` DESC LIMIT 0, 10' )
			or die( 'SELECT Error: ' . mysql_error() );
			
while ( $show_news = mysql_fetch_array ( $result_news ) ) {
		
	$result_poster = mysql_query( 'SELECT `username`, `email_address` FROM `users` WHERE `username` = \'' . mysql_real_escape_string ( $show_news['poster'] ) . '\'' )
		 or die( 'SELECT Error: ' . mysql_error() );
	$show_poster = mysql_fetch_array ( $result_poster );
	
	$title = $show_news['headline'];
	$link = 'http://www.bleach7.com/index.php?page=member/news&amp;id=' . $show_news['id'];
	$comments_num = $show_news['comments'];
	$pages_num = ceil ( $comments_num/10 );
	$comments = 'http://www.bleach7.com/index.php?page=member/comments&amp;id=' . $show_news['id'] . '&amp;pg=' . $pages_num;
	$auther = $show_news['poster'];
	$desc = stripslashes ( nl2br ( $show_news['news'] ) );
	$desc = htmlspecialchars ( $desc );
	$date = date ( 'D, d M Y H:i:s T', $show_news['id'] );
	$auther = $show_poster['email_address'] . ' (' . $show_poster['username'] . ')';
    switch ( $show_news['category'] ) {
		case 0:
			$category = 'Site News';
			break;
		case 1:
			$category = 'Manga News';
			break;
		case 2:
			$category = 'Anime News';
			break;
		case 3:
			$category = 'Editorial';
			break;
	}

	$rss .= '		<item>
			<title>' . $title . '</title>
			<link>' . $link . '</link>
			<description>' . $desc . '</description>
			<author>' . $auther . '</author>
			<comments>' . $comments . '</comments>
			<category>' . $category . '</category>
			<pubDate>' . $date . '</pubDate>
			<guid>' . $link . '</guid>
		</item>
';
}



$rss .= '	</channel>
';
$rss .= '</rss>';
( $fp = fopen ( dirname(__FILE__).'/rss.xml', 'w' ) ) or die ( 'couldn\'t open' );
fwrite ( $fp, $rss );
fclose ( $fp );
?>