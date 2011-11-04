<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

if ( !isset ( $user_info[user_id] ) ) {
//echo "Not logged in, <a href='$site_url/login.php'>Login here</a> or <a href='$site_url/index.php?page=register'>Register</a>";
include ( "$_SERVER[DOCUMENT_ROOT]/login_form2.php" );
} else {
echo "Welcome <b>$user_info[username]</b>!<br>";
if ( $user_info[type] == "2" || $user_info[type] == "3" ) {
echo "- <a href='$site_url/news/admin/index.php' target='_blank'> Manage News</a><br>";
} else {
echo "";
}
echo "
- <a href='#editprofile' onclick='EditProfile()'>Edit Profile</a><br>
- <a href='$site_url/$main_filename?page=pm_inbox'>PM Inbox ($pm_count_cp)</a><br>
- <a href='$site_url/$main_filename?page=memberlist'>Member list</a><br>
- <a href='$site_url/logout.php'>Log out</a>
";
}

echo "<p><b>Member Statistics</b><br>";
include ( "$_SERVER[DOCUMENT_ROOT]/online/online.php" );
?>
