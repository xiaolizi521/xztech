<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Bleach7_Main = "69.46.28.61";
$database_Bleach7_Main = "admin_interaction";
$username_Bleach7_Main = "admin_b7";
$password_Bleach7_Main = "intpass980";
$Bleach7_Main = mysql_pconnect($hostname_Bleach7_Main, $username_Bleach7_Main, $password_Bleach7_Main) or trigger_error(mysql_error(),E_USER_ERROR); 
?>