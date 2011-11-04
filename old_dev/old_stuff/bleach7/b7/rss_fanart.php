<?php
require_once ( 'member/db.php' );
require_once ( 'member/functions.php' );

$now = date ( "D, d M Y H:i:s T", time() );

$rss = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>
    <rss version="2.0" xmlns:media="http://search.yahoo.com/mrss" 
        xmlns:atom="http://www.w3.org/2005/Atom">

	<channel>
		<title>Bleach7 Fanart RSS Feed</title>
		<link>http://www.bleach7.com/</link>
		<description>Bleach 7 - The First Source for Bleach Information, Media, News and Fan Interaction.</description>
		<language>en-us</language>
		<copyright>Copyright 2004 - 2008</copyright>
		<generator>automatic</generator>
		<managingEditor>webmaster@bleach7.com (Hinalover)</managingEditor>
		<webMaster>tseltman@comcast.net (Hinalover)</webMaster>
		<lastBuildDate>' . $now . '</lastBuildDate>
		<ttl>15</ttl>';

$result_gallery = mysql_query( 'SELECT * FROM `gallery` WHERE `approved` = \'1\' AND `category`=\'1\' ORDER BY `id` DESC LIMIT 0, 153' )
			or die( 'SELECT Error: ' . mysql_error() );
			
while ( $gallery = mysql_fetch_array ( $result_gallery ) ) {
	$title = $gallery['title'];
	$poster = $gallery['poster'];
	$thumb = $gallery['thumb'];
	$id = $gallery['id'];
	$location = $gallery['location'];
	$comments = $gallery['comments'];
	$kudos = $gallery['kudos'];
	$category = 'Wallpapers';
	//$desc = $gallery['comment'];
	$desc = stripslashes ( nl2br ( $gallery['comment'] ) );
	$desc = htmlspecialchars ( $desc );

	$link = 'http://www.bleach7.com/?page=fan/fanartview&amp;id='.$id;
	
		$rss .= '
		<item>
			<title>' . $title . '</title>
			<link>' . $link . '</link>
			<media:description>Description: ' . $desc . '&lt;br /&gt;Comments: ' . $comments . '&lt;br /&gt;Kudos: ' . $kudos . '</media:description>
			<media:thumbnail url="http://www.bleach7.com/'.$thumb.'" />
			<media:content url="http://www.bleach7.com/'.$location.'" type="image/jpeg" />

			<author>' . $poster . '</author>
			<guid>' . $link . '</guid>
		</item>';
}



$rss .= '	</channel>
';
$rss .= '</rss>';
( $fp = fopen ( dirname(__FILE__).'/rss_fanart.xml', 'w' ) ) or die ( 'couldn\'t open' );
fwrite ( $fp, $rss );
fclose ( $fp );
?>