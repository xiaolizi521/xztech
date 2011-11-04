<?PHP
require_once ('./header.php');
require_once ( "./member/db.php" );
require_once ( "member/settings.php" );
require_once ( "member/functions.php" );
require_once ( "./member/header.php" );

$early_chapter = 290;
$chapter = 291;

if(!isset($_GET['ch']))
{
 if(isset($_POST['ch']))
 {$ch = $_POST['ch'];}
 else
 {$ch = 'null';}
}
else
{ $ch = $_GET['ch']; }

echo '
<center>
<h1>Bleach7</h1>
<a href="#">Back to Bleach7</a><br />
';

//Front Selection
if($ch=='null')
{
 echo'
 <form action="mangaview.php" method="post">
 <select name="ch" class="form">
 <option value="null">Select Chapter</option>
 ';
 for($i = $chapter; $i >= $early_chapter; $i--)
 {
 echo'
 <option value="'.$i.'">Chapter '.$i.'</option>
 ';
 }
 echo'
 </select>
 <input type="submit" value="Go" class="form" />
 </form>
 <br /><br />
 ';
}

elseif($ch != 'null')
{
 //Set Directory and file count
 $dir = 'member/images/manga/Bleach_Ch'.$ch.'_M7/';
 $count = count(glob($dir . "*")); 

 //Set Page
 if(!isset($_GET['pg']))
 { $pg = 'credits'; }
 else
 { $pg = $_GET['pg']; }


//show page
 if($pg == 'credits')
 {
 echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_00a.jpg" /><br />';
  if(file_exists($dir.'M7_Bleach_Ch'.$ch.'_00b.png'))
  { echo '<a href="mangaview.php?ch='.$ch.'&pg=00b">Next >></a>'; }
  else
  { echo '<a href="mangaview.php?ch='.$ch.'&pg=01">Next >></a>'; }
 }
 elseif($pg == '00b')
 {
 echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_00b.png" /><br />';
  if(file_exists($dir.'M7_Bleach_Ch'.$ch.'_00c.png') || file_exists($dir.'M7_Bleach_Ch'.$ch.'_00c.jpg'))
  { echo '<a href="mangaview.php?ch='.$ch.'&pg=credits">Previous</a> <a href="mangaview.php?ch='.$ch.'&pg=00c">Next >></a>'; }
  else
  { echo '<a href="mangaview.php?ch='.$ch.'&pg=credits">Previous</a> <a href="mangaview.php?ch='.$ch.'&pg=01">Next >></a>'; }
 } 
 elseif($pg == '00c')
 {
 echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_00c.png" /><br />';
 echo '<a href="mangaview.php?ch='.$ch.'&pg=00b">Previous</a> <a href="mangaview.php?ch='.$ch.'&pg=01">Next >></a>'; 
 }
 
 elseif($pg != 'credits' && $pg != '00b' && $pg != '00c')
 {
 $next = $pg+1;
 $next2 = $pg+2;
 $last = $pg-1;
 if($next < 10)
 { $next = '0'.$next;}
  if($next2 < 10)
 { $next2 = '0'.$next2;}
 if($last < 10)
 { $last = '0'.$last;}
 
  if(file_exists($dir.'M7_Bleach_Ch'.$ch.'_'.$pg.'.png'))
  {
  echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_'.$pg.'.png" /><br />';
  echo '<a href="mangaview.php?ch='.$ch.'&pg='.$last.'"><< Previous</a> <a href="mangaview.php?ch='.$ch.'&pg='.$next.'">Next >></a>';
  }
  else
  {
  echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_'.$pg.'-'.$next.'.png" /><br />';
  echo '<form method="get" action="?page=mangaviewer.php></form>';
  echo '<a href="mangaview.php?ch='.$ch.'&pg='.$last.'"><< Previous</a> <a href="mangaview.php?ch='.$ch.'&pg='.$next2.'">Next >></a>';
  }
 }

}


?>
<br /><br />
</center>
</body>
</html>