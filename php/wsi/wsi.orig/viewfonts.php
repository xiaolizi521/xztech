<?
include "config.php";
include "designtop.php";
?>
<h2>Viewing All Fonts</h2>
<? $fonts = mysql_query("SELECT * FROM `fonts` ORDER BY `name` ASC");
while ($font = mysql_fetch_assoc($fonts)) {
	if (file_exists("fonts/images/$font[name].png")) {
		$x++;
		if ($x % 2 == 1) {
			$float = "left";
		}
		else {
			$float = "right";
		}

		echo "<div style='float:$float'><b>$font[name]</b><br />
		<img src='fonts/images/$font[name].png' /><br /></div>";
	}
}
?>

<?
include "menu.php";
include "designbottom.php";
?>