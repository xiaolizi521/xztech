<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

function Paginate( $identifier, $pages_num, $query_string ) {
global $site_path;
$pg = $_GET[$identifier];
$pg_limit = 5;
$pg_prev_pages = ($pg - floor ( $pg_limit/2 ) );
$pg_next_pages = ($pg + floor ( $pg_limit/2 ) );
if ( $pg_prev_pages >= 1 ) {
$start = $pg_prev_pages;
$first ="1";
} else {
$start = 1;
} 
if ( $pg_next_pages <= $pages_num ) {
$finish = $pg_next_pages;
$last = "1";
} else {
$finish = $pages_num;
}
if ( isset ( $pg ) && !empty ( $pg ) && ( $pg >= 1 ) && ( $pg <= $pages_num ) ) {
echo "<hr noshade color='#666666' size='1' width='100%'>";
echo "<table width='100%' cellpadding='0' cellspacing='0'><tr><td align='left'>";
echo "<table cellpadding='3' cellspacing='0' class='main'><tr>";
echo "<td>(Page $pg of $pages_num)</td>";
if ( $pg > 1 ) {
if ( $first == 1 ) {
echo "<td><a href='$site_path/$query_string&pg=1' title='First Page'><b>&laquo;</b></a></td>";
}
echo "<td><a href='$site_path/$query_string&pg=".($pg-1)."' title='Previous Page'>&lt;</a></td>";
}
for ( $x = $start; $x <= $finish; $x++ ) {
if ( $pg == $x ) {
echo "<td><b>$x</b></td>";
} else {
echo "<td><a href='$site_path/$query_string&pg=$x'>$x</a></td>";
}
}
if ( $pg < $pages_num ) {
echo "<td><a href='$site_path/$query_string&pg=".($pg+1)."' title='Next Page'>&gt;</a></td>";
if ( $last == 1 ) {
echo "<td><a href='$site_path/$query_string&pg=$pages_num' title='Last Page'><b>&raquo;</b></a></td>";
}
}
echo "</tr></table></td><td align='right'><select name='page' onchange='document.location=this.value' class='form'>";
for ( $x = 1; $x <= $pages_num; $x++ ) {
if ( $pg == $x ) {
echo "<option value='$site_path/$query_string&$identifier=$x' selected>Page $x</option>";
} else {
echo "<option value='$site_path/$query_string&$identifier=$x'>Page $x</option>";
}
}
echo "</select></td>";
echo "</tr></table>";
echo "<hr noshade color='#666666' size='1' width='100%'>";
}
}


function DisplayDate( $timestamp, $display_format, $display_format_option ) { 
global $user_info;

$days_passed = ( ( time() - $timestamp ) / 86400 );

if ( $display_format_option == 1 ) {
if ( $days_passed <= 1 ) {
$display_format = "\T\o\d\a\y\, h:i A";
} elseif ( $days_passed <= 2 ) {
$display_format = "\Y\e\s\\t\e\\r\d\a\y, h:i A";
} else {
$display_format = $display_format;
}
}  
if ( isset ( $user_info[user_id] ) ) {
if ( $user_info[dst] == 1 ) {
$timezone = ($user_info[timezone]+date("I"));
} elseif ( $user_info[dst] == 0 ) {
$timezone = $user_info[timezone];
}
} else {
$timezone = (-5+date("I"));
}
$zone = 3600*$timezone;
$datetime = (int)$timestamp;
$date = gmdate ( $display_format, $datetime + $zone );
return $date;
} 


function ParseMessage( $string ) { 
global $site_url, $script_folder, $smilies_array;

foreach ( $smilies_array as $var ) {
$string = str_replace( ":$var", "<img src='$site_url/$script_folder/images/smilies/$var.gif'>", $string );
}

$patterns = array ( 
'`\[b\](.+?)\[/b\]`is', 
'`\[i\](.+?)\[/i\]`is', 
'`\[u\](.+?)\[/u\]`is', 
'`\[email\](.+?)\[/email\]`is', 
'`\[url=([a-z0-9]+?://){1}(.+?)\](.+?)\[/url\]`is', 
'`\[url=(.+?)\](.+?)\[/url\]`is', 
'`\[url\]([a-z0-9]+?://){1}(.+?)\[/url\]`is', 
'`\[url\](.+?)\[/url\]`is', 
'`\[quote\](.+?)\[/quote\]`is',
'`\[quote=(.+?)\](.+?)\[/quote\]`is'
); 

$replacements =  array ( 
'<b>\\1</b>', 
'<i>\\1</i>', 
'<u>\\1</u>', 
'<a href="mailto:\1" target="_blank">\1</a>', 
'<a href="\1\2" target="_blank">\\3</a>', 
'<a href="http://\\1" target="_blank">\\2</a>', 
'<a href="\1\2" target="_blank">\1\2</a>', 
'<a href="http://\\1" target="_blank">\\1</a>', 
'<table width="95%" cellpadding="0" cellspacing="0" align="center" class="main"><tr><td><b>Quote:</b><table width="100%" cellpadding="2" cellspacing="0" class="main" style="border: 1px solid #C3C3C3"><tr><td valign="top"><i>\1</i></td></tr></table></td></tr></table>',
'<table width="95%" cellpadding="0" cellspacing="0" align="center" class="main"><tr><td><b>Quote:</b><table width="100%" cellpadding="2" cellspacing="0" class="main" style="border: 1px solid #C3C3C3"><tr><td valign="top">Originally Posted By <b>\1</b><br><i>\2</i></td></tr></table></td></tr></table>'
); 

$previous_string = "";
while ( $previous_string != $string ) {
$previous_string = $string;
$string = preg_replace( $patterns, $replacements , $string ); 
}

return $string; 
}
?>