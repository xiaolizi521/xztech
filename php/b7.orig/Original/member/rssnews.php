<?php
####################################################################
# News RSS Feed                                                    #
# Created by Hinalover                                             #
# Copyright Bleach7.com                                            #
####################################################################

$timeout = 3600;
$file_title = "News RSS Feed";

$XML_top;
$XML_start;
$XML_end;

$nid = time();

if ( ereg ( "/news", $_SERVER['QUERY_STRING'] ) && ( !isset ( $id ) || empty ( $id ) ) ) {
$result_news = mysql_query ( "SELECT * FROM news ORDER BY id DESC" );
} else {
$result_news = mysql_query ( "SELECT * FROM news WHERE id='$id'" );
}

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
$comments = "<a href=\"$site_url/$main_filename?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=1\">$comments</a>";
} else {
$comments = "<a href=\"$site_url/$main_filename?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=$pages_num\">$comments</a>";
}

$XML_body;

if ( $news_num == mysql_num_rows( $result_news ) ) {
echo "";
} else {
echo "<table height=\"20\" cellpadding=\"0\" cellspacing=\"0\"><tr><td></td></tr></table>";
}
}

$XML_rss = "$XML_top" + "$XML_start" + "$XML_body" + "$XML_end";

( $fp = fopen ( "rss.php", "w" ) ) or die ( "couldn't open" );
fwrite ( $fp, "$XML_rss" );
fclose ( $fp );
}
?>