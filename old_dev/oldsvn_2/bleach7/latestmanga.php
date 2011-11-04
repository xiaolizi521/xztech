<?php 
$file_title = "Manga Reader";
?>
<br />

<span class="VerdanaSize1Main"><b>Bleach7 &gt; Multimedia &gt; Online Manga</b><br />

<br />

</span><span class="VerdanaSize2Main"><b>Online Manga Reader</b><br /></span>
<span class="VerdanaSize1Main"><i>&nbsp;- Read manga from the comfort of your browser without having to mess with .zips, .rars, and<br />&nbsp;&nbsp; other craziness!</i></span>

<br /><br />
<?PHP


$numbers = mysql_query ( "SELECT series,directory,complete,latest,updated FROM toshokan WHERE hidden < 1 ORDER BY updated DESC LIMIT 50" );

echo '<b>Sort by</b>: [<a href="?page=manga">Common title</a>] | [<a href="?page=manga&amp;sort=english">English title</a>] | [<a href="?page=manga&amp;sort=roman">Romanized title</a>] | [<a href="?page=manga&amp;sort=popularity">Popularity</a>]';
echo '<br /><b>Show</b>: [<b>Recently Updated</b>]';



echo '<br /><br />';


echo '<br /><table width="100%"><tr bgcolor="#eeeeee"><td align="left"><font size="2">Series Name</font></td>
<td><font size="2">Latest</font></td><td align="right"><font size="2">Added</font></td></tr>
';

$i = 1;

$popquery = mysql_query ( "SELECT `series` FROM toshokan ORDER BY `views` DESC LIMIT 10" ) or die(mysql_error());
while ( $popseries = mysql_fetch_array($popquery) ) {
	$popular .= $popseries['series'].', ';
}

while ( $nexseries = mysql_fetch_array($numbers) ) {

	$printname = $nexseries['series'];

	$color = ( $i % 2 == 1 ) ? "" : " bgcolor=\"#eeeeee\"";
	echo '<tr'.$color.'><td align="left"><a href="http://www.bleach7.com/?page=seriesview&amp;manga='.$nexseries['directory'].'">
	<font size="2">';
	if(ereg($nexseries['series'], $popular))
	{ echo '<b>'; }
	echo $printname;
	if(ereg($nexseries['series'], $popular))
	{ echo '</b>'; }
	echo '</font></a>';
	$timenow = time();
	echo '
	</td><td><font size="2"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/'.$nexseries['latest'].'">Chapter '.$nexseries['latest'].'</a></font></td>
	<td align="right">';
	$timenow = time();
	$sinceupdate = $timenow - $nexseries['updated'];
	if ($sinceupdate < 3600) {
		$minago = intval($sinceupdate/60);
		echo '<font size="2">'.$minago.' Minutes Ago</font>';
	}
	else if ($sinceupdate < 86400) {
		$hourago = intval($sinceupdate/3600);
		if($hourago > 1)
		{ echo '<font size="2">'.$hourago.' Hours Ago</font>'; }
		else
		{ echo '<font size="2">'.$hourago.' Hour Ago</font>'; }
	}
	else {
	$daysago = intval($sinceupdate/86400);
	if($daysago > 1)
	{ echo '<font size="2">'.$daysago.' Days Ago</font>'; }
	else
	{ echo '<font size="2">Yesterday</font>'; }
	}
	echo '</td></tr>';
	$i++;
}
echo '</table><br />*<b>Bold</b>: Popular series';

//readfile('http://www.toshokan.bleach7.com/latestmanga.php');
?>
<br /><br /><br />

<span class="VerdanaSize1Main">Are we missing a chapter? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
