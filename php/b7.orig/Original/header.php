<?php 
	session_start();
	require_once ( "member/settings.php" );
	require_once ( "member/db.php" );
	require_once ( "member/functions.php" );

?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="Content-Language" content="en-GB en" />

		<meta name="keywords" content="bleach, bleach manga, bleach scanlations, bleach scans, bleach anime, tv tokyo, studio perriot, bleach online, bleach fan, bleach source, bleach world, bleach media, bleach central, bleach forums, bleachportal, bleach portal, bleachexile, bleach exile, bleach chapter, bleach episode, manga7, maximum7, maximum 7, manga, shonen, zanpaktou, Bleach Direct Downloads, Bleach Direct Chapters, download anime, bleach avi, bleachsubs, bleach games, bleach discussion, heat the soul, inoue, kon, renji, chad, ichigo, rukia, kurosaki, isshin, arrancar, bleach manga raw downloads, bleach images, bleach wallpapers, dattebayo, flomp rumbel, flomp-rumbel, urahara, yoruichi,  manga-rain, biographies, bleach fanfic, fanfic, fan fic, fanart, fan art, bleach fanart, bleach fan art, music, bleach music, memories of nobody, diamond dust rebellion, diamonddust rebellion, bleach movie, bleach movies, bleach beat collection, bleach op, bleach anime downloads, bleach ed, bleach singles, bleach summary, club bleach, bleach ost, <?PHP 
$index = mysql_query ( "SELECT * FROM index_info" );
$chapters = mysql_fetch_array($index);
$chapternum = $chapters['manga_raw'];

$latest5 = $chapters['manga_raw'] -  5;
for($i = $chapternum; $i > $latest5; $i--)
{
echo "bleach $i, ";
}
$episodenum = $chapters['anime_raw'];

$latest5 = $chapters['anime_raw'] -  5;
for($i = $episodenum; $i > $latest5; $i--)
{
echo "bleach $i, ";
}
		
?>kubo, tite, kubo tite, anime, zombie powder" />
		
		<meta name="rating" content="General" />
		<meta name="Language" content="English" />
		<meta name="DESCRIPTION" content="Bleach 7 - Your source for the latest in all things Bleach. With the fastest and latest up to date news, anime, manga, media, and music with the best fan interaction around! Bleach 7 is all you need!" />
		<meta name="WARNING" content="All HTML, Javascript, and any other script used on this site is strictly for Bleach7.com only. All information on this web site is solely for Bleach7.com unless otherwise stated. Bleach7.com is strictly a fan site where by no infringement is intended. Any reproduction of this sites content, pages, HTML e.t.c will be dealt with accordingly. 2004-2006 Bleach 7(bleach7.com)" />
		<meta name="copyright" content="Bleach7.com" />

		<meta name="resource-type" content="document" />
		<meta name="distribution" content="Global" />

		<meta name="ROBOTS" content="ALL,INDEX,FOLLOW" />
		<meta name="REVISIT-AFTER" content="1 day" />

		<title>Bleach 7 - The First Source for Bleach Information, Manga, Media, News, and Fan Interaction!</title>

		<link rel="stylesheet" type="text/css" href="<?php require_once ( "style.php" ); ?>" />
		<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="alternate" type="application/rss+xml" title="Bleach7 RSS Feed" href="http://www.bleach7.com/rss.xml" />

		<script type="text/javascript">
			function mouseOver()
			{
				document.getElementById('i62').src ="./images/index_62-over.jpg";
			}
			function mouseOut()
			{
				document.getElementById('i62').src ="./images/index_62.jpg";
			}
		</script>
<STYLE type="text/css">
  arabic {text-align: center}
 </STYLE>
	</head>
	<body>
<?php
	require_once ( "member/header.php" );
?>