<?PHP
##############################
#  Bleach7 Gallery           #
#      By ExiledVip3r        #
##############################
$file_title = 'Online Manga';

?>
<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Gallery &gt; Online Manga</b><br />
<br />
<?PHP

 $early_chapter = 290;
 
 $chapter = 291;

if(!isset($_GET['chapter']))
{
 if(isset($_POST['chapter']))
 {$ch = $_POST['chapter'];}
 else
 {$ch = 'null';}
}
else
{ $ch = $_GET['chapter']; }


//Front Selection
if($ch=='null')
{
 echo'
 <center>
 <form action="mangaview.php" method="post">
 <select name="chapter" class="form">
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
 </center><br /><br />
 ';
}

elseif($ch != 'null')
{
$dir = 'member/images/manga/Bleach_Ch'.$ch.'_M7/';
$count = count(glob($dir . "*")); 

for($i = 1; $i <= $count; $i++)
{
if($i < 10)
{$i = '0'.$i;}


echo '<img src="'.$dir.'M7_Bleach_Ch'.$ch.'_'.$i.'.png" width="550" /><br />';
}


}


?>