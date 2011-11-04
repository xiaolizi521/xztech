<?
include "config.php";
$q = mysql_query("SELECT * FROM `whatpulse`");
$r = mysql_num_rows($q);
while ($a = mysql_fetch_array($q)) {
if ($a[email]) {
$to      = $a[email];
$subject = 'Whatpulse Signature Images: ' . date("F j Y, g:i a");
$message = "Hello $a[user],\r\n
We at WSI are looking to expand our horizons. We're currently looking for new ideas for new services to provide. If you have an idea or a suggestion, you may reply to this email directly. Alternatively you can visit our forums at http://offbeat-zero.net/pulse/forum.

Also thanks to those of you who have added in email addresses, we are nearly at 60% of users with email addresses. Having an email address in our database allows you to get a news password sent to that email address if you forget your old one.

Awaiting your reply,
Radar
Whatpulse Signature Images


";
$headers = 'From: radar@frozenplague.net' . "\r\n" .
   'Reply-To: radar@frozenplague.net' . "\r\n" .
   'X-Mailer: WSI/';

mail($to, $subject, $message, $headers);
}
}
?>