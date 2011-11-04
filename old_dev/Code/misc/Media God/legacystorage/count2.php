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
$a = read_dir("img");
foreach ($a as $key => $value) {
if (is_file($value)) {
$path = $value;
$name = str_replace(".png","",$value);
$name = str_replace("img/","",$name);
mysql_query("INSERT INTO `backgrounds` (`path`,`name`) VALUES ('$path','$name')");
}
}
?>
