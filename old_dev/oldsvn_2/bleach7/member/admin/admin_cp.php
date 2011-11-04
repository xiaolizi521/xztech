<?php
ob_start();
include ( "header.php" );
include ( "../settings.php" );
include ( "../db.php" );
if ( !isset ( $_COOKIE['user_id'] ) && !isset ( $_COOKIE['password'] ) ) {
header ( "Location: $site_url/$main_filename?$ident=$script_folder/login" );
exit();
}

$rank99 = "Sensei";
$rank98 = "Webmaster";
$rank90 = "Administration";
$rank80 = "Staff Member";
$rank31 = "M7 Team | Mod";
$rank30 = "M7 Team";
$rank21 = "Info Team | Mod";
$rank20 = "Info Team";
$rank10 = "Moderator";
$rank2 = "Privileged Member";
$rank1 = "Member";

//Hack protection
if($user_info['user_id'] != $_COOKIE['user_id'] && $user_info['password'] != $_COOKIE['password'] && $user_info['type'] < 20)
{
echo 'Hacking attempt...';
exit;
}

include ( "../rank.php" );
include ( "../settings.php" );
include ( "../functions.php" );

//DB QUERY RUNS



//Table 
?>
<center>
<table border="1" width="80%">
 <tr>
  <td rowspan="2" width="20%"><?php include ( "admin_cp_nav.php" ); ?>
</td>
  <td>Area Title</td>
 </tr>
 <tr>
  <td>
  <?PHP
  
  echo 'STUFF GOES HERE STUFF GOES HERE STUFF GOES HERE STUFF GOES HERE STUFF GOES HERE ';
  
  ?>
  </td>
 </tr>

</table>
</center>
