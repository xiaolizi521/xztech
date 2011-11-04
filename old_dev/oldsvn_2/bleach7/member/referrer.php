<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

session_start();

// URL To Redirect To
$redirect_url = 'http://www.bleach7.com/?page=member/register&id='.$_GET['id'];


/*// Database Username
$db_username = "bleach7";


// Database Password
$db_password = "funwithbleach.";


// Database Name
$db_database = "bleach7_b7";



// ****** DO NOT EDIT PAST THIS POINT ******

// Hey, I'm UnholyGodn, I comemnted out this code because it stopped the script working with most 
// browsers and message clients, if there was an extreme reason you need this then uncomment, but it 
// will stop the AIM link working and such..

//if ( !isset ( $GLOBALS[HTTP_REFERER] ) ) {
//echo "<font face='Times New Roman'><h1>Error</h1><p> Invalid Referral URL.
//<p><a href='#' onclick='javascript: history.back(1)'>Click here</a> to go back to where you came from.</font>"; 
//exit();
//} 

$dbh = mysql_connect ( "localhost", "$db_username", "$db_password" ) or die ( 'Cannot connect to the database because: ' . mysql_error() );
mysql_select_db ( "$db_database" );*/
$result = mysql_query ( 'SELECT `username` FROM `users` WHERE `username` = \'' . $_GET['id'] . '\'' );

if ( mysql_num_rows ( $result ) <= 0 ) {
	echo '<span style="font-family: Times New Roman, Times, serif;"><h1>Error</h1><br />
<br />
Invalid Referrer Username.<br />
<br />
<a href="#" onclick="javascript: history.back(1)">Click here</a> to go back to where you came from.</span>'; 
	exit();
}
else {
	$_SESSION['referrer_id'] = $_GET['id'];
	header( 'Location: ' . $redirect_url );
	exit();
}
?>