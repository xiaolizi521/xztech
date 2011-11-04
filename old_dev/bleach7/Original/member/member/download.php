<?php
####################################################################
# AR Memberscript - Download Mananger Add On				       #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   #
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #         
####################################################################

// ****** VARIABLES ******
//These are all variables that are used throughout the script.
//Input the appropriate values in between the double quotes - "like this"


// This is the url of your website and used to prevent leechers from downloading your files.
// It should be in this format: yoursiteurl (remove the http://, http://www., OR www.)
$download_site = "bleachportal.com";


// This is the title of your website, used to identify what site these files belong to.
$download_site_title = "Bleach Portal";


// This is the name of the folder that the memberscript is in.
$download_script_folder = "script";


// This is the number of seconds that are allowed for each download. This means that a user can only download a file every x seconds.
// If you would rather have downloads per day instead of seconds, just leave this variable blank and use the one below.
$download_timeout = 30;


// This is the number of downloads a user gets a day.
// If you would rather have downloads per second, leave this blank and use the one above.
$download_limit = 5;


// This is the number of seconds a user has to wait until they can download again once their download limit runs out..
// Note that 1 second = 3600 and that the default is set at 1 day (86400 seconds).
$download_limit_timeout = 5;


// These are the urls of the files to be downloaded from. These urls should be owned by you as leeching from another site is prohibited. 
// URLS should be full paths so it should start with: http:// BUT it must not end in a "/".
// To add new urls, put this on a new line per url: $download[variablename] = "someurl";
// You can put anything in the "variablename" part and in turn, the download url would be like: http://yoursite.com/download/download.php?id=variablenamehere&file=somefile
$download[mirror1] = "http://users.skynet.be/Chrno/naruto-kun/manga";
$download[mirror2] = "http://66.154.114.106/~ncmedia/episodes";
$download[mirror3] = "pathtomedia";


// This is the mysql information that you use to connect to your database in order to check if the visitor is a registered user.
$database_username = "bportal_members";
$database_password = "johnnymurtaza123";
$database_name = "bportal_interaction";





// ****** DO NOT EDIT PAST THIS POINT ******

$dbh = mysql_connect ( "localhost", "$database_username", "$database_password" ) or die ( 'Cannot connect to the database because: ' . mysql_error() );
mysql_select_db ( "$database_name" ); 

if ( isset ( $_COOKIE[user_id] ) && isset ( $_COOKIE[password] ) ) {
$result_userinfo = mysql_query ( "SELECT * FROM users WHERE user_id='$_COOKIE[user_id]' AND password='$_COOKIE[password]'" );
if ( mysql_num_rows ( $result_userinfo ) > 0 ) {
$user_info = mysql_fetch_array ( $result_userinfo );
}
}

include ( "../$download_script_folder/settings.php" );
include ( "functions.php" );
$download_id = $_GET[id];
$download_file = $_GET[file];
$download_source = $download[$download_id];
$download_path = str_replace( " ", "%20", "$download_source/$download_file" );
$download_filesize = FilesizeRemote ( "$download_path" );
$referer_site_parse = parse_url ( $GLOBALS[HTTP_REFERER] );
$referer_site = ereg_replace ( "http://|http://www.|www.", "", $referer_site_parse[host] );
$error_msg = "<font face='Times New Roman'><h1>Error</h1><p> File does not exist or hasn't been specified.</font>";
$error_msg_leecher = "<font face='Times New Roman'><h1>Leeching Site - $referer_site_parse[scheme]://$referer_site_parse[host]</h1><p> The site that you have attempted to download this file from is leeching this file from <a href='$download_site' target='_blank'>$download_site</a><p>
The leeching site has been logged and will be notified to the leeched site.<p>
Sorry for the inconvenience.<p>
In order to download this file, <a href='$GLOBALS[PHP_SELF]?$GLOBALS[QUERY_STRING]'>click here</a></font>"; 

/*
if ( !isset ( $user_info[user_id] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
exit();
}
*/

if ( isset ( $GLOBALS[HTTP_REFERER] ) ) {
if ( !ereg ( $referer_site, $download_site ) ) {
LogLeecher();
die ( "$error_msg_leecher" );
}
}

if ( ( !isset ( $download_id ) || empty ( $download_id ) ) || ( !isset ( $download_file ) || empty ( $download_file ) ) || !$fp = @fopen ( "$download_path", "rb" ) ) {
die ( "$error_msg" );
} else {

if ( $user_info[type] < 3 ) {
DayLimit( "$download_limit", "$download_limit_timeout", "$download_timeout", "$download_site", "$download_site_title" );
}

header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header ( "Last-Modified: ".gmdate ( "D, d M Y H:i:s" )."GMT" );
header ( "Pragma: no-cache" );
header ( "Cache-Control: no-store, no-cache, must-revalidate" );
header ( "Cache-Control: post-check=0, pre-check=0", false );
header ( "Content-Type: application/download" );
header ( "Content-Disposition: attachment; filename=".$download_file."" ); 
header ( "Content-Transfer-Encoding: binary" ); 
header ( "Content-Length: ".$download_filesize."" ); 

while ( !feof ( $fp ) ) { 
echo fread ( $fp, $download_filesize ); 
flush(); 
} 

}

fclose ( $fp );

exit();
?> 