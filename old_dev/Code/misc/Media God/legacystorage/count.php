<? include "config.php";
$r = mysql_num_rows(mysql_query("SELECT * FROM `whatpulse` WHERE `password` != ''"));
echo $r;
?>