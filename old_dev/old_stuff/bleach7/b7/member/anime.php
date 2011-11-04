<?PHP
$time = time();
$file_title='Anime Direct Downloads';

// <a href="?page=media/downloads&t=dlanime&key=&file=',$chapter['id'],'">Download Episode!</a>

?>
<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Multimedia &gt; Anime Downloads &gt; Latest Bleach Anime</b><br />
<br />
</font><font face="Verdana" size="2"><b>Latest Bleach Anime Downloads</b></font><font face="Verdana" size="1"><br />
<br />
<b>Bleach Anime Direct Downloads</b><br />
Episodes 1-70 are subbed by Lunar Fansubs. Episodes 71-curent the are subbed by Dattebayo unless otherwise noted.<br />
<br />
<b>Donate</b><br />
Bleach7.com is a self-funded community that relies solely on user donations to get through the month. Although donations are not mandatory to download anime, every dollar counts and helps us afford the rising bandwith costs as the website gets more populated each and every day. Please consider [<a href="https://www.paypal.com/xclick/business=donate@maximum7.net&item_name=Bleach7.com&no_note=1&tax=0&currency_code=USD">Supporting B7 and Donating</a>].

<br /><br />

<div class="vol">Latest 10 Releases</div><hr />
<table width="100%">
<?PHP

$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' AND episode > 100 ORDER BY `episode` DESC LIMIT 10" );
$i = 0;
while($chapter = mysql_fetch_array($index))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="',$color,'"> 
 ';
 echo'
 <td>',$chapter['episode'],': ', truncate(stripslashes ($chapter['title']), 55),'</td>
 <td>
 <a href="?page=media/dlanime&key=',encKey($time),'&file=',$chapter['id'],'">Download Episode!</a>
 </td> 
</tr>
';
}

?>
</table>

<div class="vol">All releases</div><hr />
<table width="100%">
<?PHP
$arc = 1;

$index = mysql_query ( "SELECT * FROM episodes WHERE episode < 100  ORDER BY `episode` ASC" );
$i = 0;
while($chapter = mysql_fetch_array($index))
{
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;

if($arc == 1)
{ echo'<tr><td colspan="2"><div class="vol">The Substitute arc</div><hr /></td></tr>'; }
if($arc == 21)
{ echo'<tr><td colspan="2"><div class="vol">Soul Society arc</div><hr /></td></tr>'; }
if($arc == 63)
{ echo'<tr><td colspan="2"><div class="vol">The Bount arc</div> <small><em>(filler)</em><small><hr /></td></tr>'; }

echo'
<tr bgcolor="',$color,'"> 
 ';
 echo'
 <td>',$chapter['episode'],': ', truncate(stripslashes ($chapter['title']), 55),'</td>
 <td>
 <a href="?page=media/dlanime&key=',encKey($time),'&file=',$chapter['id'],'">Download Episode!</a>
 </td> 
</tr>
';
$arc++;
}
$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'sub' AND episode > 99  ORDER BY `episode` ASC" );
$i = 0;
while($chapter = mysql_fetch_array($index))
{
	
if($arc == 105)
{ echo'<tr><td colspan="2"><div class="vol">The Arrancar arc</div><hr /></td></tr>'; }
if($arc == 139)
{ echo'<tr><td colspan="2"><div class="vol">Hueco Mundo arc</div><hr /></td></tr>'; }
if($arc == 163)
{ echo'<tr><td colspan="2"><div class="vol">The Kasumi-Ooji arc</div> <small><em>(filler)</em><small><hr /></td></tr>'; }

$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
$i++;
echo'
<tr bgcolor="',$color,'"> 
 ';
 echo'
 <td>',$chapter['episode'],': ', truncate(stripslashes ($chapter['title']), 55),'</td>
 <td>
 <a href="?page=media/dlanime&key=',encKey($time),'&file=',$chapter['id'],'">Download Episode!</a>
 </td> 
</tr>
';
 $arc++;
}

?>
</table>