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
	$numbers = mysql_query ( "SELECT series,directory,views FROM toshokan ORDER BY series ASC" );
	echo '<b>Sort by</b>: [<a href="?page=omvviews">Views</a>] | [<b>Alphabetical</b>]';
}
else {
	$numbers = mysql_query ( "SELECT series,directory,views FROM toshokan ORDER BY views DESC" );
	echo '<b>Sort by</b>: [<b>Views</b>] | [<a href="?page=omvviews&sort=alpha">Alphabetical</a>]';
}
 

$views = 0;

$numseries = mysql_num_rows($numbers);
echo '<br /><br /><br /><table width="100%">';
$i = 1;

while ( $nexseries = mysql_fetch_array($numbers) ) {

	$color = ( $i % 2 == 1 ) ? "#eeeeee" : "";
	echo '<tr bgcolor='.$color.'><td><a href="http://www.bleach7.com/?page=seriesview&amp;manga='.$nexseries['directory'].'"><font size="2">'.$nexseries['series'].'</font></a></td><td align="left"><font size="2">Views: '.$nexseries['views'].'</font></td>';
	$i++;
	$views += $nexseries['views'];
}
echo '</table><br /><br />';
echo '<span class="VerdanaSize2Main">Total views: '.$views.'<br /><br />';
?>



<span class="VerdanaSize1Main">Find a missing chapter? Is a chapter not showing up? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
