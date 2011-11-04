<?
$filename = "sigs.xml.gz";
$next =  filectime($filename) + 21600;
$now = date("U");
$till = $next - $now;
echo "<div class='navbar' align='center'>The stats file was last updated on " . date("F d Y H:i:s", filectime($filename));
echo "<br>The time is now " . date("F d Y H:i:s");
echo "<br>The next update is in " . round($till / 60 / 60,2) . " hours.</div>";
?>