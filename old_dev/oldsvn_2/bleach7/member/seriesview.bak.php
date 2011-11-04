<?php 
$file_title = "Manga Reader";
?>
<br />

<span class="VerdanaSize1Main"><b>Bleach7 &gt; Multimedia &gt; Online Manga Viewer</b><br />

<br />
<br />
<?PHP

if(isset( $_GET['manga'] ))
{
	$manga = mysql_real_escape_string ( $_GET['manga'] );
	$result = mysql_query ( 'SELECT * FROM `toshokan` WHERE `directory`=\'' . $manga . '\'' );

	if ( mysql_num_rows ( $result ) == 1 )
	{
		$mangainfo = mysql_fetch_array ( $result );
		echo '
		<span class="VerdanaSize2Main"><b>'.$mangainfo['series'].'</b></span><br />
		<b>Japanese Title:</b> '.$mangainfo['jname'].'<br />
		<b>English Title:</b> '.$mangainfo['ename'].'<br />
		<b>Mangaka:</b> '.$mangainfo['mangaka'].'<br />
		';
		if ($mangainfo['complete'] != 0)
		{ echo '<b>Status:</b> Completed<br />'; }
		else
		{ echo '<b>Status:</b> Ongoing<br />'; }
		echo '
		<b>Genre:</b> '.$mangainfo['genre'].'<br /><br />
		<b>Summary:</b> '.$mangainfo['summary'].'<br />
		';

		echo '<br /><br /><fieldset><legend><b>Chapter Menu</b></legend><br /><center><form action=""><select onchange="window.open(this.options[this.selectedIndex].value,\'_top\')" name="ch"><option disabled="disabled" selected="selected">Select Chapter</option>';

		if ($manga != 'bleach')
		{ $path = dirname(__FILE__).'/toshokan/'.$mangainfo['directory'].'/src/'; }
		else
		{ $path = dirname(__FILE__).'/reader/Bleach/'; }
		$dir = opendir($path);
		while($file = readdir($dir)){
			if(is_dir($path.'/'.$file) && $file != '..' && $file != '.')
			{
				if($file < 0)
				{$flash_folder[] = $file;}
				else
				{$folder[] = $file;}
			}
		}
		sort($folder);
		sort($flash_folder);
		
		$folder_count = count($folder);
		for($i = 0; $i < $folder_count; $i++)
		{
			echo '<option value="http://www.toshokan.bleach7.com/'.$mangainfo['directory'].'/'.$folder[$i].'">Chapter '.$folder[$i];
			if($folder[$i] >= 79 && $folder[$i] < 89 && $mangainfo['directory'] == 'to-love-ru')
			{ echo ' - Uncensored'; }
			echo '</option>';
		}
		$folder_count = count($flash_folder);

		if($folder_count > 0)
		{
			echo '<option disabled="disabled">Flashback Chapters</option>';
			for($i = 0; $i < $folder_count; $i++)
			{
				$color = ( $i % 2 == 1 ) ? "#eeeeee" : "";
				echo '<option value="http://www.toshokan.bleach7.com/'.$mangainfo['directory'].'/'.$flash_folder[$i].'"><font size="2">Chapter '.$flash_folder[$i].'</option>';
			}
		}
   		if ($mangainfo['complete'] == 1)
   		{ echo'<option disabled="disabled">[Series End]</option>'; }
		echo '</select></form></center><br /></fieldset>';
	}
	else
	{ echo 'The series specified could not be found. <a href="javascript: history.go(-1)">Click here to go back.</a>'; }
}
else {
	echo 'Oops! No manga series was specified. <a href="javascript: history.go(-1)">Click here to go back.</a>';
}


?>
<br /><br /><br />

</span><span class="VerdanaSize1Main">Are we missing a chapter? Have a suggestion for a series you want added? <a href="mailto:webmaster@bleach7.com"><b>EMAIL US</b></a>!</span>
