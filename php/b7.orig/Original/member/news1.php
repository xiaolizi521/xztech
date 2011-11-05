<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$file_title = "$sitetitle Home / News & Updates";

$nid = time();

if ( ereg ( "/news", $_SERVER['QUERY_STRING'] ) && ( !isset ( $id ) || empty ( $id ) ) ) {
$result_news = mysql_query ( "SELECT * FROM news ORDER BY id DESC" );
} else {
$result_news = mysql_query ( "SELECT * FROM news WHERE id='$id'" );
}

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
echo "<table align=\"center\">No news items have been added</table>";
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
$pages_num = ceil ( $comments_num/$limit );
$headline = stripslashes ( $show_news['headline'] );
$news = stripslashes ( nl2br ( $show_news['news'] ) );
$news = ParseMessage ( "$news" );
$poster = "<a href=\"$site_path/member&amp;id=$show_news[poster]\">$show_news[poster]</a>";;
$date = DisplayDate( "$show_news[id]", "l, F d, Y \A\\t h:i A", "0" );
$comments = "Comments ($comments_num)";
if ( $pages_num == "0" ) {
$comments = "<a href=\"$site_url/$main_filename?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=1\" target=\"_top\">$comments</a>";
} else {
$comments = "<a href=\"$site_url/$main_filename?$ident=$script_folder/comments&amp;id=$show_news[id]&amp;pg=$pages_num\">$comments</a>";
}

include ( "templates/news.php" );

if ( $news_num == mysql_num_rows( $result_news ) ) {
echo "";
} else {
echo "<table height=\"20\" cellpadding=\"0\" cellspacing=\"0\"><tr><td></td></tr></table>";
}
}

}

?>
