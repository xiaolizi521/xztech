<?
include "config.php";
function read_dir($dir) {
   $array = array();
   $d = dir($dir);
   while (false !== ($entry = $d->read())) {
       if($entry!='.' && $entry!='..') {
           $entry = $dir.'/'.$entry;
           if(is_dir($entry)) {
               $array[] = $entry;
               $array = array_merge($array, read_dir($entry));
           } else {
               $array[] = $entry;
           }
       }
   }
   $d->close();
   return $array;
}
$array = read_dir("img");
foreach ($array as $key => $value) {
	$ext = strrchr($value,".");
	$find[] = "img/";
	$find[] = $ext;
	$name = str_replace($find,"",$value);
	mysql_query("INSERT INTO `backgrounds` (`path`,`userid`,`name`) VALUES ('$value','0','$name')");
}
?>