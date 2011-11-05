<?php 

function padit( $numb, $nlen) {
	$str="".$numb ;
	while (strlen( $str ) < $nlen) {
		$str="0".$str;
	}
	return $str;
}

function tsDate( $str, $timestamp )
{
	$offset_hours = 1;
	
	$offset = 3600 * $offset_hours;
	return date( $str, $timestamp + $offset );
}

$now = 1241729183;
$start = 1241729153;

?>

<html>
	<body>
		<span id="idTime" style="color: #ffc7c7;"><strong><?= padit( floor( ( $now - $start ) / 3600 ) % 60, 2 ) ?>:<?= padit( ( floor( $now - $start ) / 60 ) % 60, 2 ) ?>:<?= padit( ( $now - $start ) % 60, 2 ) ?></strong></span>
	</body>
</html>