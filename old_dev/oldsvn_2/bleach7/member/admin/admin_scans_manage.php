<?PHP

$index = mysql_query ( "SELECT * FROM manga_chapters ORDER BY `chapternum` DESC" );
$chapters = mysql_num_rows($index);
$chapters -= 2;
$down = 0;
$views = 0;

?>
<table>
<tr>
<td><b>Stage</b></td><td><b>Chapter Title</b></td><td><b>Downloads</b></td><td><b>Raw<br />Downloads</b></td><td><b>Speed<br />Downloads</b></td><td><b>Online Reader</b></td>
</tr>
<?PHP
$i=1;
while($chapter = mysql_fetch_array ($index))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="'.$color.'"> 
 ';
 
 if($chapter['stage'] == 0)
 { echo '<td><a href="index.php?updatech=speed&id='.$chapter['id'].'">Raw</a></td>'; }
 
 elseif($chapter['stage'] == 1)
 { echo '<td><a href="index.php?updatech=final&id='.$chapter['id'].'">Speed</a></td>'; }
 else
 { echo '<td>Final</td> '; }
 echo'
 <td>'.$chapter['chapternum'].' '. stripslashes ($chapter['chaptertitle']).'</td>
 <td>
 '.number_format($chapter['downloads']).'
 </td> 
 <td>
 '.number_format($chapter['rawdown']).'
 </td> 
  <td>
 '.number_format($chapter['speeddown']).'
 </td> 
 <td>
 '.number_format($chapter['reader']).'
 </td> 
 </tr>
';

$down = $down + $chapter['downloads'] + $chapter['rawdown'] + $chapter['speeddown'];
$views = $views + $chapter['reader'];
}
echo 'Total downloads: '.number_format($down). '<br />Estimated total transfers: '.number_format($down*5) .' MB. <br />Total online reader chapter views: '.number_format($views);


?>
</table>

