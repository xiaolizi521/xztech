<?php 
$file_title = "Manga Reader";
?>
<br />

<span class="VerdanaSize1Main"><b>Bleach7 &gt; Multimedia &gt; Online Manga Viewer</b><br />

<br />
<br />
<?PHP

if(isset( $_GET['mangaka'] ))
{
	$mangaka = mysql_real_escape_string ( $_GET['mangaka'] );

	$search = mysql_query ( "SELECT series,directory,complete,latest,updated FROM `toshokan` WHERE `mangaka` LIKE '%$mangaka%' AND hidden < 1 ORDER BY series ASC" );
	$matches = mysql_num_rows ( $search );
	if ( $matches > 0 )
	{
		echo '<span class="VerdanaSize2Main"><b>Search Results</b></span><br /><br /><i>-Found '.$matches.' series by the mangaka '.$mangaka.'.</i><br />';
		echo '<br /><table width="100%"><tr bgcolor="#eeeeee"><td align="left"><font size="2">Series Name</font></td><td><font size="2">Latest</font></td><td align="right"><font size="2">Status</font></td></tr>';
		$i = 1;
		while ( $results = mysql_fetch_array($search) ) {
			$color = ( $i % 2 == 1 ) ? "" : " bgcolor=\"#eeeeee\"";
			echo '<tr'.$color.'><td align="left"><a href="http://www.bleach7.com/?page=seriesview&amp;manga='.$results['directory'].'">
			<font size="2">'.$results['series'].'</font></a>';
			$timenow = time();
			if (($timenow - $result['updated']) < 259200)
			{ echo ' - <i><small><font color="#FF0000"><b>Updated!</b></font></small></i>'; }
			echo '
			</td><td><font size="2"><a href="http://www.toshokan.bleach7.com/'.$results['directory'].'/'.$results['latest'].'">Chapter '.$results['latest'].'</a>
			</font></td>
			<td align="right">';
			if ($results[complete] != 0)
			{ echo '<font color="darkgray" size="2">Completed</font>'; }
			else
			{ echo '<font color="gray" size="2"><em>Ongoing</em></font>'; }
			echo '</td></tr>';
			$i++;
		}
		echo '</table>';
	}

	else
	{ echo 'We don\'t have any series by this mangaka. <a href="javascript: history.go(-1)">Click here to go back.</a>'; }
}
else if(isset( $_GET['genre'] ))
{
	$genre = mysql_real_escape_string ( $_GET['genre'] );

	$search = mysql_query ( 'SELECT series,directory,complete,latest,updated FROM `toshokan` WHERE `genre` LIKE \'%' . $genre . '%\' AND hidden < 1 ORDER BY series ASC' );
	$matches = mysql_num_rows ( $search );
	if ( $matches > 0 )
	{
		echo '<span class="VerdanaSize2Main"><b>Search Results</b></span><br /><br /><i>Found '.$matches.' series in the genre '.strtolower($genre).'.</i><br /><br />';
		echo '<br /><table width="100%"><tr bgcolor=#eeeeee><td align="left"><font size="2">Series Name</font></td><td><font size="2">Latest</font></td><td align="right"><font size="2">Status</font></td></tr>';
		$i = 1;
		while ( $results = mysql_fetch_array($search) ) {
			$color = ( $i % 2 == 1 ) ? "" : "#eeeeee";
			echo '<tr bgcolor='.$color.'><td align="left"><a href="http://www.bleach7.com/?page=seriesview&amp;manga='.$results['directory'].'">
			<font size="2">'.$results['series'].'</a></font>';
			$timenow = time();
			if (($timenow - $result['updated']) < 259200)
			{ echo ' - <i><small><font color="#FF0000"><b>Updated!</b></font></small></i>'; }
			echo '
			</td><td><font size="2"><a href="http://www.toshokan.bleach7.com/'.$results['directory'].'/'.$results['latest'].'">Chapter '.$results['latest'].'</a>
			</font></td>
			<td align="right">';
			if ($results[complete] != 0)
			{ echo '<font color="darkgray" size="2">Completed</font>'; }
			else
			{ echo '<font color="gray" size="2"><em>Ongoing</em></font>'; }
			echo '</td></tr>';
			$i++;
		}
		echo '</table>';

	}
	else
	{ echo 'We don\'t have any series of this genre. <a href="javascript: history.go(-1)">Click here to go back.</a>'; }
}
else {
	echo 'Oops! No search parameter was specified. <a href="javascript: history.go(-1)">Click here to go back.</a>';
}


?>
<br /><br /><br />

<span class="VerdanaSize1Main">Are we missing a chapter? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
