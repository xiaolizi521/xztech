<?php
session_start();
include ( "db.php" );

include ( "settings.php" );

include ( "functions.php" );

include ( "member/header.php" );
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
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
<link rel="stylesheet" type="text/css" href="css/transition.css" />
</head>
<body bgcolor="#e8e8e8" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<?php
//include ( "member/header.php" );
?>
<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="700" id="AutoNumber1" height="930">
		<tr>
			<td width="700" colspan="3" height="164">
		      <img border="0" src="images/banner.gif" alt="" width="700" height="164" /></td>
		</tr>
		<tr>
			<td width="700" colspan="3" height="24"><img border="0" src="images/spaceuppernavleft.gif" alt="" width="9" height="24" /><a href="http://www.bleach7.com"><img border="0" src="images/homepage.gif" alt="" width="83" height="24" /></a><a href="?page=information"><img border="0" src="images/information.gif" alt="" width="101" height="24" /></a><a href="?page=multimedia"><img border="0" src="images/multimedia.gif" alt="" width="98" height="24" /></a><a href="?page=assorted"><img border="0" src="images/assorted.gif" alt="" width="70" height="24" /></a><img border="0" src="images/spaceuppernavright.gif" alt="" width="339" height="24" /></td>
		</tr>
		<tr>
			<td width="385" rowspan="2" bgcolor="#ffffff" valign="top" height="85"><font face="Verdana" size="1">&nbsp;<a href='http://www.bleach7.com/ads/adclick.php?n=afe6fe16' target='_blank'><img src="http://www.bleach7.com/ads/adview.php?n=afe6fe16" alt="" border="0" /></a></font></td>
			<td width="127" height="21"><img border="0" src="images/donations.gif" alt="" width="127" height="21" /></td>
			<td width="188" height="21"><img border="0" src="images/latestreleases.gif" alt="" width="188" height="21" /></td>
		</tr>
		<tr>
			<td width="127" bgcolor="#ffffff" height="64">
				<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber2">
					<tr bgcolor="#ffffff">
						<td width="16%" bgcolor="#ffffff"><p align="right" />&nbsp;</td>
						<td width="84%" bgcolor="#ffffff"><font face="Verdana" size="1">&nbsp; Goal: $250<br />
          					&nbsp; Current: $351<br />
							&nbsp; Remaining: $0<br />
							&nbsp;&nbsp; <b>THANK YOU!</b><br />
							&nbsp;[ <b><a href="https://www.paypal.com/xclick/business=donate@bleach7.com&amp;item_name=Bleach7.com&amp;no_note=1&amp;tax=0&amp;currency_code=USD" target="_blank" class="OnBlack">Donate</a></b> ][<a href="?page=donations" class="OnBlack">List</a> ]</font></td>
					</tr>
				</table>
			</td>
			<td width="188" background="images/navbg.gif" valign="top" height="64">
				<div align="center">
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="178" id="AutoNumber3">
					<tr>
						<td width="9">&nbsp;</td>
						<td width="162"><font face="Verdana" size="1">&nbsp; RAW: <a href="?page=">Bleach Episode #048</a><br />
							&nbsp; SUB:&nbsp; <a href="?page=">Bleach Episode #048</a><br />
							&nbsp;&nbsp;&nbsp; - - - - - - - - - - - - - - - <br />
							&nbsp; RAW: <a href="?page=media/rawmanga">Bleach Chapter #194</a><br />
							&nbsp; SUB:&nbsp; <a href="?page=media/mangalatest">Bleach Chapter #194</a></font></td>
						<td width="7">&nbsp;</td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td width="512" colspan="2" bgcolor="#ffffff" valign="top" height="32"><img border="0" src="images/bannerrotationsitecontent.gif" alt="" width="512" height="32" /></td>
			<td width="188" bgcolor="#ffffff" background="images/navbg.gif" valign="top" height="32"><img border="0" src="images/sitenav.gif" alt="" width="188" height="32" /></td>
		</tr>
		<tr>
			<td width="512" colspan="2" bgcolor="#ffffff" valign="top" height="715">
				<div align="center">
					<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="500" id="AutoNumber5">
						<tr>
							<td width="12" valign="top">&nbsp;</td>
							<td width="483"><font face="Verdana" size="1" color="#2B5C85"><?php

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
							</font></td>
							<td width="5" valign="top">&nbsp;</td>
						</tr>
					</table>
				</div>
			</td>
			<td width="188" bgcolor="#ffffff" background="images/navbg.gif" valign="top" height="715">
			<div align="center">
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="177" id="AutoNumber4">
					<tr>
						<td width="11" valign="top">&nbsp;</td>
						<td width="166" valign="top"><font face="Verdana" size="1">&nbsp;&nbsp;<b>[</b><u>Main Navigation</u><b>]</b><br />
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
&nbsp;&nbsp;<b>[</b><u>Bleach Information</u><b>]</b><br />
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
&nbsp;&nbsp;<b>[</b><u>Bleach Multimedia</u><b>]</b><br />
&nbsp;&nbsp; » <a href="?page=media/torrents/ripper" class="OnBlack">Bleach BitTorrent</a><br />
&nbsp;&nbsp; » <a href="http://www.bleachgallery.com" target="_blank" class="OnBlack">Bleach Image Gallery</a><br />
&nbsp;&nbsp; » <a href="?page=media/rawmanga" class="OnBlack">Bleach RAW Manga</a><br />
&nbsp;&nbsp; » <a href="?page=media/manga" class="OnBlack">Bleach Manga Downloads</a><br />
&nbsp;&nbsp; » <a href="?page=media/music" class="OnBlack">Bleach Music Downloads</a><br />
&nbsp;&nbsp; » <a href="?page=media/wallpapers" class="OnBlack">Bleach Wallpapers</a><br />
<br />
&nbsp;&nbsp;<b>[</b><u>Fan Interaction</u><b>]</b><br />
&nbsp;&nbsp; « <a href="?page=fan/fanart" class="OnBlack2a">Fan Art</a><br />
&nbsp;&nbsp; « <a href="?page=fan/fanfiction" class="OnBlack2a">Fan Fictions</a><br />
<br />
&nbsp;&nbsp;<b>[</b><u>Good Buddies</u><b>]</b><br />
&nbsp;&nbsp; » <a href="http://www.animestocks.com/" target="_blank" class="OnWhite3">Anime Stocks</a><br />
&nbsp;&nbsp; » <a href="http://www.bukujutsu.net/" target="_blank" class="OnWhite3">BukuJutsu</a><br />
&nbsp;&nbsp; » <a href="http://www.combovideos.com/" target="_blank" class="OnWhite3">Combo Videos</a><br />
&nbsp;&nbsp; » <a href="http://www.directmanga.com/" target="_blank" class="OnWhite3">Direct Manga</a><br />
&nbsp;&nbsp; » <a href="http://www.fury-entertainment.com/" target="_blank" class="OnWhite3">Fury Ent.</a><br />
&nbsp;&nbsp; » <a href="http://www.kanchofansubs.com/" target="_blank" class="OnWhite3">Kancho Fansubs</a><br />
&nbsp;&nbsp; » <a href="http://www.naruto-bunshin.com" target="_blank" class="OnWhite3">Naruto Bunshin</a><br />
&nbsp;&nbsp; » <a href="http://www.narutocentral.com/" target="_blank" class="OnWhite3">Naruto Central</a><br />
<br />
&nbsp;&nbsp;<b>[</b><u>Bleach Fansites</u><b>]</b><br />
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
&nbsp;&nbsp;<b>[</b><u>Members Area</u><b>]</b><br />
<?php

include ( "$script_folder/usercp_menu.php" );

echo "<br /><br />";

include ( "$script_folder/onlinelist.php" );

echo "<br /><br />";

?>
<br />
<div align="center">
<script type="text/javascript" src="google.js">
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
							</font></td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td width="700" colspan="3" bgcolor="#ffffff" height="23"><img border="0" src="images/layoutbot.gif" alt="" width="700" height="21" /></td>
			</tr>
			<tr>
				<td width="512" colspan="2" bgcolor="#ffffff" height="12"><font size="1" face="Tahoma">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
      COPYRIGHT © 2004-2006 BLEACH7.COM</font></td>
				<td width="188" bgcolor="#ffffff" height="12">
					<font face="Verdana" size="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
      <a href="?page=linkus">LINK US</a> | <a href="?page=contact">CONTACT</a></font></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#ffffff" height="1"><font face="Verdana" size="1">&nbsp;</font></td>
				<td width="188" bgcolor="#ffffff" height="1"><font face="Verdana" size="1">&nbsp;</font></td>
			</tr>
		</table>
	</div><a href="http://t.extreme-dm.com/?login=konb7" target="_top"><img src="http://t1.extreme-dm.com/i.gif" alt="eXTReMe Tracker" border="0" height="1" width="1" /></a>
<script type="text/javascript" src="EX.js">
</script>
<script type="text/javascript" src="EXmain.js">
</script>
<img src="http://e0.extreme-dm.com/s9.g?login=konb7&amp;j=n&amp;jv=n" alt="" height="1" width="1" />
</body>

</html>