<?
ini_set('max_execution_time',360);
$stimer = explode( ' ', microtime() );
$stimer = $stimer[1] + $stimer[0];
function read_dir($dir) {
   $array = array();
   $d = dir($dir);
   while (false !== ($entry = $d->read())) {
       if($entry!='.' && $entry!='..') {
           $entry = $dir.$entry;
           if(is_dir($entry)) {
               $array = array_merge($array, read_dir($entry.'/'));
           } else {
               $array[] = $entry;
           }
       }
   }
   $d->close();
   return $array;
}
$array = read_dir('users/');
foreach($array as $value) {
list($width,$height,$type,$attr) = getimagesize($value);
if ($width) {
switch ($type) {
case 3:
$file = strstr($value,"/images/");
$file = str_replace("/images/","",$file);
$uname = str_replace("/","",str_replace("$file","",str_replace("/images","",str_replace("users/","",$value))));
echo $uname;
$ext = strstr($file,".");
$name = str_replace($ext,"",$file);
$path = str_replace($file,"",$value);
$path = $path . $name . ".png";
include "config.php";
$user = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `whatpulse` WHERE `user` = '$uname'"));
mysql_query("INSERT INTO `backgrounds` (`path`,`name`,`userid`) VALUES ('$path','$name','$user[id]')");
break;
}
}
}
$etimer = explode( ' ', microtime() );
$etimer = $etimer[1] + $etimer[0];

echo '<p style="margin:auto; text-align:center">';
$time = $etimer-$stimer;
printf( "Script timer: <b>%f</b> seconds.", ($etimer-$stimer) );
$total = $time / $rows;
echo "Avg time / user: $total seconds.";
echo '</p>';
?>