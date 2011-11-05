<?PHP
/*
Mirror URLs
zen = http://m1.bleach7.com
neo = http://m3.bleach7.com
ulz = http://m4.bleach7.com
osoi = http://m6.bleach7.com
dzerox = http://www.m5.bleach7.com
*/

//Set Server Mirrors
$manga = array(3, 4, 5);
$volume = array(3, 4, 5);
$speed = array(3, 4, 5);
$music = array(4);
$anime = array(6, 1);

// $gid Group ID's for MANGA downloads
//
// 0 = M7
// 1 = Pre-M7
// 2 = Speed Scan
// 3 = M7 v2
// 4,5,6 = Chapter 1 parts
//

//Set Variables
$file = $_GET['file'];
$time = time();
$type = $_GET['t'];
$file2 = $_GET['file'];
//set flood time limit
$floodlimit = 10;
$floodlimit_big = 30;

$usertime = $time - $user_info['flood'];

//Set File Size
if($type == 'volume' || $type == 'animeraw' || $type == 'animesub' || $type == 'album' || $type == 'musicv')
{$size='big';}
else
{$size='small';}

if($usertime > $floodlimit)
{
if(isset($file) && isset($type))
{
 //File is a MANGA
 if($type == 'scan' || $type == 'raw')
 {
  $result_dl_list = mysql_query( "SELECT * FROM manga_chapters WHERE id = '$file'" );
    //Result found, get to work.
	if ( mysql_num_rows ($result_dl_list) > 0 ) 
	{
	$dla = mysql_fetch_array($result_dl_list);
	$gid = $dla['groupid'];
	$file_title = "Downloading Manga Chapter $dla[chapternum]";
	
	//Get File Mirrors
	$count = count($manga);
	$count --;
	$rand = rand(0, $count);
	$server = $manga[$rand];
	
	//Set File
	if($gid == 0 && $type == 'scan') // File is M7
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'_M7.zip'; }
	if($gid == 1 && $type == 'scan') // File is NOT M7
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'.zip'; }
	if($gid == 3 && $type == 'scan') // File is M7 v2
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'v2_M7.zip'; }	
	if($gid == 4 && $type == 'scan') // File is Chapter 1 a
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'a.zip'; }	
	if($gid == 5 && $type == 'scan') // File is Chapter 1 b
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'b.zip'; }		
	if($gid == 6 && $type == 'scan') // File is Chapter 1 c
	{ $file = 'files/bleach/Bleach_Ch'.$dla['chapternum'].'c.zip'; }
	if($type == 'raw') // File is RAW
	{ $file_title = "Downloading Manga RAW $dla[chapternum]";
	  $file = 'files/bleach/raw/Bleach_ch'.$dla['chapternum'].'_[RAW].zip'; }	
	
	
	//Update Download Count
	if($type == 'scan')//upload download counts based on type
	{$update_downloads = mysql_query ( "UPDATE manga_chapters SET downloads=( `downloads` + 1 ) WHERE id='$file2'" );}
	elseif($type == 'raw')
	{$update_downloads = mysql_query ( "UPDATE manga_chapters SET rawdown=( `rawdown` + 1 ) WHERE id='$file2'" );}
	//END MANGA
	
	//Update flood check time
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
	}
	else
	{ 
	header('Location: http://www.bleach7.com');
	die('File note found!');
	}
 }
 //Files is SPEED SCAN
 elseif($type == 'speed')
 {
  $result_dl_list = mysql_query( "SELECT * FROM manga_chapters WHERE id = '$file'" );
    //Result found, get to work.
	if ( mysql_num_rows ($result_dl_list) > 0 ) 
	{
	$dla = mysql_fetch_array($result_dl_list);
	$gid = $dla['groupid'];
	$file_title = "Downloading Manga Speed-Scan $dla[chapternum]";
	
	//Get File Mirrors
	$count = count($speed);
	$count --;
	$rand = rand(0, $count);
	$server = $speed[$rand];
	
	//Set File
	//$file = 'files/bleach/ch/[JOJO]Bleach_ch'.$dla['chapternum'].'.zip';
	$file = 'files/bleach/speed/Bleach_'.$dla['chapternum'].'[MS].zip';
	
	//Update Download Count
	if($type == 'speed')
	{$update_downloads = mysql_query ( "UPDATE manga_chapters SET speeddown=( `speeddown` + 1 ) WHERE id='$file2'" );}
	
	//Update flood check time
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
	}
 }
 //File is MUSIC
 elseif($type == 'music')
 {
	//Get File Mirrors
	$count = count($music);
	$count --;
	$rand = rand(0, $count);
	$server = $music[$rand];
	$file_title = "Downloading Bleach Music";
	
	//Set File
	$file = 'files/music/'.$file.'.zip';
	
	
	//Update Download Count
	$update_downloads = mysql_query ( "UPDATE tracking SET value=( `value` + 1 ) WHERE name='musicDL'" );	
	//Update flood check time
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
 }
 //file is ALBUM
 elseif($type == 'album')
 {
	//Get File Mirrors
	$count = count($music);
	$count --;
	$rand = rand(0, $count);
	$server = $music[$rand];
	$file_title = "Downloading Bleach Music Album";

	//Set File
	$file = 'files/music/'.$file.'.zip';
	
	//Update Download Count
	$update_downloads = mysql_query ( "UPDATE tracking SET value=( `value` + 1 ) WHERE name='albumDL'" );
	//Update flood check time
	$dif = $floodlimit_big - $floodlimit;
    $time += $dif;
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
 }
 //file is VIDEO
 elseif($type == 'musicv')
 {
	//Get File Mirrors
	$count = count($music);
	$count --;
	$rand = rand(0, $count);
	$server = $music[$rand];
    $file_title = "Downloading Music Video";

	//Set File
	$file = 'files/music/'.$file.'.zip';
	
	//Update Download Count
	$update_downloads = mysql_query ( "UPDATE tracking SET value=( `value` + 1 ) WHERE name='music_videoDL'" );
	//Update flood check time
	$dif = $floodlimit_big - $floodlimit;
    $time += $dif;
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
 }
 //file is VOLUME
 elseif($type == 'volume')
 {
	//Get File Mirrors
	$count = count($volume);
	$count --;
	$rand = rand(0, $count);
	$server = $volume[$rand];
    $file_title = "Downloading Manga Volume";

	//Set File
	$file = 'files/bleach/volumes/'.$file.'.zip';
	
	//Update Download Count
	$update_downloads = mysql_query ( "UPDATE tracking SET value=( `value` + 1 ) WHERE name='volumeDL'" );
	//Update flood check time
	$dif = $floodlimit_big - $floodlimit;
    $time += $dif;
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
 }
 //file is ANIME
 if($type == 'animeraw' || $type == 'animesub' || $type == 'animesubf-r')
 {
  $result_dl_list = mysql_query( "SELECT * FROM episodes WHERE id = '$file'" );
    //Result found, get to work.
	if ( mysql_num_rows ($result_dl_list) > 0 ) 
	{
	$dl = mysql_fetch_array($result_dl_list);
	
	//Get File Mirrors
	$count = count($anime);
	$count --;
	$rand = rand(0, $count);
	$server = $anime[$rand];
	
	//Set File
	if($type == 'animeraw') // File is RAW
	{ 
	$server = '4';
	$file_title = "Downloading Anime RAW $dl[episode]";
	$file = 'files/bleach/anime/[S^M]_Bleach_'.$dl['episode'].'_RAW.avi'; 
	}
	if($type == 'animesub') // File is DB
	{ 
	$file_title = "Downloading Anime Episode $dl[episode] by Dattebayo";
	$file = 'files/bleach/anime/[DB]_Bleach_'.$dl['episode'].'_['.$dl['crc'].'].avi'; 
	}
	if($type == 'animesubf-r') // File is F-R
	{ 
	$file_title = "Downloading Anime Episode $dl[episode] by Flomp-Rumbel";
	$file = 'files/bleach/anime/[Flomp-Rumbel]_Bleach_-_'.$dl['episode'].'_['.$dl['crc'].'].mkv'; 
	}


	//Update Download Count
	$update_downloads = mysql_query ( "UPDATE episodes SET downloads=( `downloads` + 1 ) WHERE id='$file2'" );	
	//Update flood check time
	$dif = $floodlimit_big - $floodlimit;
    $time += $dif;
    $update_flood = mysql_query ( "UPDATE users SET flood='$time' WHERE user_id='".$user_info['user_id']."'" );
	}
	else
	{ 
	header('Location: http://www.bleach7.com');
	die('File note found!');
	}

 }
 
 
 //Give Download
 if($server != 5)
 { header('Location: http://m'.$server.'.bleach7.com/'.$file);}
 else
 { header('Location: http://m'.$server.'.bleach7.com/m7/'.$file); }
}
}
//Tripped floodcheck
else
 {
  if($size == 'small')
  {
  $timeleft = $floodlimit - $usertime;
  echo "
  <b>Error!</b>
  <br />
  You must wait $floodlimit seconds between downloading files.<br /><br />Try again in $timeleft seconds.";
  }
  else
  {
  $timeleft = $floodlimit_big - $usertime - 20;
  echo "
  <b>Error!</b>
  <br />
  You must wait $floodlimit_big seconds between downloading files.<br /><br />Try again in $timeleft seconds.";
  }
 } 
?>