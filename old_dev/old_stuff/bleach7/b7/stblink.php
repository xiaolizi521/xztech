<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info['user_id'] ) ) {
	echo 'You must be a registered member of Bleach7 to take part in &quot;Spread The Bleach&quot;.<br />
<a href="', $site_url, '/', $main_filename, '?page=register">Register</a> or <a href="', $site_url, '/login.php">Login</a>.';
}
else {
?>
<img src="./spread.php" alt="Spread The Bleach" /><br />
<br />
Copy The Code Below To Spread The Bleach!<br />
<br />
<b>HTML CODE</b><br />
<textarea rows="3" cols="98" class="textbox">&lt;a href="http://bleach7.com/?page=referrer&amp;id=<?php echo $user_info['username'] ?>"&gt;&lt;img src="http://www.bleach7.com/images/linkus5.jpg" alt="" /&gt;&lt;/a&gt;</textarea><br />
<br />
<b>BB CODE (FORUMS)</b><br />
<textarea rows="3" cols="98" class='textbox'>[URL=http://bleach7.com/?page=referrer&amp;id=<?php echo $user_info['username'] ?>][IMG]http://www.bleach7.com/images/linkus5.jpg[/IMG][/URL]</textarea><br />
<br />
<b>AIM LINK CODE</b><br />
<textarea rows="3" cols="98" class='textbox'>http://bleach7.com/?page=referrer&amp;id=<?php echo $user_info['username'] ?></textarea>
<?php
}
?>