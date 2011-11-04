<?
include "config.php";
//start timer
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];

$string = file_get_contents("sigs.xml");
$array = explode("<user>",$string);
$elements = count($array);
while ($x < $elements) {
	$x++;
$find = array("<name>", "</name>", "<country>", "</country>", "<rank>", "</rank>", "<keys>", "</keys>", "<clicks>", "</clicks>", "<team>", "</team>", "</user>", chr(9));
$replace = array("",chr(24),"",chr(24),"",chr(24),"",chr(24),"",chr(24),"",chr(24),chr(24)," ");
			 $array[$x] = str_replace($find,$replace,$array[$x]);
$array[$x] = explode(chr(24),$array[$x]);
print_r($array[$x]);
echo $array[$x][0];
mysql_query("UPDATE `whatpulse` SET `country` = $array[$x][1] WHERE `username` = '$array'") or die("Check one two" . mysql_error());
break;
}
foreach ($array as $key => $value)
$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];
echo '<p style="margin:auto; text-align:center">';
$time = $etimer-$stimer;
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
echo '</p>';