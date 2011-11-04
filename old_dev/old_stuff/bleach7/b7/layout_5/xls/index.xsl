<?xml version="1.0" encoding="iso-8859-1"?>
<!-- DWXMLSource="/indextest.xml" -->
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="iso-8859-1" doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"/>
<xsl:template match="page">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
		<meta http-equiv="content-language" content="en-gb en" />
		<meta name="keywords" content="bleach, bleach manga, bleach anime, tv tokyo, studio perriot, bleach online, bleach fan, bleach source, bleach world, bleach media, bleach central, bleach forums, bleach site, bleach information, bleach wallpapers, bleach episodes, bleach content, bleach chapters, bleach community, bleach sitest, bleach fansite, hollows, soul cutters, online viewing manga, manga viewer, donations, anime, media, episodes, full episodes, http downloads, links, kon, ichigo, rukia, chad, sado, characters, bleach characters, bleach story, bleach introduction, bleach stuff, bleach chaos, bleach ex, bleach images, image gallery, bleach screen shots, bleach viewing, bleach, BLEACH, Bleach Talk, Bleach Portal, Bleach World, Manga-Rain, Manga Rain, MangaRain, Shinigami Scanlations, BakaFish, Baka Fish, Scans, Bleach Remix, BleachRemix, Bleach Direct Episodes, Bleach French, Bleach Talk, Bleach Box, Soul Reaper, Soul-Reaper, Bleach Web, Bleach Internet, Bleach Insider, Soul Society, Live Journal, Soul_Society, naruto, naruto manga, naruto episode, naruto pictures, naruto bittorrent, naruto game, inane, anime, anbu, aone, animeone, toriyama, masashi kishimoto, masashi, kishimoto, saiyaman, animerev, naruto, Naruto, manga, Manga, anime, Anime, naruto manga, naruto anime, torrent, bittorrent, bit torrent, download, bittorrent download, aburame, shino, aburame shino, akado, yoroi, akado yoroi, akamaru, akimichi, choji, akimichi choji, akimichi, chomaru, akimichi chomaru, ami, aoba, baiu, baki, demon, brothers, demon brothers, dosu, kinuta, dosu kinuta, ebisu, enma, gaara, of the desert, gaara of the desert, gai, gama, bunta, gamabunta, gama, kichi, gamakichi, gatou, gekkou, hayate, gekkou hayate, gemma, giichi, hagane, kotetsu, hagane kotetsu, haku, haruno, sakura, haruno sakura, hatake, kakashi, hatake kakashi, hokage, homura, hoshigaki, kisame, hoshigaki kisame, hyuga, hanabi, hyuga hanabi, hyuga, hiashi, hyuga hiashi, hyuga hinate, hyuga hizashi, hyuga neji, inari, inuzaka, kiba, inuzaka kiba, iruka, iwashi, jiraiya, kagari, kaiza, kamizuki, izumo, kamizuki izumo, kankuro, kazekage, kin, tsuchi, kin tsuchi, konohamaru, koharu, komaru, kyubi, midare, mitarashi, anko, mitarashi anko, mizukage, mizuki, momochi, zabuza, momochi zabuza, morino, ibiki, morino ibiki, mubi, nara, shikamaru, nara shikamaru, nara shikato, oboro, orochimaru, pakku, pochi, raido, rock, lee, rock lee, sarutobi, asuma, sarutobi asuma, sigure, sinobi, gashir, sinobi gashir, shizimi, suzume, tazuna, temari, tenten, ten ten, tonbo, tora, tsuchikage, tsunade, tsunami, tsurugu, misumi, tsurugu misumi, uchiha, itachi, uchiha atachi, sasuke, uchiha sasuke, itachi, uchiha itachi, uzumaki, uzumaki naruto, waraji, yakushi, yakushi kabuto, kabuto, yamanaka, ino, yamanaka ino, inoshi, yamanaka inoshi, yashamaru, yuuhi kurenai, kurenai, yuuhi, zaku, abumi, zaku abumi, zouri, episodes, full episodes, download, multimedia, screencap, screencaps, screen captures, sound, sound clip, video, video clip, summary, episode summary, summaries, episode summaries, anime, pictures, images, picture, image, message board, forum, chat, buddy icon, buddy, icon, manga, downloads, review, reviews, download, wallpaper, music, characters, character, bio, character bio, chakra, sharingan" />
		<meta name="rating" content="general" />
		<meta name="language" content="english" />
		<meta name="description" content="Bleach 7 - The First Source for Bleach Information, Media, News and Fan Interaction." />
		<meta name="warning" content="All HTML, Javascript, and any other script used on this site is strictly for Bleach7.com only. All information on this web site is solely for Bleach7.com unless otherwise stated. Bleach7.com is strictly a fan site where by no infringement is intended. Any reproduction of this sites content, pages, HTML e.t.c will be dealt with accordingly. 2004-2006 Bleach 7(bleach7.com)" />
		<meta name="copyright" content="Bleach7.com" />
		<meta name="resource-type" content="document" />
		<meta name="distribution" content="global" />
		<meta name="robots" content="all,index,follow" />
		<meta name="revisit-after" content="1 day" />
		
		<title><xsl:value-of select="@title"/></title>
		<link href="layout_5/css/style.css" media="screen, projection" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="alternate" type="application/rss+xml" title="Bleach7 RSS Feed" href="http://www.bleach7.com/rss.xml" />
		<script src="/layout_5/js/index.js" type="text/javascript"></script>
	</head>
	<body onload="MM_preloadImages('/images/index_62-over.jpg'); externalLinks()">
		<div id="layout">
			<div id="i02">
				<div id="shockwave">
					<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="312" height="60" title="Top Navigation">
                    <param name="movie" value="layout_5/images/topnav_buttons.swf" />
                    <param name="quality" value="high" />
                    <embed src="layout_5/images/topnav_buttons.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="312" height="60"></embed>
			      </object>
				</div>
				<div id="divi03"><img src="layout_5/images/index_03.jpg" alt="i03" id="i03" /></div>
			</div>
			<div id="donation_top">
				<div id="divi05"><img src="layout_5/images/index_05.jpg" alt="" id="i05" /></div>
				<div id="divi06"><img src="layout_5/images/index_06.jpg" alt="" id="i06" /></div>
			</div>
			<div id="donate">
				<div id="divi08"><img src="layout_5/images/index_08.jpg" alt="" id="i08" /></div>
				<div id="divi09"><a href="https://www.paypal.com/xclick/business=donate@bleach7.com&amp;item_name=Bleach7.com&amp;no_note=1&amp;tax=0&amp;currency_code=USD" rel="external"><img src="layout_5/images/index_09.jpg" alt="Donation" id="i09" /></a></div>
				<div id="divi10"><img src="layout_5/images/index_10.jpg" alt="" id="i10" /></div>
				<div id="divi11"><img src="layout_5/images/index_11.jpg" alt="" id="i11" /></div>
			</div>
			<div id="donation">
				<div id="divi12"><img src="layout_5/images/index_12.jpg" alt="" id="i12" /></div>
				<div id="donate_main_bg">
					<div id="divi13"><img src="layout_5/images/index_13.jpg" alt="" id="i13" /></div>
					<div id="divi17"><img src="layout_5/images/index_17.jpg" alt="" id="i17" /></div>
					<div id="divi18"><img src="layout_5/images/index_18.jpg" alt="" id="i18" /></div>
				</div>
				<div id="donate_list">
					<div id="divi27"><img src="layout_5/images/index_27.jpg" alt="" id="i27" /></div>
					<div id="divi28"><img src="layout_5/images/index_28.jpg" alt="" id="i28" /></div>
					<div id="divi29"><img src="layout_5/images/index_29.jpg" alt="" id="i29" /></div>
					<div id="divi32"><a href="http://www.bleach7.com"><img src="layout_5/images/index_32.jpg" alt="http://www.bleach7.com" id="i32" /></a></div>
					<div id="divi33"><img src="layout_5/images/index_33.jpg" alt="" id="i33" /></div>
				</div>
				<div id="donate_main">
			    	<div id="donate_cur">$1,000,300.00</div>
			       	<div id="donate_goal">$1,000,400.00</div>
			       	<div id="donate_amo">$1,000,100.00</div>
				</div>
			</div>
			<div id="member">
				<div id="member_top">
					<div id="divi14"><img src="layout_5/images/index_14.jpg" alt="" id="i14" /></div>
					<div id="divi15"><img src="layout_5/images/index_15.jpg" alt="" id="i15" /></div>
				</div>
				<div id="member_login">
					<form action="member/login.php" method="post">
				    	<div id="display_username"><img src="layout_5/images/index_19.jpg" alt="" id="i19" /></div>
						<div id="input_username"><input name="username" type="text" id="username" size="20" /></div>
						<div id="display_password"><img src="layout_5/images/index_21.jpg" alt="" id="i21" /></div>
						<div id="input_password"><input name="password" type="password" id="password" size="20" /></div>
						<div id="login_spacer"><img src="layout_5/images/index_23.jpg" alt="" id="i23" /></div>
						<div id="input_login"><input type="submit" id="submit" name="login_submit" value="" /></div>
						<div id="input_cookie"><input type="checkbox" name="cookieuser" checked="checked" /></div>
						<div id="login_end"><img src="layout_5/images/index_25.jpg" alt="" id="i25" /></div>
					</form>
				</div> 
				<div id="member_bar">
					<div id="divi31"><img src="layout_5/images/index_31.jpg" alt="" id="i31" /></div>
					<div id="divi30"><img src="layout_5/images/index_30.jpg" alt="" id="i30" /></div>
				</div>
			</div>
			<div id="top_nav">
				<div id="divi34"><img src="layout_5/images/index_34.jpg" alt="General Information" id="i34" /></div>
				<div id="divi35"><img src="layout_5/images/index_35.jpg" alt="" id="i35" /></div>
				<div id="divi36"><img src="layout_5/images/index_36.jpg" alt="Information" id="i36" /></div>
				<div id="divi37"><img src="layout_5/images/index_37.jpg" alt="" id="i37" /></div>
				<div id="divi38"><img src="layout_5/images/index_38.jpg" alt="Media" id="i38" /></div>
				<div id="divi39"><img src="layout_5/images/index_39.jpg" alt="" id="i39" /></div>
				<div id="divi40"><img src="layout_5/images/index_40.jpg" alt="Member Interaction" id="i40" /></div>
				<div id="divi41"><img src="layout_5/images/index_41.jpg" alt="" id="i41" /></div>
				<div id="divi42"><img src="layout_5/images/index_42.jpg" alt="Help Section" id="i42" /></div>
				<div id="divi43"><img src="layout_5/images/index_43.jpg" alt="" id="i43" /></div>
				<div id="divi44"><img src="layout_5/images/index_44.jpg" alt="Links" id="i44" /></div>
				<div id="divi45"><img src="layout_5/images/index_45.jpg" alt="" id="i45" /></div>
			</div>
			<div id="middle_bar">
				<div id="divi47"><img src="layout_5/images/index_47.jpg" alt="" id="i47" /></div>
				<div id="divi46"><img src="layout_5/images/index_46.jpg" alt="" id="i46" /></div>
			</div>
			<div id="main_release">
				<div id="latest_release">
					<div id="divi49"><img src="layout_5/images/index_49.jpg" alt="" id="i49" /></div>
					<div id="lr_bg">
						<div id="divi50"><img src="layout_5/images/index_50.jpg" alt="" id="i50" /></div>
						<div id="divi57"><img src="layout_5/images/index_57.jpg" alt="" id="i57" /></div>
						<div id="divi58"><img src="layout_5/images/index_58.jpg" alt="" id="i58" /></div>
						<div id="divi59"><img src="layout_5/images/index_59.jpg" alt="" id="i59" /></div>
						<div id="divi60"><img src="layout_5/images/index_60.jpg" alt="" id="i60" /></div>
						<div id="divi61"><img src="layout_5/images/index_61.jpg" alt="" id="i61" /></div>
						<div id="divi65"><img src="layout_5/images/index_65.jpg" alt="" id="i65" /></div>
						<div id="divi67"><img src="layout_5/images/index_67.jpg" alt="" id="i67" /></div>
					</div>
					<div id="lr_main">
						<div id="lr_episode_raw">Episode 118</div>
						<div id="lr_episode_sub">Episode 118</div>
						<div id="lr_manga_raw">Manga 256</div>
						<div id="lr_manga_sub">Manga 256</div>
					</div>
				</div>
				<div id="top_banner_bar"><img src="layout_5/images/index_51.jpg" alt="" id="i51" /></div>
				<div id="banner_rotation">
					<div id="divi52"><img src="layout_5/images/index_52.jpg" alt="" id="i52" /></div>
					<div id="divi55"><img src="layout_5/images/index_55.jpg" alt="" id="i55" /></div>
					<div id="br_main">&nbsp;</div>
					<div id="divi64"><img src="layout_5/images/index_64.jpg" alt="" id="i64" /></div>
				</div>
				<div id="bleach_forum">
					<div id="divi53"><img src="layout_5/images/index_53.jpg" alt="" id="i53" /></div>
					<div id="divi54"><a href="http://www.bleachforums.com" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('i62','','images/index_62-over.jpg',1)"><img src="layout_5/images/index_54.jpg" alt="Bleach Forums" id="i54" /></a></div>
					<div id="divi62"><a href="http://www.bleachforums.com" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('i62','','images/index_62-over.jpg',1)"><img src="layout_5/images/index_62.jpg" alt="Bleach Forums" id="i62" /></a></div>
					<div id="divi66"><img src="layout_5/images/index_66.jpg" alt="" id="i66" /></div>
					<div id="divi63"><img src="layout_5/images/index_63.jpg" alt="" id="i63" /></div>
				</div>
			</div>
			<div id="main_div">
				<div id="divi68"><img src="layout_5/images/index_68.jpg" alt="" id="i68" /></div>
				<div id="main_sec">
					<div id="main_top_spacer"></div>
						<table id="main_table" cellspacing="0" cellpadding="0">
							<tr>
								<td id="main_left_bar"><img src="layout_5/images/index_70.jpg" alt="" id="i70" /></td>
								<td id="main_section">
									<div id="divi71"><img src="layout_5/images/index_71.jpg" alt="" id="i71" /></div>
									<div id="main_text">
			  <p>Testing</p>
			  <p>jfkla;dsjflk;asdjklfjasdl;kjfl;kasdjfl;kasjfdsakljflka;sdjfl;kasdjfk;ljsadl;kjfk;ladsjfl;kja</p>
			  <p>fjsdaklfja</p>
			  <p>fjkdslajfal;</p>
			  <p>fjklasdjfal;</p>
			  <p>jfsadlkf</p>
			  <p>fjaskd</p>
			  <p>afaqfsad</p>
			  <p>bdaklbvasd</p>
			  <p>bsdfbsdfs</p>
			  <p>bagasdfbga</p>
			  <p>vasdfewadf</p>
			  <p>ewrasdfgv</p>
			  <p>kabjakdsg</p>
			  <p>agjdksaljfa</p>
			  <p>aksdjfklads;jfa</p>
			  <p>fjsdkalfjlkads;f</p>
			  <p>fjsadklfjaklsd;f</p>
			  <p>jfkslda;fjlka;sdf</p>
			  <p>kdasjfkasld;jf;a</p>
			  <p>fjdsakl;fjlads;kjfa</p>
			  <p>fjsdaklfjlaksd;jfl;ka</p>
			  <p>fjksldajflkas;djflk;as</p>
			  <p>fjsadklfjakls;djf;lkas</p>
			  <p>fjasdkljflkadsjf;lka</p>
			  <p>sadfjkasld;jfl;ka</p>
			  <p>sdajklfjasd;lkfja</p>
			  <p>aksdfjalks;jf;lkas</p>
			  <p>akdsfjakl;sdjf;lkasjdf</p>
			  <p>akdsjfklasdjflkja</p>
			  <p>dskjfkla;sdjfl;kajsd;lka</p>
			  <p>fasdjkfl;asjdklfja;</p>
			  <p>sdajfklasdj;fas</p>
			  <p>jdfsakl;fj;lsadkf</p>
			  <p>asjdfkl;asdjl;kf</p>
			  <p>ajsdkfljlkjdsa</p>
			  <p>jasdklfjsadkl;fjka</p>
			  <p>asdjfklasdjlkfjsa</p>
			  <p>ajsdkfljalksdjfl;ka</p>
			  <p>fjasdklfjlksadjflksa;d</p>
			  <p>sjdafkla;jflk;asd</p>
			  <p>djasgvklsjfbklas</p>
			  <p>dgjsadkljgdask;lf</p>
			  <p>adsjfkladsjflas</p>
			  <p>dfjkadsljflasd</p>
			  <p>]dgjsadg</p>
			  <p>adsg</p>
			  <p>asdg</p>
			  <p>ads</p>
			  <p>gsad</p>
			  <p>dg</p>
			  <p>asdgh</p>
			  <p>asd</p>
			  <p>dgsda</p>
			  <p>g</p>
			  <p>sadg</p>
			  <p>asd</p>
			  <p>dg</p>
			  <p>sadg</p>
									</div></td>
								<td id="nav_part">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2">
											<div id="divi72"><img src="layout_5/images/index_72.jpg" alt="" id="i72" /></div>
											</td>
									</tr>
									<tr>
										<td id="main_right_bar"><img src="layout_5/images/index_73.jpg" alt="" id="i73" /></td>
										<td id="main_navigation_section">
											<div id="divi74"><img src="layout_5/images/index_74.jpg" alt="Main Navigation" id="i74" /></div>
											<div id="main_navigation" class="OnWhite">
												<ul class="ulin">
													<li><a href="http://www.bleach7.com">FrontPage</a></li>
													<li><a href="?page=contact">Contact Us</a></li>
													<li><a href="http://www.bleachforums.com"  rel="external">BleachForums.com</a></li>
													<li><a href="?page=linkus">Link To Bleach7.com</a></li>
													<li><a href="?page=supportus">Support Bleach7.com</a></li>
													<li><a href="?page=rotation">Banner Rotation</a></li>
													<li><a href="?page=member/register">Site Registration</a></li>
												</ul>
											</div>
											<div id="divi76"><img src="layout_5/images/index_76.jpg" alt="Bleach Information" id="i76" /></div>
											<div id="bleach_information" class="OnWhite2">
												<ul class="ulout">
													<li><a href="?page=information/authorkubotite">Author: Kubo Tite</a></li>
													<li><a href="?page=information/bios/main">Biographies</a></li>
													<li><a href="?page=information/bleachanimeguide">Bleach Anime Guide</a></li>
													<li><a href="?page=information/bleachcaptainsguide">Bleach Zanpaktou Guide</a></li>
													<li><a href="?page=information/encyclopedia">Bleach Encyclopedia</a></li>
													<li><a href="?page=information/summaries/anime">Bleach Episode Guide</a></li>
													<li><a href="?page=information/factoids">Bleach Factoids</a></li>
													<li><a href="?page=information/bleachgameguide">Bleach Game Guide</a></li>
													<li><a href="?page=information/bleachitemguide">Bleach Item Guide</a></li>
													<li><a href="?page=information/locations">Bleach Locations</a></li>
													<li><a href="?page=information/bleachmangaguide">Bleach Manga Guide</a></li>
													<li><a href="?page=information/movie">Bleach Movie Guide</a></li>
													<li><a href="?page=information/bleachspellguide">Bleach Spell Guide</a></li>
													<li><a href="?page=information/weapons">Bleach Weapon Guide</a></li>
													<li><a href="?page=information/miscellaneous">Miscellaneous</a></li>
													<li><a href="?page=information/tvtokyo">TV Tokyo Information</a></li>
												</ul>
											</div>
											<div id="divi78"><img src="layout_5/images/index_78.jpg" alt="Bleach Multimedia" id="i78" /></div>
											<div id="bleach_multimedia" class="OnBlack">
												<ul class="ulin">
													<li><a href="http://gallery.bleach7.com">Bleach Image Gallery</a></li>
													<li><a href="?page=media/raw">Bleach RAW Manga</a></li>
													<li><a href="?page=media/manga">Bleach Manga Downloads</a></li>
													<li><a href="?page=media/music">Bleach Music Downloads</a></li>
													<li><a href="http://gallery.bleach7.com/main.php?g2_itemId=23">Bleach Wallpapers</a></li>
												</ul>
											</div>
											<div id="divi80"><img src="layout_5/images/index_80.jpg" alt="Fan Interaction" id="i80" /></div>
											<div id="fan_interaction" class="OnWhite">
												<ul class="ulout">
													<li><a href="irc://bleach7@irc.irchighway.net/">Bleach7 IRC Room</a></li>
													<li><a href="?page=">Chat Room</a></li>
													<li><a href="http://gallery.bleach7.com/main.php?g2_itemId=125">Fan Art</a></li>
													<li><a href="?page=fan/fanfiction">Fan Fictions</a></li>
													<li><a href="?page=stb">Spread the Bleach!</a></li>
												</ul>
											</div>
											<div id="divi83"><img src="layout_5/images/index_83.jpg" alt="Help Section" id="i83" /></div>
											<div id="help_section" class="OnWhite2">
												<ul class="ulin">
													<li><a href="?page=about" class="OnWhite2">About Bleach7.com</a></li>
													<li><a href="?page=faq/faqcentral">FAQ Central</a></li>
													<li><a href="?page=member/editorial">From Our Staff</a></li>
													<li><a href="?page=legaldisclaimer">Legal Disclaimer</a></li>
													<li><a href="?page=privacypolicy">Privacy Policy</a></li>
													<li><a href="?page=statistics">Statistics</a></li>
													<li><a href="?page=termsofuse">Terms of Use</a></li>
												</ul>
											</div>
											<div id="divi85"><img src="layout_5/images/index_85.jpg" alt="Good Buddies" id="i85" /></div>
											<div id="good_buddies" class="OnWhite3">
												<ul class="ulout">
													<li><a href="http://www.anime-divx.com/" rel="external">Anime-Divx</a></li>
													<li><a href="http://www.animestocks.com/" rel="external">Anime Stocks</a></li>
													<li><a href="http://www.combovideos.com/" rel="external">Combo Videos</a></li>
													<li><a href="http://www.cosmocanyon.com/" rel="external">Cosmo Canyon</a></li>
													<li><a href="http://endless-anime.com/" rel="external">Endless-Anime</a></li>
													<li><a href="http://www.maximum7.com/" rel="external">Maximum7</a></li>
													<li><a href="http://www.moonbeanmanga.net" rel="external">Moonbean Manga</a></li>
													<li><a href="http://www.naruto-anime.net" rel="external">Naruto-Anime</a></li>
													<li><a href="http://www.naruto-bunshin.com" rel="external">Naruto Bunshin</a></li>
													<li><i><a href="?page=goodbuddies">COMPLETE LIST</a></i></li>
												</ul>
											</div>
											<div id="divi87"><img src="layout_5/images/index_87.jpg" alt="Bleach Fansites" id="i87" /></div>
											<div id="bleach_fansites" class="OnWhite4" >
												<ul class="ulin">
													<li><a href="http://www.bleach8.com" rel="external">Bleach8 (Chinese)</a></li>
												<li><a href="http://www.bleachcommunity.net" rel="external">Bleach Community</a></li>
												<li><a href="http://www.bleachdj.com" rel="external">Bleach DJ</a></li>
												<li><a href="http://www.code-master.net/bleach/" rel="external">Bleach Info</a></li>
												<li><a href="http://www.bleachportal.net" rel="external">Bleach Portal</a></li>
												<li><a href="http://www.bleach-society.com/" rel="external">Bleach Society</a></li>
												<li><a href="http://www.bleachtv.com/" rel="external">Bleach TV</a></li>
												<li><a href="http://bleachx.com/" rel="external">Bleach X</a></li>
												<li><a href="http://bleach.animewtf.com" rel="external">Bleach WTF</a></li>
												<li><a href="http://www.shinigamicentral.net/" rel="external">Shinigami Central</a></li>
												<li><a href="http://www.livejournal.com/community/soul_society/" rel="external">Soul_Society (LJournal)</a></li>
												</ul>

											</div>
											<div id="divi89"><img src="layout_5/images/index_89.jpg" alt="Member Stats" id="i89" /></div>
											<div id="member_stats">
												<ul class="ulstat">
													<li>Admins Online: <a href="?page=member/online"><b>0</b></a></li>
													<li>Members Online: <a href="?page=member/online"><b>66</b></a></li>
													<li>Guests Online: <a href="?page=member/online"><b>95</b></a></li>
													<li>Total Users Online: <a href="?page=member/online"><b>161</b></a></li>
													<li>Total Members: <a href="?page=member/memberlist"><b>353924</b></a></li>
												</ul>
											</div>
											<div id="divi91"><img src="layout_5/images/index_91.jpg" alt="The End" id="i91" /></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<div id="divi93a"><img src="layout_5/images/index_93-a.jpg" alt="" id="i93a" /></div>
								<div id="divi93b"><img src="layout_5/images/index_93-b.jpg" alt="" id="i93b" /></div>
								<div id="divi93c"><img src="layout_5/images/index_93-c.jpg" alt="" id="i93c" /></div>
								<div id="divi93d"><img src="layout_5/images/index_93-d.jpg" alt="" id="i93d" /></div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="footer">
				<div id="divi96"><img src="layout_5/images/index_96.jpg" alt="" id="i96" /></div>
				<div id="divi97"><img src="layout_5/images/index_97.jpg" alt="" id="i97" /></div>
				<div id="divi98"><img src="layout_5/images/index_98.jpg" alt="" id="i98" /></div>
				<div id="divi99"><img src="layout_5/images/index_99.jpg" alt="" id="i99" /></div>
			</div>
		</div>
	</body>
</html>
</xsl:template>
</xsl:stylesheet>