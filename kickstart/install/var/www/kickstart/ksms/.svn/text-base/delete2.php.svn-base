<?php

	$conn = pg_connect('host=127.0.0.1 dbname=kickstart user=kickstart password=l33tNix');
	
	$id = $_POST['id'];

	$delete = "DELETE from vlans where id = '$id';";
  
	pg_query($conn,$delete);

  $conf = "/exports/kickstart/bin/vlan_restart";
  exec($conf);
	header( 'Location: deleted.html' ) ;
	
?>
