<?php

	require_once ('./header2.php');

	require_once ( "./member/header.php" );
?>
		<div id="layout">
			<div id="i02" class="pos">
				<object type="application/x-shockwave-flash" data="images/topnav_buttons.swf" width="312" height="60" style="margin-top: 1px;">
					<param name="movie" value="images/topnav_buttons.swf" />
				</object>
			</div>
			<div id="i09" class="pos"><a href="https://www.paypal.com/xclick/business=donate@bleach7.com&amp;item_name=Bleach7.com&amp;no_note=1&amp;tax=0&amp;currency_code=USD"><img src="./images/index_09.jpg" alt="Donate!" /></a></div>
<?php
	require_once ('./member/donations.php');
?>
				<div id="i28" class="pos"><a href="?page=donations"><img src="./images/index_28.jpg" alt="List of Donators" /></a></div>
			</div>
<?php
	require_once ( './member/member_section.php' );
?>
			<div id="mainlinks" class="pos"><img id="i32" src="./images/index_32.jpg" alt="Menu" />
				<a href="http://www.bleach7.com"><img id="i34" src="./images/index_34.jpg" alt="General" /></a>
				<a href="?page=information"><img id="i36" src="./images/index_36.jpg" alt="Information" /></a>
				<a href="?page=multimedia"><img id="i38" src="./images/index_38.jpg" alt="Media" /></a>
				<a href="?page="><img id="i40" src="./images/index_40.jpg" alt="Interaction" /></a>
				<a href="?page="><img id="i42" src="./images/index_42.jpg" alt="Help" /></a>
				<a href="?page=fansitelist"><img id="i44" src="./images/index_44.jpg" alt="Links" /></a>
			</div>
			<div id="banner"><a href="http://www.bleach7.com/ads/adclick.php?n=afe6fe16"><img id="i56" src="http://www.bleach7.com/ads/adview.php?n=afe6fe16" alt="Banner Ad" /></a></div>
<?php
	require_once ('./member/release.php');
?>
			<div id="bleachforums"><a href="http://www.bleachforums.com" onmouseover="mouseOver()" onmouseout="mouseOut()">
				<img id="i54" src="./images/index_54.jpg" alt="BleachForums.com" style="vertical-align: top;" />
				<img id="i62" src="./images/index_62.jpg" alt="BleachForums.com" style="vertical-align: top;" /></a>
			</div>
		</div>
		<div id="main_sec">
			<table cellpadding="0" cellspacing="0" id="MainTable">
				<tr>
					<td class="MainTopLeft">&nbsp;</td>
					<td class="Main">
<?php
if ( file_exists ( "".$$ident.".php" ) && isset ( $_GET[$ident] ) && !empty ( $_GET[$ident] ) ) {
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
	if ( isset ( $user_info['user_id'] ) ) {
		$result_writeonline = mysql_query ( "UPDATE users SET last_activity_time=$timenow, last_activity_title='$file_title', last_activity_url='$current_location', ip_address='$_SERVER[REMOTE_ADDR]' WHERE user_id='$user_info[user_id]'" );
	}
	else {
		$delete_guestonline = mysql_query ( "DELETE FROM guests WHERE ip_address='$_SERVER[REMOTE_ADDR]'" );
		$insert_guestsonline = mysql_query ( "INSERT INTO guests ( ip_address, last_activity_time, last_activity_title, last_activity_url ) VALUES ( '$_SERVER[REMOTE_ADDR]', $timenow, '$file_title', '$current_location' )" );
	}
}
else {
	include ( "$script_folder/news.php" );
//header ( "Location: $site_path/news" );
//exit();
}?>
					</td>
					<td class="side_bar">
						<table cellpadding="0" cellspacing="0" id="Side_Bar">
							<tr>
								<td class="side_bar_main">
<?php
	require_once ('./member/navigation.php');
	
	require_once ('./member/onlinelist.php');
?>
								</td>
							</tr>
							<tr>
								<td>
									<div id="google">
										<script type="text/javascript" src="google.js">	</script>
										<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
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
