<?
if (!$action) {
$action = "Viewing " . $page;
}
$filename = 'foo/log.txt';
if (!file_exists($filename)) {
copy("foo/blank.txt",$filename);
chmod($filename,0646);
}
$somecontent = $_SERVER['REMOTE_ADDR'] . chr(31) . $page . chr(31) . $action . chr(31) . date("G:i:s") . chr(31) . date("d-m-y") . "\n";
if (is_writable($filename)) {
$handle = fopen($filename, 'a');
fwrite($handle, $somecontent);
   fclose($handle);
   }
?> 
