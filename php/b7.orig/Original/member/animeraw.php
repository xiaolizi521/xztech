<?PHP
$file_title='Anime Raw Downloads';



$index = mysql_query ( "SELECT * FROM episodes WHERE type = 'raw' ORDER BY `episode` DESC LIMIT 5" );

$anime = mysql_num_rows($index);



$number = mysql_fetch_array($index);

$epinumber = $number['episode'];

$limit = $number['episode'] - 5;



?>

<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Multimedia &gt; Anime Downloads &gt; Latest Bleach Anime Raws</b><br />

<br />

</font><font face="Verdana" size="2"><b>Latest Bleach Anime Raw Downloads</b></font><font face="Verdana" size="1"><br />

<br />

<b>Bleach Anime Raw Direct Downloads</b><br />

Episodes older than five week old will be removed, so grab them while they're hot!<br />
<br />
<b>Donate</b><br />
Bleach7.com is a self-funded community that relies solely on user donations to get through the month. Although donations are not mandatory to download anime, every dollar counts and helps us afford the rising bandwith costs as the website gets more populated each and every day. Please consider [<a href="https://www.paypal.com/xclick/business=donate@maximum7.net&item_name=Bleach7.com&no_note=1&tax=0&currency_code=USD">Supporting B7 and Donating</a>].

<br /><br />


<div class="vol">Latest Raws</div><hr />

<table width="100%">

<?PHP





for($i = $epinumber; $i != $limit; $i--)

{

$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";



$anime_sub = mysql_query ( "SELECT * FROM episodes WHERE episode = $i AND type = 'raw'" );

$chapter = mysql_fetch_array ($anime_sub);



echo'

<tr bgcolor="'.$color.'"> 

 ';

 echo'

 <td>Episode '.$chapter['episode'].': '. stripslashes ($chapter['title']).'</td>

 <td>

 <a href="?page=media/downloads&t=animeraw&file='.$chapter['id'].'">Download RAW!</a>

 </td> 

</tr>

';

}

?>

</table><br />