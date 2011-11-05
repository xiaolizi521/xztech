<br />
<span class="VerdanaSize1Main"><b>Bleach 7 &gt; Edit XML Files </b><br />
<br />
</span><span class="VerdanaSize2Main"><b>Edit any of the site's XML files from here</b></span><span class="VerdanaSize1Main"><br />
<br />
<script type="text/javascript">
	var click_count = 0;
	function ClickTracker() {
	    click_count++;
	    if ( click_count == 1 ) {
	        document.xmledit.submit();
	    }
	    if ( click_count >= 2 ) {
	        alert ( "Please do not try to submit the form more than once" );
	        return false;
	    }
	}
</script>
<form action="?page=xmledit" name="xmledit" method="post" class="form" >
<?php
if (  $_POST['edit_index_info'] ) {
	$xml_xml = stripslashes ( $_POST['xml'] );
	$xml_section = stripslashes ( $_POST['section'] );
	$index_anime_raw = mysql_real_escape_string ( $_POST['anime_raw'] );
	$index_anime_sub = mysql_real_escape_string ( $_POST['anime_sub'] );
	$index_manga_raw = mysql_real_escape_string ( $_POST['manga_raw'] );
	$index_manga_sub = mysql_real_escape_string ( $_POST['manga_sub'] );
	$update_message = mysql_query ( 'UPDATE `index_info` SET `anime_raw` = \'' . $index_anime_raw . '\', `anime_sub` = \'' . $index_anime_sub . '\', `manga_raw`=\'' . $index_manga_raw . '\', `manga_sub` = \'' . $index_manga_sub . '\'' );
	header ( 'Location: ' . $site_url . '?' . $ident . '=xmledit&sec=' . $xml_section . '&xml=' . $xml_xml );
}

else if ( $_POST['submit']  ) {

	$xml_write = stripslashes ( $_POST['xml_textarea'] );
	$xml_xml = stripslashes ( $_POST['xml'] );
	$xml_section = stripslashes ( $_POST['section'] );

	//file we are going to edit
	if ( $xml_section != 'xml' ) {
		$file = './' . $xml_section . '/xml/' . $xml_xml . '.xml';
	}
	else {
		$file = './xml/' . $xml_xml . '.xml';
	}
	if ( is_writable ( $file ) ) {
		// can we open the file in write mode
		if ( !$handle_file = fopen($file, 'w')) {
			echo 'Cannot open file (', $file, ')';
			//kill the script
			exit;
		}

		// file is open so lets write to it.
		if ( fwrite ( $handle_file, $xml_write ) === FALSE) {
			//cant seem to write to the file display decent error
			echo 'Cannot write to file (', $file, ')';
			exit;
		}

		fclose ( $handle_file );
	} 
	else {
		// unable to write to the file maybe due to access? chmod it 777
		echo 'The file ', $filename, ' is not writable';
	}
	header ( 'Location: ' . $site_url . '?' . $ident . '=xmledit&sec=' . $xml_section . '&xml=' . $xml_xml );
}

// makes sure that the section is of the xml is given
if ( isset ( $_GET['sec'] ) && !empty ( $_GET['sec'] ) ) {
	$section = strtolower ( $_GET['sec'] );
	$section_b = true;
}
else {
	echo 'Must contain the section where the xml file is located.<br />
';
}

// makes sure that the filename of the xml is given
if ( isset ( $_GET['xml'] ) && !empty ( $_GET['xml'] ) ) {
	$xml = strtolower ( $_GET['xml'] );
	$xml_b = true;
}
else {
	echo 'Must contain the filename of the xml file.<br />
';
}

// Makes sure the user is registerd
if ( !isset ( $user_B7 ) ) {
	echo 'You are not authorized to view this page</span>
';
}
else if ( $user_B7->getEdit_Info() == true || $user_B7->getEdit_Latest() == true ) {
	// if both the section is given and the filename is given, then onto the main section of the page
	if ( $section_b === true && $xml_b === true ) {
		if ( $section != 'xml' ) {
			$xml_file = './' . $section . '/xml/' . $xml . '.xml';
		}
		else {
			$xml_file = './xml/' . $xml . '.xml';
		}

		$xml_content = htmlentities ( file_get_contents ( $xml_file ) );
		
		//Show info on the different tags
		echo '</span><p class="VerdanaSize1Main" style="text-align: center;">Use this information to know what each tag means</p>
<span class="VerdanaSize1Main">';
		switch ( $section ) {
			case 'information':
				switch ( $xml ) {
					case 'episode':
						echo '<b>episode</b> - This just groups all the information for each episode.  The id is the episode number that corresponds to that episode.<br />
<b>jptitle</b> - This is the Japanese translated title for the episode.<br />
<b>romanji</b> - This is the romanized veriation of the Japanese Kanji of the title.<br />
<b>kanji</b> - This is the original Japanese character for the title. You only have to input the characters.  You do not need to put in the brackets that surround the characters (ie. &#12300; &#12301;).<br />
<b>viztitle</b> - This is the title that Viz has officially given for the English dub episode.<br />
<b>jpdate</b> - The date that TV Tokyo showed the episode.<br />
<b>ytvdate</b> - The date that Canada\'s YTV showed the English dub of the show.<br />
<b>asdate</b> - The date that Adult Swim showed the English dub of the show.<br />
';
						$xml_view = '?page=information/bleachanimeguide';
						break;
					case 'jpchaptertitles':
					case 'vizchaptertitles':
						echo '<b>volume</b> - The grouping of each volume.<br />
<b>vid</b> - The volume number.<br />
<b>vname</b> - The title of the volume. Use &quot;&amp;#xa0;&quot; if the name is unknown.<br />
<b>chapter</b> - Title on the chapter<br />
<b>cid</b> - The chapter number.<br />
';
						if  ( $xml == 'jpchaptertitles' ) {
							$xml_view = '?page=information/chaptertitles&amp;xml=jp';
						}
						else {
							$xml_view = '?page=information/chaptertitles&amp;xml=viz';
						}
						break;
				}
				break;
			case 'media':
				switch ( $xml ) {
					case 'manga1-5':
					case 'manga6-10':
					case 'manga6-10m7':
					case 'manga11-15':
					case 'manga11-15m7':
					case 'manga16-20':
					case 'manga16-20m7':
					case 'manga21-25':
					case 'manga21-25m7':
					case 'manga26-30':
					case 'manga26-30m7':
					case 'raw1-5':
					case 'raw6-10':
					case 'raw11-15':
					case 'raw16-20':
					case 'raw21-25':
					case 'raw26-30':
						echo '<b>vfirst</b> - The first volume on the page.<br />
<b>vlast</b> - The last volume on the page.<br />
<b>cfirst</b> - The first chapter on the page.<br />
<b>clast</b> - The last chapter on the page.<br />
<b>volume</b> - The grouping of each volume.<br />
<b>vid</b> - The volume number.<br />
<b>vname</b> - The title of the volume. Use &quot;&amp;#xa0;&quot; if the name is unknown.<br />
<b>image</b> - Information on the image. The text in-between is the altername name for the image.<br />
<b>rowspan</b> - Number or rows the image merges with.<br />
<b>expand</b> - The filename for the link to the expanded image. Use &quot;nopic.jpg&quot; if there is no image.<br />
<b>thumb</b> - The filename for the thumbnail image for the volume image. Use &quot;nopic_t.jpg&quot; if there is no image.<br />
<b>vdownload</b> - Information for the batch file for the volume. If filename attribute is not given, you can leave the filename within the chapter tags.<br />
<b>filename</b> - The name of the file to be downloaded.<br />
<b>chapter</b> - Information on the chapter download being downloaded. If filename attribute is not given, you can leave the filename within the chapter tags.<br />
<b>cid</b> - The chapter number being downloaded.<br />
';
						$xml_view = '?page=media/mangarelease&amp;xml=' . $xml;
						break;
					case 'mangalatest':
					case 'rawlatest':
						echo '<b>lastfive</b> - The last five chapter released.<br />
<b>previous</b> - All other chapter to be placed in the latest release section.<br />
<b>chapter</b> - Information on the chapter download being downloaded.  If filename attribute is not given, you can leave the filename within the chapter tags.<br />
<b>cid</b> - The chapter number being downloaded.<br />
<b>filename</b> - The name of the file to be downloaded.<br />
';
						$result_index_release = mysql_query( 'SELECT `anime_raw`, `anime_sub`, `manga_raw`, `manga_sub` FROM `index_info`' );
						$index_release = mysql_fetch_array( $result_index_release );
						if ( $xml == 'mangalatest' ) {
							$xml_view = '?page=media/latest&amp;xml=manga';
						}
						else {
							$xml_view = '?page=media/latest&amp;xml=raw';
						}
						break;
					case 'musicalbum':
					case 'musicbbc':
					case 'musicdjcd':
					case 'musicoped':
					case 'musicost':
						$xml_view = '?page=media/musicrelease&amp;xml=' . substr ( $xml, 5 );
						break;
				}
				break;
		}
		echo '<br />
<a href="',  $xml_file, '" target="_blank">View the xml file separately.</a><br />
<a href="', $xml_view,'">View the original page that the xml file is used for</a><br />
<br />';
		if ( mysql_num_rows ( $result_index_release ) > 0 ) { // Valid SQL query
?>
<hr />
<p style="width: 100%; text-align: center;">Edit the Index Information for the Manga and Anime Numbers</p>
<table cellpadding="0" cellspacing="0" class="VerdanaSize1Main">
	<tr>
		<td>Anime Raw Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type="text" name="anime_raw" style="width: 35px;" value="<?php echo stripslashes ( $index_release['anime_raw'] ) ?>" class="form" /></td>
	</tr>
	<tr>
		<td>Anime Sub Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type="text" name="anime_sub" style="width: 35px;" value="<?php echo stripslashes ( $index_release['anime_sub'] ) ?>" class="form" /></td>
	</tr>
	<tr>
		<td>Manga Raw Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type="text" name="manga_raw" style="width: 35px;" value="<?php echo stripslashes ( $index_release['manga_raw'] ) ?>" class="form" /></td>
	</tr>
	<tr>
		<td>Manga Sub Number</td>
		<td>&nbsp;&nbsp;&nbsp;<input type="text" name="manga_sub" style="width: 35px;" value="<?php echo stripslashes ( $index_release['manga_sub'] ) ?>" class="form" /></td>
	</tr>
</table>
<br />
<p style="width: 100%; text-align: center;">
	<input type="submit" name="edit_index_info" value="Edit Index Info" class="form" />
	<input type="button" value="Reset Fields" class="form" onclick="xmledit.reset()" />
</p>
<hr />
<br />
<?php
		}
?>

	<textarea name="xml_textarea" style="width: 98%; height: 1300px;"><?php echo $xml_content; ?>
	</textarea>
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="width: 500px; text-align:center;"><input type="submit" name="submit" value="Edit xml" onclick="ClickTracker()" /></td>
		</tr>
	</table>
	<input type="hidden" value="<?php echo $section; ?>" name="section" />
	<input type="hidden" value="<?php echo $xml; ?>" name="xml" />
</form>
<?php
	}
	
}
// Makes sure the user is either a Info member, M7 member, Staff Member or Admin Member
else {
	echo 'You are not authorized to view this page</span>
';
}
?>
</span>
