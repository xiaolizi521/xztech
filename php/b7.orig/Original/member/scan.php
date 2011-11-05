<?PHP

$file_title='Manga Scanlation Downloads';

$numbers = mysql_query ( "SELECT `chapternum` FROM manga_chapters WHERE stage > 0" );
$chapters = mysql_num_rows($numbers);

$chaptersLatest = mysql_query ( "SELECT `id` FROM manga_chapters WHERE stage > 0 ORDER BY id DESC LIMIT 1" );
$chapterCurrent = mysql_fetch_array($chaptersLatest);
$latest5 = $chapterCurrent['id'] - 10;



?>

<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Multimedia &gt; Manga Downloads &gt; Latest Bleach Manga</b><br />
<br />
</font><font face="Verdana" size="2"><b>Latest Bleach Manga Releases</b></font><font face="Verdana" size="1"><br />

<br />

<b>Bleach Manga Direct Downloads</b><br />

At Bleach7.com it is our on-going, fan motivated goal, to strive to be able to distribute the Bleach Manga Series to you, the valued visitor of our site, as well as we possibly can. We have put up every chapter of the series for you to enjoy. Each is from the Japanese manga series, and has been scanned and translated by either TW or Maximum7 or other smaller groups at one time or another. The speed scanlations are provided by MangaShare. Enjoy the downloads!<br />
<br />
<b>Donate</b><br />
Bleach7.com is a self-funded community that relies solely on user donations to get through the month. Although donations are not mandatory to download manga, every dollar counts and helps us afford the rising bandwith costs as the website gets more populated each and every day. Please consider [<a href="https://www.paypal.com/xclick/business=donate@maximum7.net&item_name=Bleach7.com&no_note=1&tax=0&currency_code=USD">Supporting B7 and Donating</a>].

<br /><br />


<div class="vol">Latest 10 Chapters</div><hr />

<table width="100%">

<?

for($i = $chapterCurrent['id']; $i > $latest5; $i--)
{
$index = mysql_query ( "SELECT * FROM manga_chapters WHERE id ='$i'" );
$chapterinfo = mysql_fetch_array($index);
$title = stripslashes ($chapterinfo['chaptertitle']);
$id = $chapterinfo['id'];
$shorttitle = Truncate($title, 80);
$chapterNumber = $chapterinfo['chapternum'];
$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";
 if($chapterinfo['stage'] == 1)//Give speedscan link
 {

if($i == 316)
{  $scantype = 'Download Chinese Scanlation'; }
else
{  $scantype = 'Download Speed Scanlation'; }

 echo'<tr bgcolor='.$color.'><td>Ch.'.$chapterNumber.': <em>'.$shorttitle.'</em></td>

 <td>

 <a href="index.php?page=media/downloads&file='.$id.'&t=speed">'.$scantype.'</a>

 </td>';
 }
 elseif($chapterinfo['stage'] == 2)//Give M7 Link
 {
 echo '<tr bgcolor='.$color.'><td>Ch.'.$chapterNumber.': <em>'.$shorttitle.'</em></td>
 <td>
 <a href="index.php?page=media/downloads&file='.$id.'&t=scan">Download Chapter</a>
 </td>
 ';
 }
}

?>

</table>



<div class="vol">Previous Chapters</div><hr />

<table width="100%">

<?PHP

for($i = 1; $i <= $chapters; $i++)

{



$index = mysql_query ( "SELECT * FROM manga_chapters WHERE chapternum ='$i'" );

$chapterinfo = mysql_fetch_array($index);



$color = ( $i % 2 == 0 ) ? "#eeeeee" : "";

//Volume covers, and titles. Copy and change as necessary to add new volume. $i is the chapter the volume starts with.

 if($i == 1)

 {echo '

 <tr><td colspan="2"><div style="font-weight:bold">Volume 1</div>"The Death and the Strawberry"<hr /> <a href="?page=media/downloads&t=volume&file=Bleach_v01">Download full volume</a> </td></tr>

 <tr><td rowspan="13" width="2" valign="top"><img src="media/images/covers/v01.gif"></td></tr>';}

 if($i == 8)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 2</div>"Goodbye Parakeet, Goodnite My Sista"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v02">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v02.gif"></td></tr>';}

 if($i == 17)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 3</div>"Memories in the Rain"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v03">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v03.gif"></td></tr>';}

 if($i == 26)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 4</div>"Quincy Archer Hates You"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v04">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v04.gif"></td></tr>';}

 if($i == 35)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 5</div>"Rightarm of the Giant"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v05">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v05.gif"></td></tr>';}

 if($i == 44)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 6</div>"The Death Trilogy Overture"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v06">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v06.gif"></td></tr>';}

 if($i == 53)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 7</div>"The Broken Coda"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v07">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v07.gif"></td></tr>';}

 if($i == 62)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 8</div>"The Blade and Me"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v08">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v08.gif"></td></tr>';}

 if($i == 71)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 9</div>"Fourteen Days For Conspiracy"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v09">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v09.gif"></td></tr>';}

 if($i == 80)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 10</div>"Tattoo on the Sky"<hr /> <a href="?page=media/downloads&t=volume&file=Bleach_v10">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v10.gif"></td></tr>';}

 if($i == 89)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 11</div>"A Star and a Stray Dog"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v11">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v11.gif"></td></tr>';}

 if($i == 99)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 12</div>"Flower on the Precipice"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v12">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v12.gif"></td></tr>';}

 if($i == 108)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 13</div>"The Undead"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v13">Download full volume</a> </td></tr>

 <tr><td rowspan="9" width="2" valign="top"><img src="media/images/covers/v13.gif"></td></tr>';}

 if($i == 116)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 14</div>"White Tower Rocks"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v14">Download full volume</a> </td></tr>

 <tr><td rowspan="9" width="2" valign="top"><img src="media/images/covers/v14.gif"></td></tr>';}

 if($i == 124)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 15</div>"Beginning of the Death of Tomorrow"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v15">Download full volume</a> </td></tr>

 <tr><td rowspan="8" width="2" valign="top"><img src="media/images/covers/v15.gif"></td></tr>';}

 if($i == 131)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 16</div>"Night of Wijnruit"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v16">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v16.gif"></td></tr>';}

 if($i == 140)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 17</div>"Rosa Rubicundior, Lilio Candidior"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v17">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v17.gif"></td></tr>';}

 if($i == 150)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 18</div>"The Deathberry Returns"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v18">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v18.gif"></td></tr>';}

 if($i == 159)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 19</div>"The Black Moon Rising"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v19">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v19.gif"></td></tr>';}

 if($i == 169)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 20</div>"End of Hypnosis"<hr /> <a href="?page=media/downloads&t=volume&file=Bleach_v20">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v20.gif"></td></tr>';}

 if($i == 179)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 21</div>"Be My Family or Not"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v21">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v21.gif"></td></tr>';}

 if($i == 188)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 22</div>"Conquistadores"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v22">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v22.gif"></td></tr>';}

 if($i == 198)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 23</div>"Mala Suerte!"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v23">Download full volume</a> </td></tr>

 <tr><td rowspan="9" width="2" valign="top"><img src="media/images/covers/v23.gif"></td></tr>';}

 if($i == 206)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 24</div>"Immanent God Blues"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v24">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v24.gif"></td></tr>';}

 if($i == 215)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 25</div>"No Shaking Throne"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v25">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v25.gif"></td></tr>';}

 if($i == 224)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 26</div>"The Mascaron Drive"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v26">Download full volume</a> </td></tr>

 <tr><td rowspan="11" width="2" valign="top"><img src="media/images/covers/v26.gif"></td></tr>';}

 if($i == 234)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 27</div>"Goodbye Halcyon Days"<hr /> <a href="?page=media/downloads&t=volume&file=Bleach_v27">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v27.gif"></td></tr>';}

 if($i == 243)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 28</div>"Baron&#39;s Lecture Full-Course"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v28">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v28.gif"></td></tr>';}

 if($i == 252)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 29</div>"The Slashing Opera"<hr />  <a href="?page=media/downloads&t=volume&file=Bleach_v29">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v29.gif"></td></tr>';}

 if($i == 261)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 30</div>"There is No Heart Without You"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v30">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v30.gif"></td></tr>';}

 if($i == 270)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 31</div>"Don\'t Kill My Volupture"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v31">Download full volume</a> </td></tr>

 <tr><td rowspan="10" width="2" valign="top"><img src="media/images/covers/v31.gif"></td></tr>';}

 if($i == 279)

 {echo '

 <tr><td colspan="2"><div class="vol">Volume 32</div>"Howling"<hr /><a href="?page=media/downloads&t=volume&file=Bleach_v32">Download full volume</a> </td></tr>
 <tr><td rowspan="9" width="2" valign="top"><img src="media/images/covers/v32.gif"></td></tr>';}

 if($i == 287)

 {echo '


 <tr><td colspan="2"><div class="vol">No released volume</div><hr /></td></tr>
 <tr><td rowspan="50" width="2" valign="top"><img src="media/images/covers/v00.gif"></td></tr>';}


 

$title = stripslashes ($chapterinfo['chaptertitle']);

$id = $chapterinfo['id'];



$shorttitle = Truncate($title, 30);



 if($i == 1)

 {

 echo '

 <tr>

 <td>Ch.1a: <em>Death & Strawberry</td>

 <td><a href="index.php?page=media/downloads&file=153486&t=scan">Download Chapter</a></td>

 </tr>

 <tr>

 <tr bgcolor="#eeeeee">

 <td>Ch.1b: <em>Death & Strawberry</td>

 <td><a href="index.php?page=media/downloads&file=153497&t=scan">Download Chapter</a></td>

 </tr>

 <tr>

 <tr>

 <td>Ch.1c: <em>Death & Strawberry</td>

 <td><a href="index.php?page=media/downloads&file=153498&t=scan">Download Chapter</a></td>

 </tr>

 <tr>

 ';

 }

 elseif($chapterinfo['stage'] == 1)//Give speedscan link
 {
 echo'<tr bgcolor='.$color.'><td>Ch.'.$i.': <em>'.$shorttitle.'</em></td>
 <td>
 <a href="index.php?page=media/downloads&file='.$id.'&t=speed">Download Speed Scanlation</a>
 </td>';
 }

 elseif($chapterinfo['stage'] == 2)//Give Final Link
 {
 echo '<tr bgcolor='.$color.'><td>Ch.'.$i.': <em>'.$shorttitle.'</em></td>
 <td>
 <a href="index.php?page=media/downloads&file='.$id.'&t=scan">Download Chapter</a>

 </td>
 </tr>
 ';
 }

echo '
</tr>
';
}

echo'
</table>';


//Negative chapters (Flashbacks)
echo'
<table width="100%">
<tr><td colspan="2"><div class="vol">Flashback Chapters</div><hr width="380" /></td></tr>
 <tr><td rowspan="20" width="84" valign="top"></td></tr>
 ';
//Chapters
echo'
 <tr bgcolor=""><td>Ch.-15: <em>Death in the Field of Ice</em></td>
  <td>
  <a href="index.php?page=media/downloads&file=53840&t=scan">Download Chapter</a>
  </td>
 </tr>

 <tr bgcolor="#eeeeee"><td>Ch.-108: <em>Turn Back the Pendulum</em></td>
  <td>
  <a href="index.php?page=media/downloads&file=153836&t=scan">Download Chapter</a>
  </td>
 </tr>

<tr bgcolor=""><td>Ch.-107: <em>Turn Back the Pendulum 2</em></td>
  <td>
  <a href="index.php?page=media/downloads&file=153837&t=scan">Download Chapter</a>
  </td>
 </tr>

<tr bgcolor=""><td>Ch.-107: <em>Turn Back the Pendulum 3</em></td>
  <td>
  <a href="index.php?page=media/downloads&file=153838&t=scan">Download Chapter</a>
  </td>
 </tr>


</table>';
?>