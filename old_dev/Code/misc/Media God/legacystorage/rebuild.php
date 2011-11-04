<?php
$content = "<whatpulse>\n";
$content .= file_get_contents("./bounceme.xml");

$content = preg_replace('/<user="(.*?)">/e',"'<user>\n<name>'.htmlspecialchars(urlencode(utf8_encode('\\1'))).'</name>'",$content);
$content = preg_replace('/<team="(.*?)">/e',"'<team>\n<name>'.htmlspecialchars(urlencode(utf8_encode('\\1'))).'</name>'",$content);
$content = preg_replace('/<country>(.*?)<\/country>/e',"'<country>'.htmlspecialchars(urlencode(utf8_encode('\\1'))).'</country>'",$content);
$content .= "</whatpulse>";
$fp = fopen('whatpulsenew.xml',"w+");

fputs($fp,$content);

?>
