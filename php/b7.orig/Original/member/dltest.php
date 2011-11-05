<?PHP

$chaptersLate5 = mysql_query ( "SELECT `id` FROM manga_chapters WHERE stage > 0 ORDER BY id DESC LIMIT 1" );

echo $chaptersLate5;

?>