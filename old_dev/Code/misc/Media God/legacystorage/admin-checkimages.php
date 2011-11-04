<?
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
$array = read_dir("users");
foreach ($array as $key => $value) {
	list($width, $height, $type, $attr) = @getimagesize($value);
if ($width > 500 || $height > 100) { 
echo $value . "($width x $height)<br>";
echo "<img src='$value'><br><br>";
}
}

?>