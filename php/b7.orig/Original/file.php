<?php
$dir = "/home/bleach7/public_html/member/images/manga/Bleach_Ch266_M7/";

$dh = opendir($dir);

while (($file = readdir($dh)) !== false) {
        echo "<A HREF=\"$file\">$file</A><BR>\n";
}

closedir($dh);
?>