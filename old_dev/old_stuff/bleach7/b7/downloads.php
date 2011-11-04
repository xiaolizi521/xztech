<?php

$rand = rand(1, 100); //Random Number
if ( isset ( $type ) && !empty ( $type ) && isset ( $file ) && !empty ( $file ) ) {
	switch ( $type ) {
		case 'latest':
			if ( $file == 'Bleach_Ch253_M7.zip' ) {  // if set to 1/3 set this to 30, else set it to 50
				$download = 'http://www.neonmo.net/max7/download.php?file=' . $file;

			}
		/*	elseif ( $rand >= 30 ) { // if set to 1/3 set this to 70 else set it to 100
		//		$download = 'http://www.neonmo.net/max7/download.php?file=' . $file;
		//		$download = 'http://www.NeoMirror.net/max7/download.php?file=' . $file;
				$download = 'http://www.neomirror.net/max7/files/' . $file;
			} */
			else   {
				$download = 'http://www.bleach7.com/download/untitled.php';
			}
			break;
		case 'download':
			if (substr($_GET['file'], 1, 3) == 'raw') { $download = 'http://www.bleach7.com/?page=download/error'; break;}
			$chap_num = substr($_GET['file'], 9, 3);
			if ( $chap_num <= 200 ) { 
			if ( ($chap_num == '210') || ($chap_num == '216') || ($chap_num == '226') ) { 
				$download = 'http://www.NeoMirror.net/max7files/Bleach_Ch'.$chap_num.'_M7_LQ.zip'; break;}
			else { $download = 'http://www.NeoMirror.net/max7files/Bleach_Ch'.$chap_num.'_M7.zip'; break;}
			else { $download = 'http://www.bleach7.com/?page=download/error'; }
			break;
		case 'music':
				$download = 'http://www.bleach7.com/?page=download/error';
			break;
		case 'volume':
				$download = 'http://www.bleach7.com/?page=download/error';
			break;
		case 'international':
				$download = 'http://www.bleach7.com/?page=download/error';
			break;
		default:
			echo 'invalid type';
			break;
	}

	header ( 'Location: ' . $download );	
}
else {
	echo 'You need to have a download type and a file name.';
}
?>