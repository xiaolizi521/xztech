<?PHP

$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' ORDER BY `episode` DESC" );
$anime = mysql_num_rows($index);

$number = mysql_fetch_array($index);
$epinumber = $number['episode'];

$down = 0;
echo'
<br /><br />
<table width="100%">
<tr>
<td><b>Dattebayo</b></td><td><b>Downloads</b></td>
</tr>';

$i=0;
$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' AND episode >= 100 ORDER BY `episode` DESC" );
while($chapter = mysql_fetch_array($anime_sub))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="'.$color.'"> 
 ';
 echo'
 <td>'.$chapter['episode'].' '. stripslashes ($chapter['title']).'</td>
 <td>
 '.$chapter['downloads'].'
 </td> 
</tr>
';
$down = $down + $chapter['downloads'];
}
$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' AND episode <= 99 ORDER BY `episode` DESC" );
while($chapter = mysql_fetch_array($anime_sub))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="'.$color.'"> 
 ';
 echo'
 <td>'.$chapter['episode'].' '. stripslashes ($chapter['title']).'</td>
 <td>
 '.$chapter['downloads'].'
 </td> 
</tr>
';
$down = $down + $chapter['downloads'];
}

echo'</table>';

echo'
<br /><br />
<table width="100%">
<tr>
<td><b>Lunar</b></td><td><b>Downloads</b></td>
</tr>';

$i=0;
$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE type = 'lun' ORDER BY `episode` DESC" );
while($chapter = mysql_fetch_array($anime_sub))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="'.$color.'"> 
 ';
 echo'
 <td>'.$chapter['episode'].' '. stripslashes ($chapter['title']).'</td>
 <td>
 '.$chapter['downloads'].'
 </td> 
</tr>
';
$down = $down + $chapter['downloads'];
}

echo'</table>';
echo '<br /><br />Total downloads: <b>'.number_format($down). '</b><br />Estimated total transfers: '.number_format($down*170).' MB.';

?>