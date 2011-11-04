<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

// Taken from abdullah dot a at gmail dot com on php.net
function set_cookie( $name, $value, $expires, $path, $domain )
{
   header('Set-Cookie: ' . rawurlencode($name) . '=' . rawurlencode($value)
                         . (empty($expires) ? '' : '; expires=' . gmdate('d-M-Y H:i:s \\G\\M\\T', $expires))
                         . (empty($path)    ? '' : '; path=' . $path)
                         . (empty($domain)  ? '' : '; domain=' . $domain), false);
}
$first ='0';
function Paginate( $identifier, $pages_num, $query_string ) {
	global $site_path;
	if(empty($_GET[$identifier]))
	{ $pg=1; }
	else
	{$pg = $_GET[$identifier];}
	$pg_limit = 5;
	$pg_prev_pages = ($pg - floor ( $pg_limit/2 ) );
	$pg_next_pages = ($pg + floor ( $pg_limit/2 ) );
	if ( $pg_prev_pages >= 1 ) {
		$start = $pg_prev_pages;
		$first ='1';
	}
	else {
		$start = 1;
	} 
	if ( $pg_next_pages <= $pages_num ) {
		$finish = $pg_next_pages;
		$last = '1';
	}
	else {
		$finish = $pages_num;
	}
	if ( isset ( $pg ) && !empty ( $pg ) && ( $pg >= 1 ) && ( $pg <= $pages_num ) ) {
		echo '<hr />
';
		echo '<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<tr>
		<td style="text-align: left;">
			<table style="padding: 3px;" class="main">
				<tr>
					<td>(Page ', $pg, ' of ', $pages_num, ')</td>
';
		if ( $pg > 1 ) {
			if ( 1 == 1 ) {
				echo '					<td><a href="', $site_path, '/', $query_string, '&amp;pg=1" title="First Page"><b>&laquo;</b></a></td>
';
			}
			echo '					<td><a href="', $site_path, '/', $query_string, '&amp;pg=', ( $pg - 1 ), '" title="Previous Page">&lt;</a></td>
';
		}
		for ( $x = $start; $x <= $finish; $x++ ) {
			if ( $pg == $x ) {
				echo '					<td><b>', $x, '</b></td>
';
			}
			else {
				echo '					<td><a href="', $site_path, '/', $query_string, '&amp;pg=', $x, '">', $x, '</a></td>
';
			}
		}
		if ( $pg < $pages_num ) {
			echo '					<td><a href="', $site_path, '/', $query_string, '&amp;pg=', ( $pg + 1 ), '" title="Next Page">&gt;</a></td>
';
			if ( 0 == 1 ) {
				echo '					<td><a href="', $site_path, '/', $query_string, '&amp;pg=', $pages_num, '" title="Last Page"><b>&raquo;</b></a></td>
';
			}
		}
		echo '				</tr>
			</table>
		</td>
		<td style="text-align: right;">
			<select name="page" onchange="document.location=this.value" class="form">
';
		for ( $x = 1; $x <= $pages_num; $x++ ) {
			if ( $pg == $x ) {
				echo '				<option value="', $site_path, '/', $query_string, '&amp;', $identifier, '=', $x, '" selected="selected">Page ', $x, '</option>
';
			}
			else {
				echo '				<option value="', $site_path, '/', $query_string, '&amp;', $identifier, '=', $x, '">Page ', $x, '</option>
';
			}
		}
		echo '			</select>
		</td>
	</tr>
</table>
<hr />';
	}
}
function PaginateGallery( $identifier, $pages_num, $query_string ) {
	global $site_path;
	$site_path = '?page=';
	if(empty($_GET[$identifier]))
	{ $pg=1; }
	else
	{$pg = $_GET[$identifier];}
	$pg_limit = 5;
	$pg_prev_pages = ($pg - floor ( $pg_limit/2 ) );
	$pg_next_pages = ($pg + floor ( $pg_limit/2 ) );
	if ( $pg_prev_pages >= 1 ) {
		$start = $pg_prev_pages;
		$first ='1';
	}
	else {
		$start = 1;
	} 
	if ( $pg_next_pages <= $pages_num ) {
		$finish = $pg_next_pages;
		$last = '1';
	}
	else {
		$finish = $pages_num;
	}
	if ( isset ( $pg ) && !empty ( $pg ) && ( $pg >= 1 ) && ( $pg <= $pages_num ) ) {
		echo '<hr />
';
		echo '<table cellpadding="0" cellspacing="0" style="width: 100%;">
	<tr>
		<td style="text-align: left;">
			<table style="padding: 3px;" class="main">
				<tr>
					<td>(Page ', $pg, ' of ', $pages_num, ')</td>
';
		if ( $pg > 1 ) {
			if ( 1 == 1 ) {
				echo '					<td><a href="', $site_path, '', $query_string, '&amp;pg=1" title="First Page"><b>&laquo;</b></a></td>
';
			}
			echo '					<td><a href="', $site_path, '', $query_string, '&amp;pg=', ( $pg - 1 ), '" title="Previous Page">&lt;</a></td>
';
		}
		for ( $x = $start; $x <= $finish; $x++ ) {
			if ( $pg == $x ) {
				echo '					<td><b>', $x, '</b></td>
';
			}
			else {
				echo '					<td><a href="', $site_path, '', $query_string, '&amp;pg=', $x, '">', $x, '</a></td>
';
			}
		}
		if ( $pg < $pages_num ) {
			echo '					<td><a href="', $site_path, '', $query_string, '&amp;pg=', ( $pg + 1 ), '" title="Next Page">&gt;</a></td>
';
			if ( 0 == 1 ) {
				echo '					<td><a href="', $site_path, '', $query_string, '&amp;pg=', $pages_num, '" title="Last Page"><b>&raquo;</b></a></td>
';
			}
		}
		echo '				</tr>
			</table>
		</td>
		<td style="text-align: right;">
			<select name="page" onchange="document.location=this.value" class="form">
';
		for ( $x = 1; $x <= $pages_num; $x++ ) {
			if ( $pg == $x ) {
				echo '				<option value="', $site_path, '', $query_string, '&amp;', $identifier, '=', $x, '" selected="selected">Page ', $x, '</option>
';
			}
			else {
				echo '				<option value="', $site_path, '', $query_string, '&amp;', $identifier, '=', $x, '">Page ', $x, '</option>
';
			}
		}
		echo '			</select>
		</td>
	</tr>
</table>
<hr />';
	}
}


function DisplayDate( $timestamp, $display_format, $display_format_option ) { 
	global $user_info;

	$days_passed = ( ( time() - $timestamp ) / 86400 );

	if ( $days_passed <= 1 ) {
		$display_format = "\T\o\d\a\y\ \a\\t h:i A";
	}
	else if ( $days_passed <= 2 ) {
		$display_format = "\Y\e\s\\t\e\\r\d\a\y \a\\t h:i A";
	}
	
	if ( isset ( $user_info['user_id'] ) ) {
		if ( $user_info['dst'] == 1 ) {
			$timezone = ( $user_info['timezone'] + 1 );
		}
		else if ( $user_info['dst'] == 0 ) {
			$timezone = $user_info['timezone'];
		}
	}
	else {
		$timezone = date( 'O' )/100 + 1;
	}
	$zone = 3600 * $timezone;
	$timestamp += $zone;
	$date = gmdate ( $display_format, $timestamp );
	return $date;
} 


function ParseMessage( $string ) { 
	global $site_url, $script_folder, $smilies_array;

	foreach ( $smilies_array as $var ) {
		$string = str_replace( ':' . $var, '<img src="' . $site_url . '/' . $script_folder . '/images/smilies/' . $var . '.gif" alt="' . $var . '" />', $string );
	}

	$patterns = array ( 
'`\[b\](.+?)\[/b\]`is', 
'`\[i\](.+?)\[/i\]`is', 
'`\[u\](.+?)\[/u\]`is',
'`\[email\](.+?)\[/email\]`is',
'`\[email=(.+?)\](.+?)\]`is',
'`\[url=([a-z0-9]+?://){1}(.+?)\](.+?)\[/url\]`is',
'`\[url=(.+?)\](.+?)\[/url\]`is',
'`\[url\]([a-z0-9]+?://){1}(.+?)\[/url\]`is',
'`\[url\](.+?)\[/url\]`is',
'`\[spoiler\](.+?)\[/spoiler\]`is',
'`\[quote\](.+?)\[/quote\]`is',
'`\[quote=(.+?)\](.+?)\[/quote\]`is',
'`\[color=(.+?)\](.+?)\[/color\]`is',
'`\[hl=(.+?)\](.+?)\[/hl\]`is',
'`\[img=(.+?)\]`is',
'`\[ul\](.+?)\[/ul\]`is',
'`\[li\](.+?)\[/li\]`is',
'`&gt;`is',
'`&lt;`is',
'`&quot;`is',
'`\<B\>(.+?)\</B\>`is',
'`\<I\>(.+?)\</I\>`is',
'`\<U\>(.+?)\</U\>`is',
'`\&amp;#`is'
); 

	$replacements =  array ( 
'<b>\\1</b>', 
'<i>\\1</i>', 
'<span style="text-decoration: underline;">\\1</span>', 
'<a href="mailto:\1" target="_blank">\1</a>', 
'<a href="mailto:\1" target="_blank">\2</a>', 
'<a href="\1\2" target="_blank">\\3</a>', 
'<a href="http://\\1" target="_blank">\\2</a>', 
'<a href="\1\2" target="_blank">\1\2</a>', 
'<a href="http://\\1" target="_blank">\\1</a>', 
'<div style="margin:5px 20px 20px 20px"><br />
	<div class="smallfont" style="margin-bottom:2px"><b>Spoiler:</b> <input type="button" value="Show" style="width:45px; font-size:10px; margin:0px ;padding:0px;"
			onclick="if
(this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
!= \'\') {
this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
= \'\';this.innerText = \'\'; this.value = \'Hide\'; } else {
this.parentNode.parentNode.getElementsByTagName(\'div\')[1].getElementsByTagName(\'div\')[0].style.display
= \'none\'; this.innerText = \'\'; this.value = \'Show\'; }" id="Button1" name="Button1" /></div>
	<div class="alt2" style="margin: 0px; padding: 6px; border: 1px inset;">
		<div style="display: none;">
			\1
		</div>
	</div>
</div>',
'<table cellpadding="0" cellspacing="0" style="width: 95%; text-align: left;" class="main">
	<tr>
		<td><b>Quote:</b>
			<table cellpadding="2" cellspacing="0" class="main" style="width: 100%; border: 1px solid #C3C3C3">
				<tr>
					<td style="vertical-align: top; font-style: italic;">\1</td>
				</tr>
			</table>
		</td>
	</tr>
</table>',
'
<table cellpadding="0" cellspacing="0" style="width: 95%; text-align: left;" class="main">
	<tr>
		<td><b>Quote:</b>
			<table cellpadding="2" cellspacing="0" class="main" style="width: 100%; border: 1px solid #C3C3C3">
				<tr>
					<td style="vertical-align: top; font-style:italic;">Originally posted by <b>\1</b><br />
						\2</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
',
'<span style="color: \1;">\2</span>',
'<span style="background-color: \1;">\2</span>',
'<a href="\1"><img src="\1" alt="" height="120" width="160" /></a>',
'<ul>\1</ul>',
'<li>\1</li>',
'>',
'<',
'"',
'<b>\\1</b>', 
'<i>\\1</i>', 
'<span style="text-decoration: underline;">\\1</span>',
'&#'
); 

	$previous_string = '';
	while ( $previous_string != $string ) {
		$previous_string = $string;
		$string = preg_replace( $patterns, $replacements , $string ); 
	}

	return $string; 
}


function getuserdetail( $username ) {
	$username = stripslashes ( htmlentities ( $username ) );
	$query = 'SELECT * FROM `users` WHERE `username`=\'' . mysql_real_escape_string ( $username ) . '\'';
	$result = mysql_query( $query );
	$row = mysql_fetch_array( $result );
	return $row;
}

function isbanned( $userid ) {
	$userid = stripslashes ( htmlentities ( $userid ) );
	
	// Get a list of users with that user's ip address
	$query2 = "SELECT c.* FROM comments_banned c, users u WHERE u.user_id = $userid AND c.ip = u.ip_address";
	$result2 = mysql_query($query2);
	$result2_count = mysql_num_rows($result2);
	$count = 0;
	
	// if the list found users with the same ip address
	if ( $result2_count > 0 ) {
		// go through the list
		while ( $row_main2 = mysql_fetch_array($result2) ) {
			// if the userid the same as one in the list
			// add one to count
			if ( $userid == $row_main2['id'] ) {
				$count++;
			}
		}
		// if the ip address  is found, but the user name is not part of the list
		// the the user is using an alternate account, and an ip ban needs to be placed
		if ( $count == 0 ) {
			return true;
		}
	}

	$query="SELECT *, UNIX_TIMESTAMP(banlength) As banends FROM comments_banned WHERE user_id=$userid";

	$result = mysql_query( $query );
	$row = mysql_fetch_array( $result );

	if (mysql_num_rows( $result ) > 0) {
		//Still banned
		if($row['banends'] >= time() ) { 
			return true;
		}
		else {
			//Updates that users ban status
			mysql_query('DELETE FROM `comments_banned` WHERE `id` = ' . mysql_real_escape_string ( $row['id'] ) );
			return false;
		}
	}
	else {
		return false ;
	}
}

###################
##  log_entry
###################

/*
Creates a new log entry for an event.

log_entry (<type>,<message>);
 :: type :: This is used to identify different type of log entries.
 		- 'message' :: This indicates the entry is just to log an event
 		- 'error' :: This indicates the entry is to log an error
 :: message :: This is the string to enter as the message for this event log.

*/
function log_entry ( $type2, $text2, $user) {
	$type = $type2;
	$text = $text2;
	$ip = $_SERVER['REMOTE_ADDR'];
	if ( isset ( $user ) ){
		$user = $user ;
	}
	else{
		$user="Uknown";
	}
	$sql = mysql_query('INSERT INTO `error_logs` ( `type`, `message`, `date`, `source`, `user`) VALUES (\'' . mysql_real_escape_string ( $type ) . '\', \'' . mysql_real_escape_string ( $text ) . '\', \'' . date('H:i:s d/m/Y') . '\', \'' . mysql_real_escape_string ( $ip ) . '\', \'' . mysql_real_escape_string ( $user ) . '\')' ) or die ( 'SELECT error: ' . mysql_error() );
}

// Note: hsc is an abbreviation of htmlspecialchars
function hscFixed ( $str ) {
   return preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $str); 
}

  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

 function Truncate($string, $limit, $break=" ", $pad="...")
   {
    // return with no change if string is shorter than $limit
 if(strlen($string) <= $limit) return $string;
    // is $break present between $limit and the end of the string?

 $string = substr($string, 0, $limit);
 if(false !== ($breakpoint = strrpos($string, $break))) {
 $string = substr($string, 0, $breakpoint);
    }
    
 return $string . $pad;
   }
   

function findexts ($filename)
{
 $filename = strtolower($filename) ;
 $exts = split("[/\\.]", $filename) ;
 $n = count($exts)-1;
 $exts = $exts[$n];
 return $exts;
} 

function thumbnailjpg($image_path,$thumb_path,$image_name,$resize)
		{
    	$src_img = imagecreatefromjpeg($image_path.$image_name);
    	$origw = imagesx($src_img);
    	$origh = imagesy($src_img);
		
		if($origh < $origw)		
		{
    	$new_w = $resize;
		$new_h = $origh * ($new_w/$origw);
		}
		else	
		{
    	$new_h = $resize;
		$new_w = $origw * ($new_h/$origh);
		}
		
    	$dst_img = imagecreatetruecolor($new_w,$new_h);
    	imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));

    	imagejpeg($dst_img, "$thumb_path/$image_name");
    	return true;
		}
function thumbnailpng($image_path,$thumb_path,$image_name,$resize)
		{
    	$src_img = imagecreatefrompng($image_path.$image_name);
    	$origw = imagesx($src_img);
    	$origh = imagesy($src_img);
		
		if($origh < $origw)		
		{
    	$new_w = $resize;
		$new_h = $origh * ($new_w/$origw);
		}
		else	
		{
    	$new_h = $resize;
		$new_w = $origw * ($new_h/$origh);
		}
		
    	$dst_img = imagecreatetruecolor($new_w,$new_h);
    	imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));

    	imagepng($dst_img, "$thumb_path/$image_name");
    	return true;
		}
function thumbnailgif($image_path,$thumb_path,$image_name,$resize)
		{
    	$src_img = imagecreatefromgif($image_path.$image_name);
    	$origw = imagesx($src_img);
    	$origh = imagesy($src_img);
		
		if($origh < $origw)		
		{
    	$new_w = $resize;
		$new_h = $origh * ($new_w/$origw);
		}
		else	
		{
    	$new_h = $resize;
		$new_w = $origw * ($new_h/$origh);
		}
		
    	$dst_img = imagecreatetruecolor($new_w,$new_h);
    	imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,imagesx($src_img),imagesy($src_img));

    	imagegif($dst_img, "$thumb_path/$image_name");
    	return true;
		}

		
		
function preview($episode, $img1, $img2, $img3)
		{
		if($episode < 10 && $episode !='mon' && $episode != 'ddr')
		{$episode = '00'.$episode;}
		if($episode >= 10 && $episode < 100)
		{$episode = '0'.$episode;}

		if($img1 < 10)
		{$img1 = '0'.$img1;}
		if($img2 < 10)
		{$img2 = '0'.$img2;}
		if($img3 < 10)
		{$img3 = '0'.$img3;}

		$path = "http://www.bleach7.com/member/images/screencaps/thumbs/";
		$file1 = $path.'e'.$episode.' - '.$img1.'.jpg';
		$file2 = $path.'e'.$episode.' - '.$img2.'.jpg';
		$file3 = $path.'e'.$episode.' - '.$img3.'.jpg';


		if($episode != 'ddr' && $episode != 'mon')
		{
		echo'
		<center>
		<table border="0" class="artg" cellpadding="0" cellspacing="0">
		<tr>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/Episode '.$episode.'/e'.$episode.' - '.$img1.'.jpg"><img src="'.$file1.'" alt="Episode Preview 1" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/Episode '.$episode.'/e'.$episode.' - '.$img2.'.jpg"><img src="'.$file2.'" alt="Episode Preview 2" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/Episode '.$episode.'/e'.$episode.' - '.$img3.'.jpg"><img src="'.$file3.'" alt="Episode Preview 3" /></a></td>
		</tr>
		</table>
		</center>
		';
		}
		elseif($episode == 'mon')
		{
		echo'
		<center>
		<table border="0" class="artg" cellpadding="0" cellspacing="0">
		<tr>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/movie/e'.$episode.' - '.$img1.'.jpg"><img src="'.$file1.'" alt="Movie Preview 1" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/movie/e'.$episode.' - '.$img2.'.jpg"><img src="'.$file2.'" alt="Movie Preview 2" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/movie/e'.$episode.' - '.$img3.'.jpg"><img src="'.$file3.'" alt="Movie Preview 3" /></a></td>
		</tr>
		</table>
		</center>
		';
		}
		elseif($episode == 'ddr')
		{
		echo'
		<center>
		<table border="0" class="artg" cellpadding="0" cellspacing="0">
		<tr>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/ddr/e'.$episode.' - '.$img1.'.jpg"><img src="'.$file1.'" alt="Movie Preview 1" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/ddr/e'.$episode.' - '.$img2.'.jpg"><img src="'.$file2.'" alt="Movie Preview 2" /></a></td>
		 <td><a href="http://www.bleach7.com/member/images/screencaps/ddr/e'.$episode.' - '.$img3.'.jpg"><img src="'.$file3.'" alt="Movie Preview 3" /></a></td>
		</tr>
		</table>
		</center>
		';
		}
		}

		
function encKey($time)
{
$time = str_ireplace('0','y8',$time);
$time = str_ireplace('1','5a',$time);
$time = str_ireplace('2','l6',$time);
$time = str_ireplace('3','r2',$time);
$time = str_ireplace('4','04',$time);
$time = str_ireplace('5','1f',$time);
$time = str_ireplace('6','g5',$time);
$time = str_ireplace('7','b9',$time);
$time = str_ireplace('8','35',$time);
$time = str_ireplace('9','jo',$time);

return $time;
}
?>
