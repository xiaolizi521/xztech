<?php

    include "header.php";

    // Minor Inventory Script

    if (!$_POST['submit']):

        // If the form isn't posted˝

        echo "<form method='post' action='addhost.php'>";
        echo "Hostname: <input type='text' size='16' name='host' /><br />";
        echo "Primary IP Address: <input type='text' size='16' name='ip' /><br />";
        echo "MAC Address: <input type='text' size='16' name='mac' /><br />";
        echo "<input type='submit' name='submit' value='submit' /><br />";
        echo "</form>";

    else:

        // If the form is posted

		$query = "insert into hosts (`host`,`ip`,`mac`) VALUES ('";
		$query .= $_POST['host']."','";
		$query .= $_POST['ip']."','";
		$query .= $_POST['mac']."')";

		$result = $db->query($query);

		echo $query;

		echo "We have inserted a new host with the following Host ID: " . $db->insert_id;

    endif;

    include "footer.php";
?>
