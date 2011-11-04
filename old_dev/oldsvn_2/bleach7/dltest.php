<?PHP

$type = $_GET['type'];

$anime = '2, 6, 7, 8';
$music = '4, 8, 3, 2, 1';


$server = rand(1,7);


if($type == 'anime')
{
 $exanime = explode(', ', $anime);
 $count = count($exanime);
 $count --;
 $rand = rand(0, $count);
 $server = $exanime[$rand];
}
if($type == 'music')
{
 $exmusic = explode(', ', $music);
 $count = count($exmusic);
 $count --;
 $rand = rand(0, $count);
 $server = $exmusic[$rand];
}


echo 'ms'.$server.'.bleach7.com';


?>