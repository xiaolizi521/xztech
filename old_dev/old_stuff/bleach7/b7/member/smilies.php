<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "settings.php" );
?>
<html>
<head>
<title><?php echo "$site_name - Smilies" ?></title>
<style>
a:link,a:visited,a:active { 
text-decoration: none;
font-family: Verdana;
font size: 10px;
color: #000000;
}
a:hover { 
text-decoration: underline;
font-family: Verdana;
font size: 10px;
color: #000000;
}
.main { 
text-decoration: none; 
font-family: Verdana; 
font-size: 10px; 
color: #000000 
}
</style>
<script>
function InsertSmile( expression ) {
opener.document.comment_form.comment_post.value += ':'+expression+' ';
}
</script>
</head>
<?php
if ( $handle = opendir ( "images/smilies" ) ) {
while ( false !== ( $file = readdir ( $handle ) ) ) { 
if ( $file != "." && $file != ".." && ereg ( ".gif", $file ) ) { 
$smile_name = str_replace ( ".gif", "", $file );
$smilies_array[] = $smile_name;
} 
}
closedir( $handle ); 
}

echo "<fieldset class=\"main\"><legend>Smilies</legend><table cellpadding=\"0\" cellspacing=\"0\"><tr><td>";
echo "<table cellpadding=\"5\" cellspacing=\"0\" class=\"main\">";
sort ( $smilies_array );
foreach ( $smilies_array as $var ) {
echo "<tr>";
echo "<td><a href=\"#insertsmile\" onclick=\"InsertSmile( \"$var\" )\"><img src=\"$site_url/$script_folder/images/smilies/$var.gif\" alt=\"$var\" border=\"0\"></a></td>";
echo "<td><a href=\"#insertsmile\" onclick=\"InsertSmile( \"$var\" )\">:$var</a></td>";
}
echo "</table>";
echo "</td></tr></table></fieldset>";
?>
