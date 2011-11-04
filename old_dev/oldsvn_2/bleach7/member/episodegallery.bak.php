<?PHP

##############################

#  Bleach7 Gallery           #

#      By ExiledVip3r        #

##############################

$file_title = 'Episode Image Gallery';



?>

<p /><font face="Verdana" size="1" id="content_title"><b>Bleach 7 &gt; Gallery &gt; Episode Gallery</b><br />

<br />

<?PHP

 $episodes = 168;

if(!isset($_GET['episode']))

{

 if(isset($_POST['episode']))

 {$epi = $_POST['episode'];}

 else

 {$epi = 'null';}

}

else

{ $epi = $_GET['episode']; }







if($epi=='null')

{

 echo'

 <center>

 <form action="?page=member/episodegallery" method="post">

 <select name="episode" class="form">

 <option value="null">Select Episode Gallery</option>

 <option value="mon">Memories of Nobody</option>

 ';

 for($i = $episodes; $i != 0; $i--)

 {

  

  if($i == 75 || $i == 76 )

  {

  echo'

  <option value="75-076">Episode 75-76</option>

  ';

  $i = 75;

  }

  elseif($i == 84 || $i == 85 )

  {

  echo'

  <option value="84-085">Episode 84-85</option>

  ';

  $i = 84;

  }

  elseif($i == 73 || $i == 74 )

  {

  echo'

  <option value="73-074">Episode 73-74</option>

  ';

  $i = 73;

  }

  elseif($i == 68 || $i == 69 )

  {

  echo'

  <option value="68-069">Episode 68-69</option>

  ';

  $i = 68;

  }

  elseif($i == 52 || $i == 53 )

  {

  echo'

  <option value="52-053">Episode 52-53</option>

  ';

  $i = 52;

  }

  else

  {

 echo'

 <option value="'.$i.'">Episode '.$i.'</option>

 ';

  }

 }

 echo'

 </select>

 <input type="submit" value="Go" class="form" />

 </form>

 </center><br /><br />

 ';

}





elseif($epi != 'mon')

{

if($epi < 10)

{$epi = '00'.$epi;}

if($epi >= 10 && $epi < 100)

{$epi = '0'.$epi;}







$dir = "member/images/screencaps/Episode $epi/";



$dir_path = "member/images/screencaps/Episode $epi/";

$count = count(glob($dir_path . "*")); 

if($epi == 144)
{$omni = 'Screenshots used with permission from <a href="http://randomc.animeblogger.net/" target="_blank">Random Curiosity</a>.<br /><br />';}
else
{$omni = '';}



echo'



<table width="100%">

<tr>

<td align="left"><h3>Bleach Episode '.$epi.' Gallery</h3></td>
<td align="right">

 <form action="?page=member/episodegallery" method="post">

 <select name="episode" class="form">

 <option value="null">Episode '.$epi.'</option>

 <option value="mon">Memories of Nobody</option>

 ';

 for($i = $episodes; $i != 0; $i--)

 {

  

  if($i == 75 || $i == 76 )

  {

  echo'

  <option value="75-076">Episode 75-76</option>

  ';

  $i = 75;

  }

  elseif($i == 84 || $i == 85 )

  {

  echo'

  <option value="84-085">Episode 84-85</option>

  ';

  $i = 84;

  }

  elseif($i == 73 || $i == 74 )

  {

  echo'

  <option value="73-074">Episode 73-74</option>

  ';

  $i = 73;

  }

  elseif($i == 68 || $i == 69 )

  {

  echo'

  <option value="68-069">Episode 68-69</option>

  ';

  $i = 68;

  }

  elseif($i == 52 || $i == 53 )

  {

  echo'

  <option value="52-053">Episode 52-53</option>

  ';

  $i = 52;

  }

  else

  {

 echo'

 <option value="'.$i.'">Episode '.$i.'</option>

 ';

  }

 }

 echo'

 </select>

 <input type="submit" value="Go" class="form" />

 </form>

</td>

</tr>

<tr>

 <td colspan="3">
'.$omni.'
  <hr />

 </td>

</tr>

</table>

<center>

<table align="center">

<tr>

';

for($i = 0; $i <= $count; $i++)

{

 if($i < 10)

 {$i = '0'.$i;}

  echo '

  <td>

  <table border="0" class="artg"><tr><td align="center">

   <a href="'.$dir.'e'.$epi.' - '.$i.'.jpg"><img src="'.$dir.'../thumbs/e'.$epi.' - '.$i.'.jpg" width="140" /></>

  </td></tr></table>

  </td>

  ';

  if($i % 3 == 0)

  { echo '</tr><tr>'; }

} 



echo'

</tr>

</table>

</center>';

}

elseif($epi == 'mon')

{



$dir = "member/images/screencaps/movie/";

$count = count(glob($dir . "*")); 







echo'



<table width="100%">

<tr>

<td align="left"><h3>Bleach: Memories of Nobody Gallery.</h3><td>

<td align="right">

 <form action="?page=member/episodegallery" method="post">

 <select name="episode" class="form">

 <option value="mon">Memories of Nobody</option>

 <option value="mon">Memories of Nobody</option>

 ';

 for($i = $episodes; $i != 0; $i--)

 {



  echo'

  <option value="'.$i.'">Episode '.$i.'</option>

  ';

 }

 echo'

 </select>

 <input type="submit" value="Go" class="form" />

 </form>

</td>

</tr>

<tr>

 <td colspan="3">

  <hr />

 </td>

</tr>

</table>

<center>

<table align="center">

<tr>

';

for($i = 0; $i <= $count; $i++)

{

 if($i < 10)

 {$i = '0'.$i;}

  echo '

  <td>

  <table border="0" class="artg"><tr><td align="center">

   <a href="'.$dir.'e'.$epi.' - '.$i.'.jpg"><img src="'.$dir.'../thumbs/e'.$epi.' - '.$i.'.jpg" width="140" /></>

  </td></tr></table>

  </td>

  ';

  if($i % 3 == 0)

  { echo '</tr><tr>'; }

} 



echo'

</tr>

</table>

</center>';

}



?>