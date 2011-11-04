<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$result_referrals_all = mysql_query ( "SELECT * FROM users ORDER BY referrals DESC, username ASC LIMIT 100" );
while ( $referrals_all = mysql_fetch_array ( $result_referrals_all ) ) {
	if ($referrals_all[referrals] > 0 ) {
		echo "<a href='#viewmember' onclick='ViewMember(\"$referrals_all[username]\")'>$referrals_all[username]</a> - $referrals_all[referrals]<br>";
	}
}
?>