<?php
require_once("include.php");

if (!isset($_GET['type'])) {
header("Content-type: text/html; charset=utf-8");
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
?>
<html>
<head>
<title>Blog Torrent Download</title>
<link rel="stylesheet" type="text/css" href="basic.css" />
</head>
<body onload="javascript:sendTorrent();">
<div id="rap">
<?php draw_detect_scripts();
      draw_download_link(isset($_GET['file'])?$_GET['file']:''); ?>
</div>
</body>
</html>
<?php } else { //We're sending an exe

//Prevent ../ or / file name attacks
$firstchar = substr($_GET['file'],0,1);
if ($firstchar == '.' || $firstchar == DIRECTORY_SEPARATOR ||
    $firstchar == '/')
  die('Bad file name');

//Give an error if the file is missing
//FIXME This assumes that the torrents are always stored as flat files  
if(!(file_exists("torrents/".$_GET['file'])))
	die("File not found.");
	
$_GET['file']=str_replace(DIRECTORY_SEPARATOR, "", $_GET['file']);
$_GET['file']=str_replace('/', "", $_GET['file']);

if ($_GET['type'] == 'exe') {
  send_installer($_GET['file']);
}  elseif ($_GET['type'] == 'mac') {
  send_mac_installer($_GET['file']);
}elseif ($_GET['type'] == 'torrent') {
  header('Content-type: application/x-bittorrent');
  header('Content-Disposition: inline; filename="'.htmlspecialchars($_GET['file']).'"');

  echo $store->getRawTorrent($_GET['file']);

} else {
  echo "Unknown file type. Something is seriously wrong, dude. Please email your site administrator.";
}
}