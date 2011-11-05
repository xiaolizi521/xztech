<form action="index.php" method="post" class="form">
<input type="submit" name="timecop" value="Release Dattebayo Episode" class="form" />
</form>
<?PHP
if(isset ($_POST['timecop']))
{
$headline_db = htmlspecialchars($show_news['headline']);
$news_db = htmlspecialchars($show_news['news']);

$expTitle = explode(" ", $headline_db);
 if(isset ($expTitle['4']))
 {
 $expTitle_ext = explode(" | ", $headline_db);
 }
$expDB = explode("[Dattebayo] Bleach Episode $expTitle[2]", $news_db);
$headline_db = $expTitle['0'].' '.$expTitle['1'].' '.$expTitle['2'].' SUB';
if(isset($expTitle_ext['1']))
{ $headline .=  ' | '.$expTitle_ext['1']; }
$news_db = $expDB['0'].'<a href="http://www.dattebayo.com/t/b'.$expTitle['2'].'.torrent">[Dattebayo] Bleach Episode '.$expTitle['2'].'</a>'.$expDB['1'];
$nid = $show_news['id'];

$update_news = mysql_query ( "UPDATE `bleach7_b7`.`news` SET `headline`='$headline_db', `news`='$news_db', `db`='0' WHERE `id`='$nid'" );
echo "<script>alert('zomg, dattebayo bleach episode $expTitle[2] has been released! thank you for pressing the timecop button! bleachtards rejoice!')</script>";
}


?>