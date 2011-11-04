<?
include "config.php";
$q = mysql_query("SELECT `email` FROM `whatpulse`");
$r = mysql_num_rows($q);
while ($a = mysql_fetch_array($q)) {
if ($a['email']) {
$x++;
}
}
$p = $x / $r * 100;
echo "Only $x people out of the total of $r registered on this site have entered email addresses. This equates to $p% of the total.";
?>