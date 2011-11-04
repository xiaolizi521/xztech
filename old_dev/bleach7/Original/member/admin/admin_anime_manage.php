<?PHP

$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' ORDER BY `episode` DESC" );
$anime = mysql_num_rows($index);

$number = mysql_fetch_array($index);
$epinumber = $number['episode'];

$down = 0;

?>
<br /><br />
<table width="100%">
<tr>
<td><b>Dattebayo</b></td><td><b>Downloads</b></td>
</tr>
<?PHP
for($i = $epinumber; $i != 133; $i--)
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";

$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE episode = $i AND type = 'sub'" );
$chapter = mysql_fetch_array ($anime_sub);

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
echo 'Total downloads: '.number_format($down). '<br />Estimated total transfers: '.number_format($down*170).' MB';


?>
</table>
<?


$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'flo' ORDER BY `episode` DESC" );
$anime = mysql_num_rows($index);

$number = mysql_fetch_array($index);
$epinumber = $number['episode'];

$down = 0;

?>
<br /><br />
<table width="100%">
<tr>
<td><b>Flomp-Rumbel</b></td><td><b>Downloads</b></td>
</tr>
<?PHP
for($i = $epinumber; $i != 133; $i--)
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";

$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE episode = $i AND type = 'flo'" );
$chapter = mysql_fetch_array ($anime_sub);

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
echo 'Total downloads: '.number_format($down). '<br />Estimated total transfers: '.number_format($down*170) .' MB.';


?>
</table>
<?
$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'raw' ORDER BY `episode` DESC" );
$anime = mysql_num_rows($index);

$number = mysql_fetch_array($index);
$epinumber = $number['episode'];

$down = 0;

?>
<br /><br />
<table width="100%">
<tr>
<td><b>RAW</b></td><td><b>Downloads</b></td>
</tr>
<?PHP
for($i = $epinumber; $i != 133; $i--)
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";

$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE episode = $i AND type = 'raw'" );
$chapter = mysql_fetch_array ($anime_sub);

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
echo 'Total downloads: '.number_format($down). '<br />Estimated total transfers: '.number_format($down*170).' MB.';


?>
</table>