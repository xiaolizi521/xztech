<?
include "config.php";
function read_dir($dir) {
   $array = array();
   $d = dir($dir);
   while (false !== ($entry = $d->read())) {
       if($entry!='.' && $entry!='..') {
           $entry = $dir.'/'.$entry;
           if(is_dir($entry)) {
               $array = array_merge($array, read_dir($entry));
           } else {
               $array[] = $entry;
           }
       }
   }
   $d->close();
   return $array;
}
$dir = "img";
$info = read_dir($dir);
foreach($info as $key => $value) {
$name = str_replace("img/","",$value);
$name = str_replace(".png","",$name);
mysql_query("INSERT INTO `backgrounds` (`path`,`name`) VALUES ('$value','$name')");
}
?>