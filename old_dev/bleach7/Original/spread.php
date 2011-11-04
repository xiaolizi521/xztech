<?php
####################################################################
# AR Memberscript 				                                   #
# Created By: Thomas of Anime Reporter - http://animereporter.com  #
# Copyright Anime Reporter. All Rights Reserved.                   # 
# THIS IS A PAID SCRIPT AND MAY NOT BE REDISTRIBUTED TO OTHERS.    #
####################################################################

print_r(gd_info());
die;

// Title
$title = 'Spread the Bleach!';

// Start Date
$start_date = '10/1/2005';

// Image Name (include the path as well)
$imagename = 'spread.jpg';

// Font Type (include the path as well)
$font = './fonts/verdana.ttf'; 

// Font Size Title
$fontsize_title = 12;

// Font Size Title
$fontsize = 10;

// ****** DO NOT EDIT PAST THIS POINT ******

require_once ( './member/db.php' );
$result = mysql_query( 'SELECT count( `user_id` ) AS `total_members` , sum( `referrals` ) AS `referrals_sum` FROM `users`' );
$referrers_total = mysql_fetch_array ( $result );
$refer = $referrers_total['referrals_sum'] - 712;

$image = imagecreatefromjpeg( $imagename );
$imagesize = getimagesize( $imagename );
$imagewidth = $imagesize[0];		// 227
$imageheight = $imagesize[1];		// 133
$blank = imagecolorallocate( $image, 61, 61, 47 ); //4013359

imagettftext ( $image, 12, 0, 10, 108, 4013359, 'fonts/verdana.ttf', 'Spread the Bleach!' );
//imagettftext( $image, $fontsize_title, 0, $fontsize, ( $imageheight - ( $fontsize*2.5 ) ), $blank, $font, $title );
//imagettftext( $image, $fontsize, 0, $fontsize, ( $imageheight - $fontsize ), $blank, $font, $refer . ' Users Since ' . $start_date );
header( 'Content-type: image/jpeg' );
imagejpeg( $image );
imagedestroy( $image );
?>