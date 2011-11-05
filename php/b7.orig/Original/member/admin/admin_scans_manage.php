<?PHP

$index = mysql_query ( "SELECT `chapternum` FROM manga_chapters" );
$chapters = mysql_num_rows($index);
$chapters -= 2;
$down = 0;

?>
<table>
<tr>
<td><b>Stage</b></td><td><b>Chapter Title</b></td><td><b>Downloads</b></td><td><b>Raw<br />Downloads</b></td><td><b>Speed<br />Downloads</b></td>
</tr>
<?PHP
for($i = $chapters; $i != 0; $i--)
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";

$mangach = mysql_query ( "SELECT * FROM manga_chapters WHERE chapternum = $i" );
$chapter = mysql_fetch_array ($mangach);

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
 </tr>
';

$down = $down + $chapter['downloads'] + $chapter['rawdown'] + $chapter['speeddown'];
}
echo 'Total downloads: '.number_format($down). '<br />Estimated total transfers: '.number_format($down*5) .' MB.';


?>
</table>

