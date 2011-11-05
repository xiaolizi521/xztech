<?php
function parse_message( $string ) { 
global $site_url, $img_array;

foreach ( $img_array as $var ) {
$string = str_replace( ":$var", "<img src='$site_url/news/images/smilies/$var.gif'>", $string );
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