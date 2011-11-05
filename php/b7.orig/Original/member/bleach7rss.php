<?php

include ( "db.php" );
include ( "settings.php" );

#	Title for the RSS feed	#
$rss_title = "Bleach7";

#	URL Link for the site	#
$rss_url = "http://bleach7.com/";

#	Site description		#
$rss_desc =  "The First Source for Bleach Information, Media, News and Fan Interaction";

#	Webmaster email address	#
$rss_webMaster = $contact_email;

#####################################################
#		Do not change beyond this point				#
#####################################################

if ( ereg ( "/news", $_SERVER['QUERY_STRING'] ) && ( !isset ( $id ) || empty ( $id ) ) ) {
$result_news = mysql_query ( "SELECT * FROM news ORDER BY id DESC" );
} else {
$result_news = mysql_query ( "SELECT * FROM news WHERE id='$id'" );
}

// open a file pointer to an RSS file
$fp = fopen ( "rss.xml", "w" );

// Now write the header information
fwrite ($fp, "<?xml version='1.0' ?>");
fwrite ($fp, "<rss version='2.0'>");
fwrite ($fp, "");
fwrite ($fp, "<channel>");
fwrite ($fp, "<title>$rss_title</title>");
fwrite ($fp, "<link>$rss_url</link>");
fwrite ($fp, "<description>$rss_desc</description>");
fwrite ($fp, "<webMaster>$rss_webMaster</webMaster>");
fwrite ($fp, "");

$limit = 10;
$news_num = 0;
while ( $show_news = mysql_fetch_array ( $result_news ) ) {
	$rssnews_id = "$site_url/$main_filename?$ident=$script_folder/news&amp;id=$show_news[id]";
	$news_num++;
	$comments_num = $show_news['comments'];
	$pages_num = ceil ( $comments_num/$limit );
	$headline = stripslashes ( $show_news['headline'] );
	$content_1 = stripslashes ( nl2br ( $show_news['news'] ) );
	$content_2 = substr($content_1, 0, 250);
	$news = strip_tags($content_2);
	if (strlen($content_2) > 250) {
        $news = $news . "....";
	}
	$poster = "$site_path/member&amp;id=$show_news[poster]";
	$date = DisplayDate( "$show_news[id]", "l, F d, Y h:i A \C\T", "0" );
	$comments = "$site_url/$main_filename?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=1";
	
	fwrite ($fp, "<item>");
	fwrite ($fp, "<title>$headline</title>");
	fwrite ($fp, "<link>$rssnews_id</link>");
	fwrite ($fp, "<description>$news</description>");
	fwrite ($fp, "<pubDate>$date</pubDate>");
	fwrite ($fp, "<comments>$comments</comments>");
	fwrite ($fp, "</item>");
	fwrite ($fp, "");
}
fwrite ($fd, "</channel>");
fwrite ($fd, "</rss>");
fclose ($fd);
?>