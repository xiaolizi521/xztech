<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

include ( "$_SERVER[DOCUMENT_ROOT]/db.php" );
include ( "$_SERVER[DOCUMENT_ROOT]/header.php" );
$id = mysql_real_escape_string ( $_GET['id'] );
if ( isset ( $id ) ) {
$result = mysql_query ( "SELECT * FROM users WHERE username='$id'" );
$member = mysql_fetch_array ( $result );
}

function DisplayInfo( $header, $content ) {
global $member;

echo "<fieldset class='main'><legend class='main'><b>$header</b></legend>";
echo "- ";

if ( empty ( $member[$content] ) ) {
echo "Not Available";
} else {

switch ( $content ) {
case "registered_on":
$datetime = $member['registered_on']; 
$date = date ( 'F d, Y', strtotime( $datetime ) ); 
echo $date;
break;

case "website":
if ( ereg ( "www.|http://.", $member['website'] ) ) {
echo "<a href='$member[website]' target='_blank'>$member[website]</a>";
} else {
echo "$member[website]";
}
break;

default:
echo "$member[$content]";
} 
echo "</fieldset>";
}

}
?>
<html>
<head>
<title>Viewing Member Profile</title>
</head>

<body bgcolor="<?php echo $bgcolor ?>" background="<?php echo $bgbackground ?>" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" height="100%" cellpadding="7" cellspacing="0" class="main"><tr><td align="center" valign="middle">

<?php
if ( isset ( $id ) && !empty ( $id ) && mysql_num_rows ( $result ) > 0 && ( eregi ( "^[a-z0-9\-_\.]+$", $id ) ) ) {
?>

<table width="240" cellpadding="0" cellspacing="0"><tr><td>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td style="background-color: <?php echo $tableheadercolor ?>; padding: 3px" class="secondary"><b>General Information</b>
</td></tr>
<tr><td>
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="5" cellspacing="0">
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0" class="main"><tr><td align="center">
<?php
if ( empty ( $member['avatar'] ) ) {
echo "<div style='width: 50px; height: 50px; border: 1px solid #4A4D4F'></div>";
} else {
echo "<img src='$member[avatar]' width='50' height='50'>";
}
?>
</td></tr></table>
</td></tr>
<tr><td>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td><?php DisplayInfo( $header="Username", $content="username" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Website", $content="website" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="MSN Messenger", $content="msn" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="AOL Screen Name", $content="aol" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Registered On", $content="registered_on" ) ?></td></tr>
<tr><td><?php DisplayInfo( $header="Referrals", $content="referrals" ) ?></td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>

<table height="7"><tr><td></td></tr></table>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td style="background-color: <?php echo $tableheadercolor ?>; padding: 3px" class="secondary"><b>PM Information</b>
</td></tr>
<tr><td>
<table bgcolor="<?php echo $tablebgcolor ?>" width="100%" cellpadding="5" cellspacing="0">
<tr><td>
<fieldset class='main'><legend class='main'><b>Send PM</b></legend>- <a href="<?php echo "$site_url/index.php?page=pm_compose&to=$id" ?>" target="mainwindow">Click here to send this user a PM</a></fieldset>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>

</td></tr></table>
<?php
} else {
echo "There are no users in our database with that ID. Check the ID and try again";
} 
?>
</td></tr></table>