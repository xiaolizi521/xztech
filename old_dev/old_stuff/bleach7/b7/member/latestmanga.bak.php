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

$numbers = mysql_query ( "SELECT series,jname,mangaka,directory,genre,complete,latest,updated FROM toshokan WHERE hidden < 1 ORDER BY updated DESC LIMIT 20" );

if(!isset( $_GET['info'] )) {
		echo '<b>Sort by</b>: [<a href="?page=manga">Common title</a>] | [<a href="?page=manga&sort=english">English title</a>] | [<a href="?page=manga&sort=roman">Romanized title</a>] | [<a href="?page=manga&sort=popularity">Popularity</a>]';
		echo '<br /><b>Show</b>: [<b>Less Info</b>] | [<a href="?page=latestmanga&info=more">More Info</a>] | [<b>Recently Updated</b>]';
}
else {
		echo '<b>Sort by</b>: [<a href="?page=manga&info=more">Common title</a>] | [<a href="?page=manga&sort=english&info=more">English title</a>] | [<a href="?page=manga&sort=roman&info=more">Romanized title</a>] | [<a href="?page=manga&sort=popularity&info=more">Popularity</a>]';
		echo '<br /><b>Show</b>: [<a href="?page=latestmanga">Less Info</a>] | [<b>More Info</b>] | [<b>Recently Updated</b>]';
}


echo '<br /><br />';

$numseries = mysql_num_rows($numbers);

if(!isset( $_GET['info'] )) {
	echo '<br /><table width="100%"><tr bgcolor=#eeeeee><td align="left"><font size="2">Series Name</font></td>
	<td><font size="2">Latest</font></td><td align="right"><font size="2">Added</font></td></tr>
	';

	$i = 1;

	$popquery = mysql_query ( "SELECT `series` FROM toshokan ORDER BY `views` DESC LIMIT 5" ) or die(mysql_error());
	while ( $popseries = mysql_fetch_array($popquery) ) {
		$popular .= $popseries['series'].', ';
	}

	while ( $nexseries = mysql_fetch_array($numbers) ) {
	
		$printname = $nexseries['series'];
	
		$color = ( $i % 2 == 1 ) ? "" : "#eeeeee";
		echo '<tr bgcolor='.$color.'><td align="left"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/">
		<font size="2">';
		if(ereg($nexseries['series'], $popular))
		{ echo '<b>'; }
		echo $printname;
		if(ereg($nexseries['series'], $popular))
		{ echo '</b>'; }
		echo '</a></font>';
		$timenow = time();
		echo '
		</td><td><font size="2"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/'.$nexseries['latest'].'">Chapter '.$nexseries['latest'].'</a>		</font></td>
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
		{ echo '<font size="2">'.$daysago.' Day Ago</font>'; }
		}
		echo '</td></tr>';
		$i++;
	}
	echo '</table><br />*<b>Bold</b>: Popular series';
}

else {
	echo '<br />';
	while ( $nexseries = mysql_fetch_array($numbers) ) {
	
		$printname = $nexseries['series'];

		echo '
		<span class="VerdanaSize2Main"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/">
		<b>'.$printname.'</b></a></span>
		<span class="VerdanaSize1Main">
		<br />
		<b>Japanese Title:</b> '.$nexseries['jname'].'<br />
		<b>Mangaka:</b> '.$nexseries['mangaka'].'<br />
		';
		echo '<b>Latest:</b>: <a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/'.$nexseries['latest'].'">Chapter '.$nexseries['latest'].'</a><br />';
		$timenow = time();
		$sinceupdate = $timenow - $nexseries['updated'];
		if ($sinceupdate < 3600) {
			$minago = intval($sinceupdate/60);
			echo '<b>Added:</b> '.$minago.' Minutes Ago</font>';
		}
		else if ($sinceupdate < 86400) {
			$hourago = intval($sinceupdate/3600);
			if($hourago > 1)
			{ echo '<b>Added:</b> '.$hourago.' Hours Ago</font>'; }
			else
			{ echo '<b>Added:</b> '.$hourago.' Hour Ago</font>'; }
		}
		else {
		$daysago = intval($sinceupdate/86400);
		if($daysago > 1)
		{ echo '<b>Added:</b> '.$daysago.' Days Ago</font>'; }
		else
		{ echo '<b>Added:</b> '.$daysago.' Day Ago</font>'; }
		}
		echo '<br /><b>Genre:</b> '.$nexseries['genre'].'<br /><br />';
		echo '</span>';
	}
}
?>
<br /><br /><br />

</span><span class="VerdanaSize1Main">Are we missing a chapter? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
