<?php
$ftp = ftp_connect("localhost","21");

$login = ftp_login($ftp,"offbea2","chicaly86");

ftp_site($ftp,"CHMOD 666 ./public_html/pulse/sig/AgentGreasy.png");

ftp_close($ftp);

?>