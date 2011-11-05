<?php
	include( "code/config.php" );

	session_start();	

	header("Cache-Control: no-cache");
	
	$function = "";
	
	$f = '';
	$p1 = '';
	$p2 = '';
	$p3 = '';
	$p4 = '';
	$p5 = '';
		
	if (isset($_GET["f"])){
		$f = $_GET["f"];
	}	
	if (isset($_GET["p1"])){
		$p1 = $_GET["p1"];
	}	
	if (isset($_GET["p2"])){
		$p2 = $_GET["p2"];
	}	
	if (isset($_GET["p3"])){
		$p3 = $_GET["p3"];
	}	
	if (isset($_GET["p4"])){
		$p4 = $_GET["p4"];
	}	
	if (isset($_GET["p5"])){
		$p5 = $_GET["p5"];
	}		
	
	switch( $f ){
		case 'RATE_CLASS':			
			print 'Works';
			exit();

		default:
			break;
	}
	
	
?>