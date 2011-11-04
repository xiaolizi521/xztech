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

if(isset( $_GET['sort'] ))
{
	if($_GET['sort'] == 'roman') {
		$numbers = mysql_query ( "SELECT series,rname,jname,mangaka,directory,genre,complete,latest,updated FROM toshokan WHERE hidden < 1 ORDER BY rname ASC" );
 	}
	elseif($_GET['sort'] == 'popularity') {
		$numbers = mysql_query ( "SELECT series,jname,mangaka,directory,genre,complete,latest,updated FROM toshokan WHERE hidden < 1 ORDER BY views DESC" );
	}
	elseif($_GET['sort'] == 'english') {
		$numbers = mysql_query ( "SELECT series,ename,jname,mangaka,directory,genre,complete,latest,updated FROM toshokan WHERE hidden < 1 ORDER BY ename ASC" );

	}
	if(!isset( $_GET['info'] )) {
		if($_GET['sort'] == 'roman') {
			echo '<b>Sort by</b>: [<a href="?page=manga">Common title</a>] | [<a href="?page=manga&sort=english">English title</a>] | [<b>Romanized title</b>] | [<a href="?page=manga&sort=popularity">Popularity</a>]';
			echo '<br /><b>Show</b>: [<b>Less Info</b>] | [<a href="?page=manga&sort='.$_GET['sort'].'&info=more">More Info</a>] | [<a href="?page=latestmanga">Recently Updated</a>]';
		}
		elseif($_GET['sort'] == 'popularity') {
				echo '<b>Sort by</b>: [<a href="?page=manga">Common title</a>] | [<a href="?page=manga&sort=english">English title</a>] | [<a href="?page=manga&sort=roman">Romanized title</a>] | [<b>Popularity</b>]';
			echo '<br /><b>Show</b>: [<b>Less Info</b>] | [<a href="?page=manga&sort='.$_GET['sort'].'&info=more">More Info</a>] | [<a href="?page=latestmanga">Recently Updated</a>]';
		}
		elseif($_GET['sort'] == 'english') {
			echo '<b>Sort by</b>: [<a href="?page=manga">Common title</a>] | [<b>English title</b>] | [<a href="?page=manga&sort=roman">Romanized title</a>] | [<a href="?page=manga&sort=popularity">Popularity</a>]';
			echo '<br /><b>Show</b>: [<b>Less Info</b>] | [<a href="?page=manga&sort='.$_GET['sort'].'&info=more">More Info</a>] | [<a href="?page=latestmanga">Recently Updated</a>]';
		}
	}
	else {
		if($_GET['sort'] == 'roman') {
			echo '<b>Sort by</b>: [<a href="?page=manga&info=more">Common title</a>] | [<a href="?page=manga&sort=english&info=more">English title</a>] | [<b>Romanized title</b>] | [<a href="?page=manga&sort=popularity&info=more">Popularity</a>]';
			echo '<br /><b>Show</b>: [<a href="?page=manga&sort='.$_GET['sort'].'">Less Info</a>] | [<b>More Info</b>] | [<a href="?page=latestmanga&info=more">Recently Updated</a>]';
		}
		elseif($_GET['sort'] == 'popularity') {
				echo '<b>Sort by</b>: [<a href="?page=manga&info=more">Common title</a>] | [<a href="?page=manga&sort=english&info=more">English title</a>] | [<a href="?page=manga&sort=roman&info=more">Romanized title</a>] | [<b>Popularity</b>]';
			echo '<br /><b>Show</b>: [<a href="?page=manga&sort='.$_GET['sort'].'">Less Info</a>] | [<b>More Info</b>] | [<a href="?page=latestmanga&info=more">Recently Updated</a>]';
		}
		elseif($_GET['sort'] == 'english') {
			echo '<b>Sort by</b>: [<a href="?page=manga&info=more">Common title</a>] | [<b>English title</b>] | [<a href="?page=manga&sort=roman&info=more">Romanized title</a>] | [<a href="?page=manga&sort=popularity&info=more">Popularity</a>]';
			echo '<br /><b>Show</b>: [<a href="?page=manga&sort='.$_GET['sort'].'">Less Info</a>] | [<b>More Info</b>] | [<a href="?page=latestmanga&info=more">Recently Updated</a>]';
		}
	}
}
else {
	$numbers = mysql_query ( "SELECT * FROM toshokan WHERE hidden < 1 ORDER BY series ASC" ) or die(mysql_errors());
	if(!isset( $_GET['info'] )) {
		echo '<b>Sort by</b>: [<b>Common title</b>] | [<a href="?page=manga&sort=english">English title</a>] | [<a href="?page=manga&sort=roman">Romanized title</a>] | [<a href="?page=manga&sort=popularity">Popularity</a>]';
		echo '<br /><b>Show</b>: [<b>Less Info</b>] | [<a href="?page=manga&info=more">More Info</a>] | [<a href="?page=latestmanga">Recently Updated</a>]';
	}
	else {
		echo '<b>Sort by</b>: [<b>Common title</b>] | [<a href="?page=manga&sort=english&info=more">English title</a>] | [<a href="?page=manga&sort=roman&info=more">Romanized title</a>] | [<a href="?page=manga&sort=popularity&info=more">Popularity</a>]';
		echo '<br /><b>Show</b>: [<a href="?page=manga">Less Info</a>] | [<b>More Info</b>] | [<a href="?page=latestmanga&info=more">Recently Updated</a>]';
	}
}




echo '<br /><br />';

$numseries = mysql_num_rows($numbers);

if(!isset( $_GET['info'] )) {
	echo '<br /><table width="100%"><tr bgcolor=#eeeeee><td align="left"><font size="2">Series Name</font></td>
	<td><font size="2">Latest</font></td><td align="right"><font size="2">Status</font></td></tr>
	';

	$i = 1;

	$popquery = mysql_query ( "SELECT `series` FROM toshokan ORDER BY `views` DESC LIMIT 5" ) or die(mysql_error());
	while ( $popseries = mysql_fetch_array($popquery) ) {
		$popular .= $popseries['series'].', ';
	}

	while ( $nexseries = mysql_fetch_array($numbers) ) {
	
		if(isset( $_GET['sort'] ))
		{
 			if($_GET['sort'] == 'roman')
			{ $printname = $nexseries['rname']; }
			elseif($_GET['sort'] == 'popularity')
			{ $printname = $nexseries['series']; }
			elseif($_GET['sort'] == 'english')
			{ $printname = $nexseries['ename']; }
		}
		else
		{ $printname = $nexseries['series']; }
	
		$color = ( $i % 2 == 1 ) ? "" : "#eeeeee";
		echo '<tr bgcolor='.$color.'><td align="left"><a href="http://www.bleach7.com/?page=seriesview&amp;manga='.$nexseries['directory'].'">
		<font size="2">';
		if(ereg($nexseries['series'], $popular))
		{ echo '<b>'; }
		echo $printname;
		if(ereg($nexseries['series'], $popular))
		{ echo '</b>'; }
		echo '</a></font>';
		$timenow = time();
		if (($timenow - $nexseries['updated']) < 259200)
		{ echo ' - <i><small><font color="#FF0000"><b>Updated!</b></font></small></i>'; }
		echo '
		</td><td><font size="2"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/'.$nexseries['latest'].'">Chapter '.$nexseries['latest'].'</a>
		</font></td>
		<td align="right">';
		if ($nexseries[complete] != 0)
		{ echo '<font color="darkgray" size="2">Completed</font>'; }
		else
		{ echo '<font color="gray" size="2"><em>Ongoing</em></font>'; }
		echo '</td></tr>';
		$i++;
	}
	echo '</table><br />*<b>Bold</b>: Popular series';
}

else {
	echo '<br />';
	while ( $nexseries = mysql_fetch_array($numbers) ) {
	
		if(isset( $_GET['sort'] ))
		{
 			if($_GET['sort'] == 'roman')
			{ $printname = $nexseries['rname']; }
			elseif($_GET['sort'] == 'popularity')
			{ $printname = $nexseries['series']; }
			elseif($_GET['sort'] == 'english')
			{ $printname = $nexseries['ename']; }
		}
		else
		{ $printname = $nexseries['series']; }

		echo '
		<span class="VerdanaSize2Main"><a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/">
		<b>'.$printname.'</b></a></span>
		<span class="VerdanaSize1Main">
		<br />
		<b>Japanese Title:</b> '.$nexseries['jname'].'<br />
		<b>Mangaka:</b> '.$nexseries['mangaka'].'<br />
		';
		if ($nexseries[complete] != 0)
		{ echo '<b>Status:</b> Completed<br />'; }
		else
		{ echo '<b>Status:</b> Ongoing<br />'; }
		echo '<b>Latest:</b>: <a href="http://www.toshokan.bleach7.com/'.$nexseries['directory'].'/'.$nexseries['latest'].'">Chapter '.$nexseries['latest'].'</a><br />';
		echo '<b>Genre:</b> '.$nexseries['genre'].'<br /><br />';
		echo '</span>';
	}
}
?>
<br /><br /><br />

</span><span class="VerdanaSize1Main">Are we missing a chapter? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
