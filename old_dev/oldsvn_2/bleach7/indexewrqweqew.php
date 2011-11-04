<?php
session_start();
include ( "member/db.php" );

include ( "member/settings.php" );

include ( "member/functions.php" );

//include ( "member/header.php" );
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta name="keywords" content="bleach, bleach manga, bleach anime, tv tokyo, studio perriot, bleach online, bleach fan, bleach source, bleach world, bleach media, bleach central, bleach forums, bleach site, bleach information, bleach wallpapers, bleach episodes, bleach content, bleach chapters, bleach community, bleach sitest, bleach fansite, hollows, soul cutters, online viewing manga, manga viewer, donations, anime, media, episodes, full episodes, http downloads, links, kon, ichigo, rukia, chad, sado, characters, bleach characters, bleach story, bleach introduction, bleach stuff, bleach chaos, bleach ex, bleach images, image gallery, bleach screen shots, bleach viewing, bleach, BLEACH, Bleach Talk, Bleach Portal, Bleach World, Manga-Rain, Manga Rain, MangaRain, Shinigami Scanlations, BakaFish, Baka Fish, Scans, Bleach Remix, BleachRemix, Bleach Direct Episodes, Bleach French, Bleach Talk, Bleach Box, Soul Reaper, Soul-Reaper, Bleach Web, Bleach Internet, Bleach Insider, Soul Society, Live Journal, Soul_Society, naruto, naruto manga, naruto episode, naruto pictures, naruto bittorrent, naruto game, inane, anime, anbu, aone, animeone, toriyama, masashi kishimoto, masashi, kishimoto, saiyaman, animerev, naruto, Naruto, manga, Manga, anime, Anime, naruto manga, naruto anime, torrent, bittorrent, bit torrent, download, bittorrent download, aburame, shino, aburame shino, akado, yoroi, akado yoroi, akamaru, akimichi, choji, akimichi choji, akimichi, chomaru, akimichi chomaru, ami, aoba, baiu, baki, demon, brothers, demon brothers, dosu, kinuta, dosu kinuta, ebisu, enma, gaara, of the desert, gaara of the desert, gai, gama, bunta, gamabunta, gama, kichi, gamakichi, gatou, gekkou, hayate, gekkou hayate, gemma, giichi, hagane, kotetsu, hagane kotetsu, haku, haruno, sakura, haruno sakura, hatake, kakashi, hatake kakashi, hokage, homura, hoshigaki, kisame, hoshigaki kisame, hyuga, hanabi, hyuga hanabi, hyuga, hiashi, hyuga hiashi, hyuga hinate, hyuga hizashi, hyuga neji, inari, inuzaka, kiba, inuzaka kiba, iruka, iwashi, jiraiya, kagari, kaiza, kamizuki, izumo, kamizuki izumo, kankuro, kazekage, kin, tsuchi, kin tsuchi, konohamaru, koharu, komaru, kyubi, midare, mitarashi, anko, mitarashi anko, mizukage, mizuki, momochi, zabuza, momochi zabuza, morino, ibiki, morino ibiki, mubi, nara, shikamaru, nara shikamaru, nara shikato, oboro, orochimaru, pakku, pochi, raido, rock, lee, rock lee, sarutobi, asuma, sarutobi asuma, sigure, sinobi, gashir, sinobi gashir, shizimi, suzume, tazuna, temari, tenten, ten ten, tonbo, tora, tsuchikage, tsunade, tsunami, tsurugu, misumi, tsurugu misumi, uchiha, itachi, uchiha atachi, sasuke, uchiha sasuke, itachi, uchiha itachi, uzumaki, uzumaki naruto, waraji, yakushi, yakushi kabuto, kabuto, yamanaka, ino, yamanaka ino, inoshi, yamanaka inoshi, yashamaru, yuuhi kurenai, kurenai, yuuhi, zaku, abumi, zaku abumi, zouri, episodes, full episodes, download, multimedia, screencap, screencaps, screen captures, sound, sound clip, video, video clip, summary, episode summary, summaries, episode summaries, anime, pictures, images, picture, image, message board, forum, chat, buddy icon, buddy, icon, manga, downloads, review, reviews, download, wallpaper, music, characters, character, bio, character bio, chakra, sharingan" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="content-language" content="en" />
<meta name="DESCRIPTION" content="Bleach 7 - The First Source for Bleach Information, Media, News and Fan Interaction." />
<meta name="ROBOTS" content="ALL,INDEX,FOLLOW" />
<meta name="REVISIT-AFTER" content="1 day" />
<meta name="rating" content="General" />
<meta name="Language" content="English" />
<meta name="resource-type" content="document" />
<meta name="distribution" content="Global" />
<meta name="copyright" content="Bleach7.com" />
<meta name="WARNING" content="All HTML, Javascript, and any other script used on this site is strictly for Bleach7.com only. All information on this web site is solely for Bleach7.com unless otherwise stated. Bleach7.com is strictly a fan site where by no infringement is intended. Any reproduction of this sites content, pages, HTML e.t.c will be dealt with accordingly. ©2004-2005 Bleach 7(bleach7.com)" />
<title>Bleach 7 - The First Source for Bleach Information, Media, News, and Fan Interaction! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bleach, bleach manga, bleach anime, tv tokyo, studio perriot, bleach online, bleach fan, bleach source, bleach world, bleach media, bleach central, bleach forums, bleach site, bleach information, bleach wallpapers, bleach episodes, bleach content, bleach chapters, bleach community, bleach sitest, bleach fansite, hollows, soul cutters, online viewing manga, manga viewer, donations, anime, media, episodes, full episodes, http downloads, links, kon, ichigo, rukia, chad, sado, characters, bleach characters, bleach story, bleach introduction, bleach stuff, bleach chaos, bleach ex, bleach images, image gallery, bleach screen shots, bleach viewing, bleach, BLEACH, Bleach Talk, Bleach Portal, Bleach World, Manga-Rain, Manga Rain, MangaRain, Shinigami Scanlations, BakaFish, Baka Fish, Scans, Bleach Remix, BleachRemix, Bleach Direct Episodes, Bleach French, Bleach Talk, Bleach Box, Soul Reaper, Soul-Reaper, Bleach Web, Bleach Internet, Bleach Insider, Soul Society, Live Journal, Soul_Society, naruto, naruto manga, naruto episode, naruto pictures, naruto bittorrent, naruto game, inane, anime, anbu, aone, animeone, toriyama, masashi kishimoto, masashi, kishimoto, saiyaman, animerev, naruto, Naruto, manga, Manga, anime, Anime, naruto manga, naruto anime, torrent, bittorrent, bit torrent, download, bittorrent download, aburame, shino, aburame shino, akado, yoroi, akado yoroi, akamaru, akimichi, choji, akimichi choji, akimichi, chomaru, akimichi chomaru, ami, aoba, baiu, baki, demon, brothers, demon brothers, dosu, kinuta, dosu kinuta, ebisu, enma, gaara, of the desert, gaara of the desert, gai, gama, bunta, gamabunta, gama, kichi, gamakichi, gatou, gekkou, hayate, gekkou hayate, gemma, giichi, hagane, kotetsu, hagane kotetsu, haku, haruno, sakura, haruno sakura, hatake, kakashi, hatake kakashi, hokage, homura, hoshigaki, kisame, hoshigaki kisame, hyuga, hanabi, hyuga hanabi, hyuga, hiashi, hyuga hiashi, hyuga hinate, hyuga hizashi, hyuga neji, inari, inuzaka, kiba, inuzaka kiba, iruka, iwashi, jiraiya, kagari, kaiza, kamizuki, izumo, kamizuki izumo, kankuro, kazekage, kin, tsuchi, kin tsuchi, konohamaru, koharu, komaru, kyubi, midare, mitarashi, anko, mitarashi anko, mizukage, mizuki, momochi, zabuza, momochi zabuza, morino, ibiki, morino ibiki, mubi, nara, shikamaru, nara shikamaru, nara shikato, oboro, orochimaru, pakku, pochi, raido, rock, lee, rock lee, sarutobi, asuma, sarutobi asuma, sigure, sinobi, gashir, sinobi gashir, shizimi, suzume, tazuna, temari, tenten, ten ten, tonbo, tora, tsuchikage, tsunade, tsunami, tsurugu, misumi, tsurugu misumi, uchiha, itachi, uchiha atachi, sasuke, uchiha sasuke, itachi, uchiha itachi, uzumaki, uzumaki naruto, waraji, yakushi, yakushi kabuto, kabuto, yamanaka, ino, yamanaka ino, inoshi, yamanaka inoshi, yashamaru, yuuhi kurenai, kurenai, yuuhi, zaku, abumi, zaku abumi, zouri, episodes, full episodes, download, multimedia, screencap, screencaps, screen captures, sound, sound clip, video, video clip, summary, episode summary, summaries, episode summaries, anime, pictures, images, picture, image, message board, forum, chat, buddy icon, buddy, icon, manga, downloads, review, reviews, download, wallpaper, music, characters, character, bio, character bio, chakra, sharingan</title>
<link rel="SHORTCUT ICON" href="http://bleach7.com/B7.ico" />
<link rel="stylesheet" type="text/css" href="css/transition2.css" />

</head>
<body>
<?php
include ( "member/header1.php" );
?>
<div align="center">
	<table cellpadding="0" cellspacing="0" id="AutoNumber1">
		<tr>
			<td colspan="3" class="banner">
		      <img src="images/banner.gif" alt="" class="banner" /></td>
		</tr>
		<tr>
			<td colspan="3" class="NavBar">
				<img src="images/spaceuppernavleft.gif" alt="" class="spaceuppernavleft" /><a href="http://www.bleach7.com"><img src="images/homepage.gif" alt="" class="homepage" /></a><a href="?page=information"><img src="images/information.gif" alt="" width="101" height="24" /></a><a href="?page=multimedia"><img src="images/multimedia.gif" alt="" width="98" height="24" /></a><a href="?page=assorted"><img src="images/assorted.gif" alt="" width="70" height="24" /></a><img src="images/spaceuppernavright.gif" alt="" width="339" height="24" /></td>
		</tr>
		<tr>
			<td rowspan="2" class="ad"><span class="VerdanaSize1">&nbsp;<a href='http://www.bleach7.com/ads/adclick.php?n=afe6fe16' target='_blank'><img src="http://www.bleach7.com/ads/adview.php?n=afe6fe16" alt="" class="ad" /></a></span></td>
			<td class="donations"><img src="images/donations.gif" alt="Donations" class="donations" /></td>
			<td class="latestreleases"><img src="images/latestreleases.gif" alt="Latest Releases" class="latestreleases" /></td>
		</tr>
		<tr>
			<td class="donationsmain">
				<table cellpadding="0" cellspacing="0" id="AutoNumber2">
					<tr class="donationsmain">
						<td class="donatspace"><p class="donatspace" /> </td>
						<td class="donatinfo"><span class="VerdanaSize1">
							&nbsp; Goal: $250<br />
          					&nbsp; Current: $351<br />
							&nbsp; Remaining: $0<br />
							&nbsp;&nbsp; <b>THANK YOU!</b><br />
							&nbsp;[ <b><a href="https://www.paypal.com/xclick/business=donate@bleach7.com&amp;item_name=Bleach7.com&amp;no_note=1&amp;tax=0&amp;currency_code=USD" target="_blank" class="OnBlack">Donate</a></b> ][<a href="?page=donations" class="OnBlack">List</a> ]</span></td>
					</tr>
				</table>
			</td>
			<td class="navbg">
				<div align="center">
				<table cellpadding="0" cellspacing="0" id="AutoNumber3">
					<tr>
						<td class="latrelspace1">&nbsp;</td>
						<td class="latrelmain"><span class="VerdanaSize1">
							&nbsp; RAW: <a href="?page=">Bleach Episode #050</a><br />
							&nbsp; SUB:&nbsp; <a href="?page=">Bleach Episode #049</a><br />
							&nbsp;&nbsp;&nbsp; - - - - - - - - - - - - - - - <br />
							&nbsp; RAW: <a href="?page=media/rawmanga">Bleach Chapter #195</a><br />
							&nbsp; SUB:&nbsp; <a href="?page=media/mangalatest">Bleach Chapter #195</a></span></td>
						<td class="latrelspace2">&nbsp;</td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bannerrotationsitecontent"><img src="images/bannerrotationsitecontent.gif" alt="" class="bannerrotationsitecontent" /></td>
			<td class="siteNav"><img src="images/sitenav.gif" alt="" class="siteNav" /></td>
		</tr>
		<tr>
			<td colspan="2" class="mainphpouter">
				<div align="center">
					<table cellpadding="0" cellspacing="0" id="AutoNumber5">
						<tr>
							<td class="mainphpinnerspace1">&nbsp;</td>
							<td class="mainphpinnermain"><span class="VerdanaSize1Main"><?php

if ( file_exists ( "".$$ident.".php" ) && isset ( $_GET[$ident] ) && !empty ( $_GET[$ident] ) ) {

if ( ereg ( "media", $_SERVER[QUERY_STRING] ) ) {

if ( isset ( $user_info[user_id] ) ) {

include ( "".$$ident.".php" );

} else {

echo "<center>You need to be registered to access this page, please <a href='$site_path/login'><b>login</b></a> or <a href='$site_path/register'><b>register</b></a>.</center>";

}

} else {

include ( "".$$ident.".php" );

}

$delete_oldguests = mysql_query ( "DELETE FROM guests WHERE UNIX_TIMESTAMP(now()) - last_activity_time > 600" );

if ( isset ( $user_info[user_id] ) ) {

$result_writeonline = mysql_query ( "UPDATE users SET last_activity_time=$timenow, last_activity_title='$file_title', last_activity_url='$current_location', ip_address='$_SERVER[REMOTE_ADDR]' WHERE user_id='$user_info[user_id]'" );

} else {

$delete_guestonline = mysql_query ( "DELETE FROM guests WHERE ip_address='$_SERVER[REMOTE_ADDR]'" );

$insert_guestsonline = mysql_query ( "INSERT INTO guests ( ip_address, last_activity_time, last_activity_title, last_activity_url ) VALUES ( '$_SERVER[REMOTE_ADDR]', $timenow, '$file_title', '$current_location' )" );

}

} else {

include ( "$script_folder/news.php" );

//header ( "Location: $site_path/news" );

//exit();

}?>
							</span></td>
							<td class="mainphpinnerspace2">&nbsp;</td>
						</tr>
					</table>
				</div>
			</td>
			<td class="mainsidenavigation">
			<div align="center">
				<table cellpadding="0" cellspacing="0" id="AutoNumber4">
					<tr>
						<td class="sidebar1">&nbsp;</td>
						<td class="mainside"><span class="VerdanaSize1">&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Main Navigation</span><b>]</b><br />
							&nbsp;&nbsp; » <a href="http://www.bleach7.com"  class="OnWhite">FrontPage</a><br />
							&nbsp;&nbsp; » <a href="?page=contact" class="OnWhite">Contact Us</a><br />
							&nbsp;&nbsp; » <a href="?page=legaldisclaimer" class="OnWhite">Legal Disclaimer</a><br />
							&nbsp;&nbsp; » <a href="?page=linkus" class="OnWhite">Link To Bleach7.com</a><br />
							&nbsp;&nbsp; » <a href="?page=supportus" class="OnWhite">Support Bleach7.com</a><br />
							&nbsp;&nbsp; » <a href="http://www.bleachforums.com" target="_blank" class="OnWhite">BleachForums.com</a><br />
							&nbsp;&nbsp; » <a href="?page=stb" class="OnWhite">Spread the Bleach!</a><br />
							&nbsp;&nbsp; » <a href="irc://bleach7@irc.irchighway.net/" class="OnWhite">Bleach7 IRC Room</a><br />
							&nbsp;&nbsp; » <a href="?page=rotation" class="OnWhite">Banner Rotation</a><br />
							&nbsp;&nbsp; » <a href="?page=BittorrentBleach7FAQ" class="OnWhite">Bleach7/Bittorrent FAQ</a><br />
							&nbsp;&nbsp; » <a href="http://bleach7.com/cgi-bin/gtchat/chat.pl" class="OnWhite">Chat Room</a><br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Bleach Information</span><b>]</b><br />
							&nbsp;&nbsp; « <a href="?page=information/authorkubotite" class="OnWhite2">Author: Kubo Tite</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bios/main" class="OnWhite2">Biographies</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachanimeguide" class="OnWhite2">Bleach Anime Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachcaptainsguide" class="OnWhite2">Bleach Captains Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/encyclopedia" class="OnWhite2">Bleach Encyclopedia</a><br />
							&nbsp;&nbsp; « <a href="?page=information/summaries/anime" class="OnWhite2">Bleach Episode Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/factoids" class="OnWhite2">Bleach Factoids</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachgameguide" class="OnWhite2">Bleach Game Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachitemguide" class="OnWhite2">Bleach Item Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/locations" class="OnWhite2">Bleach Locations</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachmangaguide" class="OnWhite2">Bleach Manga Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/bleachspellguide" class="OnWhite2">Bleach Spell Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/weapons" class="OnWhite2">Bleach Weapon Guide</a><br />
							&nbsp;&nbsp; « <a href="?page=information/miscellaneous" class="OnWhite2">Miscellaneous</a><br />
							&nbsp;&nbsp; « <a href="?page=information/tvtokyo" class="OnWhite2">TV Tokyo Information</a><br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Bleach Multimedia</span><b>]</b><br />
							&nbsp;&nbsp; » <a href="?page=media/torrents/ripper" class="OnBlack">Bleach BitTorrent</a><br />
							&nbsp;&nbsp; » <a href="http://www.bleachgallery.com" target="_blank" class="OnBlack">Bleach Image Gallery</a><br />
							&nbsp;&nbsp; » <a href="?page=media/rawmanga" class="OnBlack">Bleach RAW Manga</a><br />
							&nbsp;&nbsp; » <a href="?page=media/manga" class="OnBlack">Bleach Manga Downloads</a><br />
							&nbsp;&nbsp; » <a href="?page=media/music" class="OnBlack">Bleach Music Downloads</a><br />
							&nbsp;&nbsp; » <a href="?page=media/wallpapers" class="OnBlack">Bleach Wallpapers</a><br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Fan Interaction</span><b>]</b><br />
							&nbsp;&nbsp; « <a href="?page=fan/fanart" class="OnBlack2a">Fan Art</a><br />
							&nbsp;&nbsp; « <a href="?page=fan/fanfiction" class="OnBlack2a">Fan Fictions</a><br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Good Buddies</span><b>]</b><br />
							&nbsp;&nbsp; » <a href="http://www.animestocks.com/" target="_blank" class="OnWhite3">Anime Stocks</a><br />
							&nbsp;&nbsp; » <a href="http://www.bukujutsu.net/" target="_blank" class="OnWhite3">BukuJutsu</a><br />
							&nbsp;&nbsp; » <a href="http://www.combovideos.com/" target="_blank" class="OnWhite3">Combo Videos</a><br />
							&nbsp;&nbsp; » <a href="http://www.directmanga.com/" target="_blank" class="OnWhite3">Direct Manga</a><br />
							&nbsp;&nbsp; » <a href="http://www.fury-entertainment.com/" target="_blank" class="OnWhite3">Fury Ent.</a><br />
							&nbsp;&nbsp; » <a href="http://www.kanchofansubs.com/" target="_blank" class="OnWhite3">Kancho Fansubs</a><br />
							&nbsp;&nbsp; » <a href="http://www.naruto-bunshin.com" target="_blank" class="OnWhite3">Naruto Bunshin</a><br />
							&nbsp;&nbsp; » <a href="http://www.narutocentral.com/" target="_blank" class="OnWhite3">Naruto Central</a><br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Bleach Fansites</span><b>]</b><br />
							&nbsp;&nbsp; « <a href="http://bleachadventsou.6.forumer.com" target="_blank" class="OnWhite4">Advent Soul</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleach.de" target="_blank" class="OnWhite4">Bleach </a>(DE)<br />
							&nbsp;&nbsp; « <a href="http://www.bleachsp.com" target="_blank" class="OnWhite4">Bleach </a>(SP)<br />
							&nbsp;&nbsp; « <a href="http://www.twilightus.com/bleacher" target="_blank" class="OnWhite4">Bleacher </a>(RPG)<br />
							&nbsp;&nbsp; « <a href="http://www.bleachcommunity.net" target="_blank" class="OnWhite4">Bleach Community</a><br />
							&nbsp;&nbsp; « <a href="http://bleachcorp.com" target="_blank" class="OnWhite4">Bleach Corp</a> (FR)<br />
							&nbsp;&nbsp; « <a href="http://www.bleachdj.com" target="_blank" class="OnWhite4">Bleach DJ</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleach-ent.com" target="_blank" class="OnWhite4">Bleach Entertainment</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachexile.com" target="_blank" class="OnWhite4">Bleach Exile</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachfan.com" target="_blank" class="OnWhite4">Bleach Fan</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachflame.com" target="_blank" class="OnWhite4">Bleach Flame</a> (PL)<br />
							&nbsp;&nbsp; « <a href="http://www.code-master.net/bleach/" target="_blank" class="OnWhite4">Bleach Info</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleach-nation.com" target="_blank" class="OnWhite4">Bleach Nation</a><br />
							&nbsp;&nbsp; « <a href="http://bleachparty.kefi.org" target="_blank" class="OnWhite4">Bleach Party</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachportal.net" target="_blank" class="OnWhite4">Bleach Portal</a><br />
							&nbsp;&nbsp; « <a href="http://productions.lovelyv.net/bleach/" target="_blank" class="OnWhite4">Bleach Radioplay</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleach-society.com/" target="_blank" class="OnWhite4">Bleach Society</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachstorm.com/" target="_blank" class="OnWhite4">Bleach Storm</a><br />
							&nbsp;&nbsp; « <a href="http://www.bleachtrad.com/" target="_blank" class="OnWhite4">Bleach Trad</a> (FR)<br />
							&nbsp;&nbsp; « <a href="http://www.bleachtv.com/" target="_blank" class="OnWhite4">Bleach TV</a><br />
							&nbsp;&nbsp; « <a href="http://bleachweb.free.fr/" target="_blank" class="OnWhite4">Bleach Web</a> (FR)<br />
							&nbsp;&nbsp; « <a href="http://bleachx.com/" target="_blank" class="OnWhite4">Bleach X</a><br />
							&nbsp;&nbsp; « <a href="http://www.BleachXL.com" target="_blank" class="OnWhite4">Bleach XL</a><br />
							&nbsp;&nbsp; « <a href="http://www.BleachXP.com" target="_blank" class="OnWhite4">Bleach XP</a><br />
							&nbsp;&nbsp; « <a href="http://bleach.animewtf.com" target="_blank" class="OnWhite4">Bleach WTF</a><br />
							&nbsp;&nbsp; « <a href="http://shinigamicentral.savefile.com/" target="_blank" class="OnWhite4">Shinigami Central</a><br />
							&nbsp;&nbsp; « <a href="http://www.livejournal.com/community/soul_society/" target="_blank" class="OnWhite4">Soul_Society (LJournal)</a><br />
							&nbsp;&nbsp; « (<a href="mailto:bleach7@gmail.com" class="OnWhite4">Submit A Link</a>)<br />
							<br />
							&nbsp;&nbsp;<b>[</b><span class="sidebartitle">Members Area</span><b>]</b><br />
<?php

include ( "$script_folder/usercp_menu.php" );

echo "<br /><br />";

include ( "$script_folder/onlinelist.php" );

echo "<br /><br />";
?>
							<br />
							<div align="center">
								<script type="text/javascript" src="google.js"></script>
								<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
							</div></span></td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="layoutbot"><img src="images/layoutbot.gif" alt="" class="layoutbot" /></td>
			</tr>
			<tr>
				<td colspan="2" class="copyright"><span class="TahomaSize1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;COPYRIGHT © 2004-2006 BLEACH7.COM</span></td>
				<td class="contact"><span class="VerdanaSize1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=linkus">LINK US</a> | <a href="?page=contact">CONTACT</a></span></td>
			</tr>
			<tr>
				<td colspan="2" class="bottomspacing1"><span class="TahomaSize1">&nbsp;</span></td>
				<td class="bottomspacing2"><span class="VerdanaSize1">&nbsp;</span></td>
			</tr>
		</table>
	</div><a href="http://t.extreme-dm.com/?login=konb7" target="_top"><img src="http://t1.extreme-dm.com/i.gif" alt="eXTReMe Tracker" class="extracker" /></a>
	<script type="text/javascript" src="EX.js"></script>
	<script type="text/javascript" src="EXmain.js"></script>
	<img src="http://e0.extreme-dm.com/s9.g?login=konb7&amp;j=n&amp;jv=n" alt="" class="extrackermain" />
</body>

</html>