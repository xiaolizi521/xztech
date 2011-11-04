<?PHP

$chaptersLate5 = mysql_query ( "SELECT `id` FROM manga_chapters WHERE stage > 0 ORDER BY id DESC LIMIT 1" );
$chapterinfo = mysql_fetch_array($chaptersLate5);


echo $chapterinfo['id'];


?>