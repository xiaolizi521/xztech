<?php

	echo "<form method='post' name='form_index'>";
	echo "<table style='width: 60%'>
	<tr>
		<td style='text-align: center;'><a href='index.php?view=main&amp;type=index&amp;action=release' target='main'>Edit New Releases</a></td>";
	if ( "98" <= $user_info['type'] ) {
		echo "<td style='text-align: center;'><a href='index.php?view=main&amp;type=index&amp;action=donations' target='main'>Edit Donation Info</a></td>
		<td style='text-align: center;'><a href='index.php?view=main&amp;type=index&amp;action=donator' target='main'>This Months Donator List</a></td>";
	}
	echo "</tr>
</table>
";
	
	if ( isset ( $action ) && !empty ( $action ) ) { //an action is set
		if ( $action == "release" ) { // Edit the new release information
			include ( "admin_index_release.php" );
		} elseif ( $action == "donations" ) { //edit donations information
			include ( "admin_index_donations.php" );
		} elseif ( $action == "donator" ) { //view donations list
			include ( "admin_index_donator_view.php" );
		} elseif ( $action == "add" ) { //add donations list
			include ( "admin_index_donator_add.php" );
		} elseif ( $action == "edit" ) { //edit donations list
			include ( "admin_index_donator_edit.php" );
		} elseif ( $action == "delete" ) { //delete donations list
			include ( "admin_index_donator_delete.php" );
		} 
	}
	echo "

</form>";
?>
