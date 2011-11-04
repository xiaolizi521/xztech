<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

$site_path = "?$ident=$script_folder";
$current_location = "$site_url$_SERVER[REQUEST_URI]";
$timenow = time();

if ( !ereg ( "/member.php", "$_SERVER[SCRIPT_NAME]" ) ) {
echo "<script type='text/javascript'>
	self.name='mainwindow'
	function new_window ( url ) {
		open ( url, \"_blank\" );
	}
</script>
";
}
?>