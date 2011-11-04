<?php
	require_once ('./header.php');
	require_once ( "./member/db.php" );
	require_once ( "member/settings.php" );
	require_once ( "member/functions.php" );
	require_once ( "./member/header.php" );

/*
    if ($_SERVER['HTTP_HOST'] == 'bleach7.maximum7.net')
    {header('Location: http://www.bleach7.com');}
*/

?>
		<div id="layout">
			<div id="i02" class="pos">
				<object type="application/x-shockwave-flash" data="images/topnav_buttons.swf" width="312" height="60" style="margin-top: 1px;">
					<param name="movie" value="images/topnav_buttons.swf" />
				</object>
			</div>
			<div id="i09" class="pos"><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="image" src="/images/index_09.jpg" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
" />
</form>
<!--<a href="https://www.paypal.com/xclick/business=donate@maximum7.net&amp;item_name=Bleach7.com&amp;no_note=1&amp;tax=0&amp;currency_code=USD"><img src="./images/index_09.jpg" alt="Donate!" /></a>--></div>
<?php
	require_once ('./member/donations.php');
?>
				<div id="i28" class="pos"><a href="?page=donations"><img src="./images/index_28.jpg" alt="List of Donators" /></a></div>
			</div>
<?php
	require_once ( './member/member_section.php' );
?>
			<div id="mainlinks" class="pos"><img id="i32" src="./images/index_32.jpg" alt="Menu" />
				<a href="?page=general"><img id="i34" src="./images/index_34.jpg" alt="General" /></a>
				<a href="?page=information"><img id="i36" src="./images/index_36.jpg" alt="Information" /></a>
				<a href="?page=multimedia"><img id="i38" src="./images/index_38.jpg" alt="Media" /></a>
				<a href="?page=interaction"><img id="i40" src="./images/index_40.jpg" alt="Interaction" /></a>
				<a href="?page=help"><img id="i42" src="./images/index_42.jpg" alt="Help" /></a>
				<a href="?page=manga"><img id="i44" src="./images/index_44.jpg" alt="Manga" /></a>
			</div>
			<div id="banner"><center>



<?php
  define('MAX_PATH', '/home/bleach7/public_html/rotation');
  if (@include_once(MAX_PATH . '/www/delivery/alocal.php')) {
    if (!isset($phpAds_context)) {
      $phpAds_context = array();
    }
    $phpAds_raw = view_local('', 1, 0, 0, '', '', '0', $phpAds_context);

    $pattern = '/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i';			
    $replacement = '&amp;';	
    $string = preg_replace ( $pattern, $replacement, $string);
    echo preg_replace( $pattern, $replacement, $phpAds_raw['html']);
  }
/*
Adbrite code for above news post
<!-- Begin: AdBrite -->
<script type="text/javascript">
   var AdBrite_Title_Color = 'a60000';
   var AdBrite_Text_Color = '000000';
   var AdBrite_Background_Color = 'fefefe';
   var AdBrite_Border_Color = 'fefefe';
   var AdBrite_URL_Color = '000000';
</script>
<span style="white-space:nowrap;"><script src="http://ads.adbrite.com/mb/text_group.php?sid=666612&zs=3436385f3630" type="text/javascript"></script><!--
--><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=666612&afsid=1"><img src="http://files.adbrite.com/mb/images/adbrite-your-ad-here-banner.gif" style="background-color:#fefefe;border:none;padding:0;margin:0;" alt="Your Ad Here" width="11" height="60" border="0" /></a></span>
<!-- End: AdBrite -->
*/

?>

</center>
</div>
<?php
	require_once ('./member/release.php');
?>
			<div id="bleachforums"><a href="http://www.bleachforums.com" onmouseover="mouseOver()" onmouseout="mouseOut()" target="_blank">
				<img id="i54" src="./images/index_54.jpg" alt="BleachForums.com" style="vertical-align: top;" />
				<img id="i62" src="./images/index_62.jpg" alt="BleachForums.com" style="vertical-align: top;" name="i62" /></a>
			</div>
		</div>
		<div id="main_sec">
			<table cellpadding="0" cellspacing="0" id="MainTable">
				<tr>
					<td class="MainTopLeft">&nbsp;</td>
					<td class="Main">
<!-- AD GOES HERE FOR RIGHT ABOVE NEWS POST -->


<br /><br />
<?php

/*
if ( file_exists ( "".$ident.".php" ) && isset ( $_GET[$ident] ) && !empty ( $_GET[$ident] ) ) {
	if ( ereg ( "media", $_SERVER[QUERY_STRING] ) ) {
		if ( isset ( $user_info[user_id] ) ) {
			include ( "".$$ident.".php" );
		}
		else {
			echo "<p align='center'>You need to be registered to access this page, please <a href='$site_path/login'><b>login</b></a> or <a href='$site_path/register'><b>register</b></a>.</p>";
		}
	}
	else {
		include ( "".$$ident.".php" );
	}
	$delete_oldguests = mysql_query ( "DELETE FROM guests WHERE UNIX_TIMESTAMP(now()) - last_activity_time > 600" );
	if ( isset ( $user_info[user_id] ) ) {
		$result_writeonline = mysql_query ( "UPDATE users SET last_activity_time=$timenow, last_activity_title='$file_title', last_activity_url='$current_location', ip_address='$_SERVER[REMOTE_ADDR]' WHERE user_id='$user_info[user_id]'" );
	}
	else {
		$delete_guestonline = mysql_query ( "DELETE FROM guests WHERE ip_address='$_SERVER[REMOTE_ADDR]'" );
		$insert_guestsonline = mysql_query ( "INSERT INTO guests ( ip_address, last_activity_time, last_activity_title, last_activity_url ) VALUES ( '$_SERVER[REMOTE_ADDR]', $timenow, '$file_title', '$current_location' )" );
	}
}
else {
	include ( "$script_folder/news.php" );
	}
*/


if ( file_exists ( "".$page.".php" ) )
{	
$timenow = time();
    if(ereg('submit', $page) && empty($user_info['username']))
	{
	include('login.php');
	}
	else
	{
	include $page.'.php';
    }
	$delete_oldguests = mysql_query ( "DELETE FROM guests WHERE UNIX_TIMESTAMP(now()) - last_activity_time > 300" );
	if ( isset($user_info['user_id']) && !empty($user_info['user_id']) )
	 {
	 $user=$user_info['user_id'];
	 if(empty($file_title))
	 {$file_title=truncate($current_location, 40);}
	 $result_writeonline = mysql_query ( "UPDATE users SET last_activity_time=$timenow, last_activity_title='$file_title', last_activity_url='$current_location', ip_address='$_SERVER[REMOTE_ADDR]' WHERE user_id='$user'" );
	 }
	else 
	{
		 if(empty($file_title))
		 {$file_title=truncate($current_location, 40);}
		
		$delete_guestonline = mysql_query ( "DELETE FROM guests WHERE ip_address='$_SERVER[REMOTE_ADDR]'" );
		$insert_guestsonline = mysql_query ( "INSERT INTO guests ( ip_address, last_activity_time, last_activity_title, last_activity_url ) VALUES ( '$_SERVER[REMOTE_ADDR]', $timenow, '$file_title', '$current_location' )" );
	}
	
	
}
//page doesn't exist, must be front page or wrong. Spit out news.
else
{
	include ( "$script_folder/news.php" );

}

//header ( "Location: $site_path/news" );
//exit();

/*
bottom content adbrite
<!-- Begin: AdBrite -->
<script type="text/javascript">
   var AdBrite_Title_Color = 'a60000';
   var AdBrite_Text_Color = '000000';
   var AdBrite_Background_Color = 'F7F6F1';
   var AdBrite_Border_Color = 'F7F6F1';
   var AdBrite_URL_Color = '000000';
</script>
<span style="white-space:nowrap;"><script src="http://ads.adbrite.com/mb/text_group.php?sid=666634&zs=3436385f3630" type="text/javascript"></script><!--
--><a target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=666634&afsid=1"><img src="http://files.adbrite.com/mb/images/adbrite-your-ad-here-banner.gif" style="background-color:#F7F6F1;border:none;padding:0;margin:0;" alt="Your Ad Here" width="11" height="60" border="0" /></a></span>
<!-- End: AdBrite -->
*/
?>
<br /><br />


<!-- GOOGLE AD CODE HERE -->



					</td>
					<td class="side_bar">
						<table cellpadding="0" cellspacing="0" id="Side_Bar">
							<tr>
								<td class="side_bar_main">
<?php
	require_once ('./member/navigation.php');
	
	/*$randcheck = rand(1, 30);
	if($randcheck = 5)
	{include ( "$script_folder/onlinecheck.php" );}*/
	
	require_once ('./member/onlinelist.php');
?>
								</td>
							</tr>
							<tr>
								<td>
									<div id="google">
<!-- Sidebar banner rotation -->
<?php

  define('MAX_PATH', '/home/bleach7/public_html/rotation');
  if (@include_once(MAX_PATH . '/www/delivery/alocal.php')) {
    if (!isset($phpAds_context)) {
      $phpAds_context = array();
    }
    $phpAds_raw = view_local('', 3, 0, 0, '', '', '0', $phpAds_context);
    $pattern = '/&(?!(?i:\#((x([\dA-F]){1,5})|(104857[0-5]|10485[0-6]\d|1048[0-4]\d\d|104[0-7]\d{3}|10[0-3]\d{4}|0?\d{1,6}))|([A-Za-z\d.]{2,31}));)/i';			
    $replacement = '&amp;';	
    $string = preg_replace ( $pattern, $replacement, $string);
    echo preg_replace( $pattern, $replacement, $phpAds_raw['html']);
    echo '<!--<br /><a href="http://bleach7.com/?page=rotation"><img src="http://bleach7.com/images/rotation/hori.gif" /></a>-->';
  }

/*
<style type="text/css">
   .adHeadline {font: bold 10pt Arial; text-decoration: underline; color: #a60000;}
   .adText {font: normal 10pt Arial; text-decoration: none; color: #000000;}
</style>
<script type="text/javascript" src="http://ads.adbrite.com/mb/text_group.php?sid=666629&br=1&dk=7469636b6574735f355f325f776562">
</script>
<div><a class="adHeadline" target="_top" href="http://www.adbrite.com/mb/commerce/purchase_form.php?opid=666629&afsid=1">Your Ad Here</a></div>
*/

?>

									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
<?php
	require_once ('./footer.php');
?>
