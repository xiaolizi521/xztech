<?PHP

$id = $_GET['id'];

$art_query = mysql_query("SELECT * FROM `gallery` WHERE `id` =  $id");
$sel_art = mysql_fetch_array($art_query);
$file_title = 'Viewing '.$sel_art['title'];
$update_views = mysql_query("UPDATE `gallery` SET `views` =  (`views` + 1) WHERE `id` =  $id ");

echo '
<center>
<table width="100%">
<tr>
<td align="center" bgcolor="#eeeeee">
<strong>'.$sel_art['title'].'</strong><br />
';

list($width) = getimagesize($sel_art['location']);
if($width < 450)
{echo'<a href="'.$sel_art['location'].'"><img src="'.$sel_art['location'].'"><br /><a/><br />';}
else
{echo'<a href="'.$sel_art['location'].'"><img src="'.$sel_art['location'].'" width="450"></a><br />';}

echo'
</td>
</tr>
</table>
'.stripslashes($sel_art['comment']).'<br /><br />
<a href="index.php?page=fan/fanart&user='.$sel_art['poster'].'">View all fanart submissions by: '.$sel_art['poster'].'</a>
</center>';

?>