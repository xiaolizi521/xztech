<center>
<?
if(empty($_POST['gallerydo']))
{ $do = "null"; }
else
{ $do = $_POST['gallerydo']; }

if($do == 'APPROVE')
{
$id = mysql_escape_string($_POST['id']);
$update_message = mysql_query ( "UPDATE gallery SET approved='1' WHERE id='$id'" );
header('Location: http://www.bleach7.com/member/admin/index.php?action=galleryapprove');
}
if($do == 'DISAPPROVE')
{
$id = $_POST['id'];
$art_query = mysql_query("SELECT * FROM `gallery` WHERE id='$id' LIMIT 1;");
$sel_art = mysql_fetch_array($art_query);

$dirfull = '../../'.$sel_art['location'];
if(unlink($dirfull))
$dirthumb = '../../'.$sel_art['thumb'];
if(unlink($dirthumb))

$delete_entry = mysql_query ( "DELETE FROM gallery WHERE id='$id'" );

header('Location: http://www.bleach7.com/member/admin/index.php?action=galleryapprove');
}


$art_query = mysql_query("SELECT * FROM `gallery` WHERE `approved` = '0' ORDER BY `id` ASC LIMIT 1;");

while($sel_art = mysql_fetch_array($art_query))
{
 list($width) = getimagesize('http://www.bleach7.com/'.$sel_art['location']);
 if($width < 500)
 {echo'<a href="http://www.bleach7.com/'.$sel_art['location'].'"><img src="http://www.bleach7.com/'.$sel_art['location'].'" /></a><br />';}
 else
 {echo'<a href="http://www.bleach7.com/'.$sel_art['location'].'"><img src="http://www.bleach7.com/'.$sel_art['location'].'" width="500" /></a><br />';}
  
  if($sel_art['category'] == 1)
  { echo '<b>Fan art</b><br />';}
  elseif($sel_art['category'] == 2)
  { echo '<b>Wall Paper</b><br />';}
  else
  { echo '<b>Misc Image</b><br />';}
  echo '<b>Poster:</b> '.$sel_art['poster'].'<br />';
  echo '<b>Title:</b> '.stripslashes($sel_art['title']).'<br />';
  echo '<b>Comment:</b> '.stripslashes(htmlspecialchars($sel_art['comment'])).'<br />';
  echo '
  <form></form>
  <form method="post" action="?action=galleryapprove">
  <input type="hidden" name="id" value="'.$sel_art['id'].'" />
  <input type="submit" name="gallerydo" value="APPROVE" class="form" />
  <input type="submit" name="gallerydo" value="DISAPPROVE" class="form" />
  </form>
  ';
}

?>
</center>