<?php

echo $user_info['user_id'];
echo $user_info['username'];






function log_entry ($type2,$text2)
{
	$type=$type2;
	$text=$text2;
	$ip=$_SERVER['REMOTE_ADDR'];
	if ( isset ( $user_info['user_id'] ) ){
		$user=$user_info['user_id'] ;
		echo $user;
	}
	else{
		$user="-";
		echo $user;
	}
	$sql=mysql_query("INSERT INTO error_logs (type,message,date,source,user) VALUES ('$type','$text','" . date('H:i:s d/m/Y') . "','$ip','$user')")or die(mysql_error());

}
?>
