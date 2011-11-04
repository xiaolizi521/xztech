<?

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
$dir = read_dir("img");
print_r($dir);
foreach ($dir as $key => $value) {
$name = str_replace("img/","",$value);
$name = str_replace(".png","",$name);
include "config.php";
mysql_query("INSERT INTO `backgrounds` (`name`,`path`) VALUES ('$name','$value')") or die(mysql_error());
}
?>