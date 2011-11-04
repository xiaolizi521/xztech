<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$result_referrals_limit = mysql_query ( "SELECT * FROM users ORDER BY referrals DESC, username ASC LIMIT 5" );
while ( $referrals_limit = mysql_fetch_array ( $result_referrals_limit ) ) {
echo "<a href='#viewmember' onclick='ViewMember(\"$referrals_limit[username]\")'>$referrals_limit[username]</a> - $referrals_limit[referrals]<br>";
}
echo "<a href='$site_url/$main_filename?page=referrals'>Complete List</a>";
?>