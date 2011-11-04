<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$file_title = 'User Control Panel';
?>
<fieldset>
<table cellpadding="5" cellspacing="0" class='VerdanaSize1Main' style="width:100%; text-align: center">
	<tr>
		<td>
			<a href="<?php echo $site_path, '/usercp&amp;do=editprofile' ?>"><b>Edit Profile</b></a> | <a href="<?php echo $site_path, '/usercp&amp;do=editoptions' ?>"><b>Edit Options</b></a> | <a href="<?php echo $site_path, '/usercp&amp;do=editavatar' ?>"><b>Edit Avatar</b></a> | <a href="<?php echo $site_path, '/usercp&amp;do=editpassword' ?>"><b>Edit Password</b></a></td>
	</tr>
</table>
</fieldset>
<table cellpadding="0" cellspacing="0" style="height: 7px;">
	<tr>
		<td></td>
	</tr>
</table>
<?php
require_once ( $script_folder . '/profile.php' );
?>
